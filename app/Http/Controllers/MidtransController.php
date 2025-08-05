<?php

namespace App\Http\Controllers;

use App\FundStatus;
use Exception;
use App\Models\Fund;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller {
    public function handleNotifications(Request $request) {
        /**{
             "transaction_time": "2023-11-15 18:45:13",
            "transaction_status": "settlement",
            "transaction_id": "513f1f01-c9da-474c-9fc9-d5c64364b709",
            "status_message": "midtrans payment notification",
            "status_code": "200",
            "signature_key": "35d9e1e0a6a509392523d0d72b4eee8fa3bde9609e4eb6efc2bc1434de2c6993dc24205fd1578cbef8f5a186bc393ca52a86fb33a6ce76bbffbe81c6f4afbd8a",
            "settlement_time": "2023-11-15 22:45:13",
            "payment_type": "gopay",
            "order_id": "payment_notif_test_G228331272_e72a3752-4997-4e97-b9be-cd6733fcb05b",
            "merchant_id": "G228331272",
            "gross_amount": "105000.00",
            "fraud_status": "accept",
            "currency": "IDR"
        } */
       Log::info($request);

       $status = $request->input('transaction_status');
       $orderId = $request->input('order_id');

       // Successful Payment
        if ($status === "settlement" || $status === 'capture') {
            /** @var Fund $fund */
            $fund = Fund::where('order_id', $orderId)->firstOrFail();

            if (!$fund) {
                Log::warning('Midtrans: Fund not found for order_id: '. $orderId);
                return response('OK', 200);
            }

            DB::beginTransaction();
            try {
                /** @var Donation $donation */
                $donation = $fund->donation();

                /** @var Campaign $campaign */
                $campaign = $fund->campaign();

                // update campaigns
                $campaign->update([
                    'donated_fund_amount' => $campaign->donated_fund_amount += $fund->amount()
                ]);

                // update funds
                $fund->update([
                    'status' => FundStatus::mapMidtransStatus($status)
                ]);

                // update donation
                $donation->update([
                    'verified_at' => now()
                ]);

                DB::commit();
                return response('OK', 200);
            } catch (ModelNotFoundException $e) {
                DB::rollBack();
                Log::error('Midtrans Notification Error - Not Found: ' . $e->getMessage());
                return response()->json([
                    'success' => true,
                    'message' => $e->getMessage()
                ], 200);
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Midtrans Notification Error: ' . $e->getMessage(), [
                    'trace' => $e->getTrace()
                ]);
                return response()->json([
                    "message" => $e->getMessage(),
                    "error" => "Internal server error"
                ], 500);
            }
        }

        // Failed Payment
        if ($status === 'deny' || $status === 'cancel' || $status === 'expire' || $status === 'failuer') {
            /** @var Fund $fund */
            $fund = Fund::where('order_id', $orderId)->firstOrFail();

            DB::beginTransaction();
            try {
                $fund->update([
                    'status' => FundStatus::mapMidtransStatus($status)
                ]);
            } catch (ModelNotFoundException $e) {
                DB::rollBack();
                Log::error('Midtrans Notification Error - Not Found: ' . $e->getMessage());
                return response()->json([
                    'success' => true,
                    'message' => $e->getMessage()
                ], 200);
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Midtrans Notification Error: ' . $e->getMessage(), [
                    'trace' => $e->getTrace()
                ]);
                return response()->json([
                    "message" => $e->getMessage(),
                    "error" => "Internal server error"
                ], 500);
            }
        }
    }
}
