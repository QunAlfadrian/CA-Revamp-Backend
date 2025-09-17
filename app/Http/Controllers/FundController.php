<?php

namespace App\Http\Controllers;

use App\Http\Resources\V1\FundCollection;
use Exception;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\MidtransService;
use App\Models\Fund;
use Illuminate\Support\Facades\Log;

class FundController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Donation $donation) {

    }

    /**
     * Display listing of the resource by Campaign
     */
    public function indexByCampaign(Request $request, Campaign $campaign) {
        $query = Fund::query();

        $query->whereHas('campaignRelation', function ($q) use ($campaign) {
            $q->where('campaign_id', $campaign->id());
        });

        if ($request->filled("status")) {
            $query->where('status', $request->input('status'));
        }

        return new FundCollection($query->orderBy('updated_at', 'desc')->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Campaign $campaign) {
        $rules = [];
        $user = auth('sanctum')->user();

        $rules += [
            'fund_amount' => 'required|integer|min:5000|max:20000000'
        ];
        $request->validate($rules);

        DB::beginTransaction();
        try {
            // get or create donation instance
            $username = $user
                ? $user->name()
                : (
                    $request->filled('username')
                    ? $request->input('username')
                    : "Kind Donor"
                );
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
                'fund_id' => $orderId,
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
                    'finish' => config('app.frontend_url') . "/campaigns/" . $campaign->slug() ?? url('/'),
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
