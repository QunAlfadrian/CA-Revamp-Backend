<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Book;
use App\Models\Role;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\DonatedItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RequestedSupply;
use Illuminate\Support\Facades\DB;
use App\Http\Services\ImageService;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\V1\DonatedItemCollection;
use App\Http\Resources\V1\DonatedItemResource;
use App\Http\Resources\V1\DonatedItemSummaryCollection;
use App\Models\RequestedBook;

class DonatedItemController extends Controller {
    public function __construct(private ImageService $imageService) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index() {
        //
    }

    /**
     * Display a listing of the resource by campaign
     */
    public function indexByCampaign(Request $request, Campaign $campaign) {
        $query = DonatedItem::query();

        $query->whereHas('campaignRelation', function ($q) use ($campaign) {
            $q->where('campaign_id', $campaign->id());
        });

        if ($request->filled("status")) {
            $query->where('status', $request->input('status'));
        }

        return new DonatedItemSummaryCollection(($query->orderBy('updated_at', 'desc')->paginate(10)));
    }

    /**
     * Display a listing of the resource by donations
     */
    public function indexByDonation(Request $request, Donation $donation) {
        $query = DonatedItem::query();

        $query->whereHas('donationRelation', function ($q) use ($donation) {
            $q->where('donation_id', $donation->id());
        });

        return new DonatedItemCollection($query->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Campaign $campaign) {
        $user = auth('sanctum')->user();

        $rules = [
            'books' => 'nullable|array',
            'books.*.isbn' => 'string|min:10|max:13|exists:books,isbn',
            'books.*.quantity' => 'integer|min:1|max:99',

            'supplies' => 'nullable|array',
            'supplies.*.id' => 'string|max:25',
            'supplies.*.quantity' => 'int|min:1|max:10',

            'package_picture' => 'required|image|max:5048',
            'delivery_service' => 'required|string|in:pos_indonesia,jne,sicepat,anteraja,lion_parcel,spx_express,dhl',
            'resi' => 'required|string|max:25'
        ];

        $request->validate($rules);

        DB::beginTransaction();
        try {
            // get or create donation instance
            $username = $user ? $user->name() : $request->input('username');
            if ($user) {
                $donation = $campaign->donationsRelation()
                    ->whereHas('donorRelation', function ($q) use ($username) {
                        $q->where('name', $username);
                    })->where('type', 'item')
                    ->first();
            } else {
                $donation = $campaign->donationsRelation()
                    ->where('donor_name', $username)
                    ->where('type', 'item')
                    ->first();
            }

            if (!$donation) {
                $donation = $campaign->donationsRelation()->create([
                    'donor_name' => $username,
                    'type' => 'item',
                ]);
            }

            // attach user to donation
            if ($user) {
                $donation->donatedBy($user);
            }

            // create donated items instance
            $donatedItems = $donation->donatedItemsRelation()->create([
                'campaign_id' => $campaign->id(),
                'quantity' => 0,
                'delivery_service' => $request->input('delivery_service'),
                'resi' => $request->input('resi'),
            ]);

            // store package picture
            if ($request->hasFile('package_picture')) {
                $slug = $user
                    ? Str::slug($user->name())
                    : Str::slug($request->input('username'));
                $image = $request->file('package_picture');
                $filename = 'package-' . $slug . "-" . now()->format('YmdHis') . ".webp";
                $campaignId = $campaign->id();
                $path = "images/donations/products/$campaignId/";

                $this->imageService->storeImage(
                    $image,
                    $filename,
                    $path,
                    75,
                    true
                );
                $donatedItems->update([
                    'package_picture_url' => asset($path . $filename)
                ]);
            }

            // get or create donated book instances
            $books = $request->input('books');
            if ($books) {
                foreach ($books as $book) {
                    $bookInstance = Book::findOrFail($book['isbn']);
                    $donatedItems->attachBook($bookInstance, $book['quantity']);
                }
            }

            // create donated supplies instances
            $supplies = $request->input('supplies');
            if ($supplies) {
                foreach ($supplies as $supply) {
                    $requestedSupply = RequestedSupply::findOrFail($supply['id']);
                    $donatedItems->attachSupply($requestedSupply, $supply['quantity']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Please wait for verification',
                'data' => [
                    'redirect_url' => null
                ]
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json([
                "message" => $e->getMessage(),
                "error" => "Internal server error",
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DonatedItem $donatedItem) {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'error' => 'unauthorized',
                'message' => 'You do not have permission to access this resource'
            ], 403);
        }

        $hasDonor = $donatedItem->donation()->donor() ? true : false;
        $donation = $donatedItem->donation();
        $campaign = $donatedItem->campaign();
        if (!(
            $user->matches($campaign->organizer())
            || ($hasDonor && $user->matches($donation->donor()))
        )) {
            return response()->json([
                'error' => 'unauthorized',
                'message' => 'You do not have permission to access this resource'
            ], 403);
        }

        return (new DonatedItemResource($donatedItem))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * verify the resource
     */
    public function verify(Request $request, DonatedItem $donatedItem) {
        $user = auth()->user();

        $organizer = $donatedItem->campaign()->organizer();
        if (!$user || !$user->matches($organizer)) {
            return response()->json([
                'error' => 'unauthorized',
                'message' => 'You do not have permission to access this resource'
            ], 403);
        }

        $donatedItem->update([
            'status' => 'on_delivery'
        ]);
        $donatedItem->refresh();

        return (new DonatedItemResource($donatedItem))
            ->response()
            ->setStatusCode(200);
    }

    public function updateStatus(Request $request, DonatedItem $donatedItem) {
        $user = auth()->user();

        $organizer = $donatedItem->campaign()->organizer();
        if (!$user || !$user->matches($organizer)) {
            return response()->json([
                'error' => 'unauthorized',
                'message' => 'You do not have permission to access this resource'
            ], 403);
        }

        $request->validate([
            'status' => 'required|string|in:received,not_received,cancelled,declined',
        ]);

        $donatedItem->update([
            'status' => $request->input('status')
        ]);
        $donatedItem->refresh();

        if ($donatedItem->status() === 'received') {
            $campaign = $donatedItem->campaign();
            $campaign->update([
                'donated_item_quantity' => (int)$campaign->donatedItemQuantity() + (int)$donatedItem->quantity()
            ]);

            $donatedBooks = $donatedItem->books();
            foreach ($donatedBooks as $book) {
                $requested = RequestedBook::where(
                    'campaign_id',
                    $campaign->campaign_id
                )->where(
                    'book_id',
                    $book->isbn
                )->first();

                if ($requested) {
                    RequestedBook::where('campaign_id', $campaign->campaign_id)
                        ->where('book_id', $book->isbn)
                        ->update([
                            'donated_quantity' => (int) $requested->donatedQuantity() + (int) $book->pivot->quantity,
                        ]);
                }
            }

            $donatedSupplies = $donatedItem->requestedSupplies();
            foreach ($donatedSupplies as $supply) {
                $requested = RequestedSupply::where(
                    'campaign_id',
                    $campaign->id()
                )->where(
                    'requested_supply_id',
                    $supply->id()
                )->first();

                if ($requested) {
                    $requested->update([
                        'donated_quantity' => (int)$requested->donatedQuantity() + (int)$supply->pivot->quantity
                    ]);
                }
            }
        }

        return (new DonatedItemResource($donatedItem))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        //
    }
}
