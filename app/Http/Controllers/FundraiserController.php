<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Campaign;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\ImageService;
use App\Http\Services\MidtransService;
use App\Http\Services\PaginateService;
use App\Http\Resources\V1\CampaignResource;

class FundraiserController extends Controller {
    public function __construct(
        private ImageService $imageService,
        private PaginateService $paginateService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index() {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $rules = [
            'attributes.requested_fund_amount' => 'required|numeric|min:50000|max:9999999999'
        ];
        $request->validate($rules);

        // create campaign instance
        $user = auth()->user();
        $user->campaignsRelation()->create([
            'type' => $request->input('type'),
            'title' => $request->input('attributes.title'),
            'slug' => $request->filled('attributes.slug')
                ? $request->input('attributes.slug')
                : Str::slug($request->input('attributes.title')),
            'description' => $request->input('attributes.description'),
            'requested_fund_amount' => $request->input('attributes.requested_fund_amount'),
        ]);

        // store and update header image
        $slug = $request->filled('attributes.slug')
            ? $request->input('attributes.slug')
            : Str::slug($request->input('attributes.title'));
        $campaign = $user->campaignsRelation()
            ->where('slug', $slug)
            ->first();
        if ($request->hasFile('attributes.header_image')) {
            $slug = Str::slug($request->input('attributes.title'));
            $image = $request->file('attributes.header_image');
            $filename = 'header-' . $slug . "-" . now()->format('YmdHis') . ".webp";
            $path = 'images/campaigns/' . $slug . '/';

            $this->imageService->storeImage(
                $image,
                $filename,
                $path,
                75
            );

            $campaign->update([
                'header_image_url' => asset($path . $filename)
            ]);
            $campaign->refresh();
        }

        return response()->json([
            'success' => true,
            'message' => 'Campaign successfully created',
            'data' => new CampaignResource($campaign)
        ], 200);
    }

    public function donate(Request $request, Campaign $campaign) {
        $rules = [];
        $user = auth('sanctum')->user();

        $rules += [
            'fund_amount' => 'required|integer|min:5000|max:20000000'
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
                })->where('type', 'fund')
                ->first();
            } else {
                $donation = $campaign->donationsRelation()
                    ->where('donor_name', $username)
                    ->where('type', 'fund')
                    ->first();
            }

            if (!$donation) {
                $donation = $campaign->donationsRelation()->create([
                    'donor_name' => $username,
                    'type' => 'fund',
                ]);
            }

            // attach user to donation
            if ($user) {
                $donation->donatedBy($user);
            }

            // calculate service fee
            $serviceFeePercent = config('donation.service_fee_percent');
            $serviceFeeThreshold = config('donation.service_fee_threshold');
            $serviceFeeMax = config('donation.service_fee_max');
            $serviceFee = $request->input('fund_amount') > $serviceFeeThreshold
                ? $serviceFeeMax
                : $serviceFeePercent / 100 * $request->input('fund_amount');

            // create fund instance
            $orderId = $campaign->slug() . '-' . now()->timestamp;
            $fund = $donation->fundsRelation()->create([
                'campaign_id' => $campaign->id(),
                'donation_id' => $donation->id(),
                'order_id' => $orderId,
                'amount' => $request->input('fund_amount'),
                'service_fee' => $serviceFee
            ]);

            // create snap token request payload
            if ($user) {
                $fullName = $user->identity()
                    ? Str::of($user->identity()->fullName())->explode(' ')
                    : Str::of($user->name())->explode(' ');
            } else {
                $fullName = Str::of($username)->explode(' ');
            }

            $firstName = $fullName[0];
            $lastName = count($fullName) > 1
                ? $fullName[count($fullName) - 1]
                : null;
            $payload = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => floatval($fund->amount)
                ],
                'customer_details' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $user ? $user->email() : null
                ],
                'item_details' => [
                    [
                        'id' => $campaign->id(),
                        'price' => $fund->amount(),
                        'quantity' => 1,
                        'name' => $campaign->title(),
                        'type' => $campaign->type(),
                    ]
                ],
                'callbacks' => [
                    'finish' => request()->header('X-Previous-URL') ?? url('/'),
                ]
            ];

            // Send request to midtrans
            $service = new MidtransService();
            $response = $service->snap($payload);

            // Retrieve snap token
            $snapToken = $response['token'];
            $url = $response['redirect_url'];
            $fund->update([
                'snap_token' => $snapToken,
                'redirect_url' => $url
            ]);
            $fund->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Please finish the payment',
                'data' => [
                    'redirect_url' => $fund->redirectUrl()
                ]
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage(),
                "error" => "Internal server error",
            ], 500);
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
}
