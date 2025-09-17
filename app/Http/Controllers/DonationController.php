<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Book;
use App\Models\Role;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RequestedSupply;
use Illuminate\Support\Facades\DB;
use App\Http\Services\ImageService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\MidtransService;
use App\Http\Resources\V1\DonationCollection;
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

            $query->where(function ($q) use ($fundStatus, $status) {
                $q->whereHas('fundsRelation', function ($fund) use ($fundStatus) {
                    $fund->where('status', $fundStatus);
                });

                switch ($status) {
                    case 'success':
                        $q->orWhereHas('donatedItemsRelation', function ($q) {
                            $q->where('status', 'received');
                        });
                        break;
                    case 'failed':
                        $q->orWhereHas('donatedItemsRelation', function ($q) {
                            $q->where('status', 'not_received')
                                ->orWhere('status', 'cancelled')
                                ->orWhere('status', 'declined');
                        });
                        break;
                    case 'pending':
                        $q->orWhereHas('donatedItemsRelation', function ($q) {
                            $q->where('status', 'pending_verification')
                                ->orWhere('status', 'on_delivery');
                        });
                        break;
                    default:
                        break;
                }
            });
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
     * Display a listing of the resource based on campaigns
     */
    public function donationByCampaign(Request $request, Campaign $campaign) {
        $user = auth('sanctum')->user();

        if (!$user->isActingAs(Role::organizer())) {
            return response()->json([
                'message' => 'Unauthorized',
                'errors' => [
                    'role' => 'You do not have permission to access this resource'
                ]
            ], 403);
        }

        $query = Donation::query();
        $query->where('campaign_id', $campaign->id());

        // filter type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // filter status
        $query->where(function ($q) {
            $q->whereHas('fundsRelation', function ($fund) {
                $fund->where('status', 'paid');
            })->orWhereHas('donatedItemsRelation');
        });

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
                'username' => 'string|min:5|max:25',
            ];
        }
        $request->validate($rules);

        if ($user && $user->id() === $campaign->organizerId()) {
            return response()->json([
                'message' => 'You cannot donate to your own campaign.',
                'errors' => [
                    'donor' => 'You are the organizer of this campaign'
                ]
            ], 403);
        }

        if ($campaign->type() === 'fundraiser') {
            return app(FundController::class)->store($request, $campaign);
        }

        if ($campaign->type() === 'product_donation') {
            return app(DonatedItemController::class)->store($request, $campaign);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign, Donation $donation) {
        
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
