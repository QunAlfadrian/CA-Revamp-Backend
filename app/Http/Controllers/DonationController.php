<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\ImageService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\MidtransService;
use App\Http\Resources\V1\DonationCollection;
use App\Models\Book;
use App\Models\RequestedSupply;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DonationController extends Controller {
    public function __construct(private ImageService $imageService) {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $user = auth('sanctum')->user();

        $query = Donation::query();

        // filter by user
        $query->where('donor_id', $user->id())->get();

        // filter type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // filter status
        if ($request->filled('status')) {
            $status = $request->input('status');

            $fundStatus = match ($status) {
                'success' => 'paid',
                'failed' => 'failed',
                'pending' => 'pending',
                default => 'pending'
            };
            $query->whereHas('fundsRelation', function ($fund) use ($fundStatus) {
                $fund->where('status', $fundStatus);
            });

            switch ($status) {
                case 'success':
                    $query->whereHas('donatedItemsRelation', function ($q) {
                        $q->where('status', 'received');
                    });
                    break;
                case 'failed':
                    $query->whereHas('donatedItemsRelation', function ($q) {
                        $q->where('status', 'not_received')
                            ->orWhere('status', 'cancelled')
                            ->orWhere('status', 'declined');
                    });
                    break;
                case 'pending':
                    $query->whereHas('donatedItemsRelation', function ($q) {
                        $q->where('status', 'pending_verification')
                            ->orWhere('status', 'on_delivery');
                    });
                    break;
                default:
                    break;
            }
        }

        // search
        if ($request->filled('search')) {
            $term = $request->input('search');

            $query->where(function ($q) use ($term) {
                $q->whereHas('campaignRelation', function ($q2) use ($term) {
                    $q2->where('title', 'like', "%$term%")
                        ->orWhereHas('organizerRelation', function ($q) use ($term) {
                            $q->where('name', 'like', "%$term%");
                        });
                });
            });
        }

        return new DonationCollection($query->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Campaign $campaign) {
        if (!($campaign->status() === 'on_progress' || $campaign->status() === 'finished')) {
            return response()->json([
                'message' => 'Donations are not allowed for this campaigns.',
                'errors' => [
                    'status' => 'The campaign is not accepting donations at this time',
                    'campaign_status' => $campaign->status()
                ]
            ], 403);
        }

        $rules = [];

        $user = auth('sanctum')->user();
        if (!$user) {
            $rules += [
                'username' => 'required|string|min:5|max:25',
            ];
        }
        $request->validate($rules);

        if ($campaign->type() === 'fundraiser') {
            return app(FundraiserController::class)->donate($request, $campaign);
        }

        if ($campaign->type() === 'product_donation') {
            $rules += [
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


            Log::info($request->input('books'));
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

                // update donated items instance quantity

                // // update requested book
                // if ($request->input('books')) {
                //     $books = $request->input('books');
                //     foreach ($books as $donatedBook) {
                //         $isbn = $donatedBook['isbn'];
                //         $quantity = $donatedBook['quantity'];

                //         $book = Book::find($isbn);
                //         $campaign->donateBook($book, $quantity);
                //     }
                // }

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
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        //
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

    public function finish(Request $request, Campaign $campaign) {
    }

    public function error(Request $request, Campaign $campaign) {
    }
}
