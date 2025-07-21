<?php

namespace App\Http\Controllers;

use App\Http\Resources\V1\CampaignCollection;
use App\Http\Resources\V1\CampaignResource;
use App\Models\Campaign;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Services\SearchService;
use App\Http\Services\PaginateService;
use App\Models\Role;
use Carbon\Carbon;

class CampaignController extends Controller {
    public function __construct(
        private SearchService $searchService,
        private PaginateService $paginateService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $query = Campaign::query();

        // exclude pending and rejected campaigns
        $query->whereNot('status', 'pending')->whereNot('status', 'rejected');

        // filter type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // filter status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // sort
        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            $direction = 'asc';

            if (Str::of($sort)->startsWith('-')) {
                $sort = Str::of($sort)->ltrim('-');
                $direction = 'desc';
            }

            $sort_by = [
                'created_at',
                'type',
                'title',
            ];

            if (in_array($sort, $sort_by)) {
                $query->orderBy($sort, $direction);
            }
        }

        // search
        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->with('organizerRelation');

            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', '%' . $term . '%')
                    ->orWhere('type', 'like', '%' . $term . '%')
                    ->whereHas('organizerRelation', function ($q2) use ($term) {
                        $q2->where('name', 'like', '%' . $term . '%');
                    });
            });
        }

        return new CampaignCollection($query->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $user = auth()->user();
        if (!$user->isActingAs(Role::organizer())) {
            return response()->json([
                'error' => true,
                'message' => 'You do not have permission to access this resource'
            ], 403);
        }

        $request->validate([
            'type' => 'required|string|in:fundraiser,product_donation',
        ]);

        $rules = [
            'attributes.title' => 'required|string|min:10|max:255|unique:campaigns,title',
            'attributes.description' => 'required|string|min:20|max:2500',
            'attributes.header_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ];
        $request->validate($rules);

        if ($request->input('type') === 'fundraiser') {
            return app(FundraiserController::class)->store($request);
        }

        if ($request->input('type') === 'product_donation') {
            $rules['requested_fund_amount'] = 'required|numeric|min:50000|max:9999999999';
            $rules['requested_item_quantity'] = 'required|integer|min:1';
        }

        $request->validate($rules);

        $user = auth()->user();
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign) {
        return (new CampaignResource($campaign))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campaign $campaign) {
        $user = auth()->user();

        if (!($user->isActingAs(Role::admin()) || $user->isActingAs(Role::organizer()))) {
            return response()->json([
                'error' => 'unauthorized',
                'message' => 'You do not have permission to access this resource'
            ], 403);
        }

        // review campaign as admin
        if ($request->filled('_action') && $request->input('_action') === 'review') {
            if (!$user->isActingAs(Role::admin())) {
                return response()->json([
                    'error' => 'unauthorized',
                    'message' => 'You do not have permission to access this resource'
                ], 403);
            }

            $rules = [
                'status' => 'required|string|in:accepted,rejected'
            ];
            $request->validate($rules);

            $campaign->update([
                'status' => $request->input('status') === 'accepted' ? 'on_progress' : 'rejected',
                'reviewed_at' => Carbon::now()
            ]);
            $campaign->reviewedBy($user);

            return response()->json([
                'success' => true,
                'message' => 'Successfully reviews ' . $campaign->title()
            ], 200);
        }

        if ($request->filled('_action') && $request->input('_action') === 'update') {
            if (!$user->isActingAs(Role::organizer())) {
                return response()->json([
                    'error' => 'unauthorized',
                    'message' => 'You do not have permission to access this resource'
                ], 403);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign) {
        $user = auth()->user();
        if (!$user->isActingAs(Role::organizer())) {
            return response()->json([
                'error' => 'unauthorized',
                'message' => 'You do not have permission to access this resource'
            ], 403);
        }

        // prevent deletion of ongoing campaigns
        if ($campaign->status() !== 'pending' || $campaign->status() !== 'rejected') {
            return response()->json([
                'error' => 'forbidden',
                'message' => 'You are not allowed to delete ongoing or finished campaigns'
            ], 403);
        }

        $campaign->delete();

        return response()->json([
            'success' => true,
            'message' => 'Campaign successfully deleted'
        ], 200);
    }

    /**
     * Restore the specified resource
     */
    public function restore($id) {
        $user = auth()->user();
        if (!$user->isActingAs(Role::organizer())) {
            return response()->json([
                'error' => 'unauthorized',
                'message' => 'You do not have permisson to access this resource'
            ], 403);
        }

        $campaign = $user->campaignsRelation()
            ->withoutGlobalScopes()
            ->onlyTrashed()
            ->where('id', $id)
            ->orWhere('slug', $id)
            ->firstOrFail();

        if (!$campaign->trashed()) {
            return response()->json([
                'error' => 'invalid_action',
                'message' => 'This campaign is not deleted and cannot be restored.'
            ], 400);
        }

        $campaign->restore();

        return response()->json([
            'success' => true,
            'message' => 'Campaign restored successfully'
        ], 200);
    }

    /**
     * Display listing of owned resource
     */
    public function organizerIndex(Request $request) {
        $user = auth()->user();
        if (!$user->isActingAs(Role::organizer())) {
            return response()->json([
                'error' => 'unauthorized',
                'message' => 'You do not have permission to access this resource'
            ], 403);
        }

        $query = Campaign::query();
        $query->whereHas('organizerRelation', function ($q) use ($user) {
            $q->where('id', $user->id());
        });

        // filter type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // filter status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // sort
        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            $direction = 'asc';

            if (Str::of($sort)->startsWith('-')) {
                $sort = Str::of($sort)->ltrim('-');
                $direction = 'desc';
            }

            $sort_by = [
                'created_at',
                'type',
                'title',
            ];

            if (in_array($sort, $sort_by)) {
                $query->orderBy($sort, $direction);
            }
        }

        // search
        if ($request->filled('search')) {
            $term = $request->input('search');
            $search_fields = [
                'title',
                'type',
            ];

            foreach ($search_fields as $field) {
                $query->orWhere($field, 'like', '%' . $term . '%');
            }
        }

        return new CampaignCollection($query->paginate(10));
    }

    /**
     * Display listing of trashed owned resources
     */
    public function organizerTrashed(Request $request) {
        $user = auth()->user();
        if (!$user->isActingAs(Role::organizer())) {
            return response()->json([
                'error' => 'unauthorized',
                'message' => 'You do not have permission to access this resource'
            ], 403);
        }

        $query = Campaign::query()->withoutGlobalScopes();
        $query->whereHas('organizerRelation', function ($q) use ($user) {
            $q->where('id', $user->id());
        });

        // limit to trashed campaigns
        $query->onlyTrashed();

        // filter type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // sort
        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            $direction = 'asc';

            if (Str::of($sort)->startsWith('-')) {
                $sort = Str::of($sort)->ltrim('-');
                $direction = 'desc';
            }

            $sort_by = [
                'created_at',
                'type',
                'title',
            ];

            if (in_array($sort, $sort_by)) {
                $query->orderBy($sort, $direction);
            }
        }

        // search
        if ($request->filled('search')) {
            $term = $request->input('search');
            $search_fields = [
                'title',
                'type',
            ];

            foreach ($search_fields as $field) {
                $query->orWhere($field, 'like', '%' . $term . '%');
            }
        }

        return response()->json([
            'success' => true,
            'data' => new CampaignCollection($query->paginate(10))
        ], 200);
    }

    /**
     * Display listing of all resource for administrative purposes
     */
    public function adminIndex(Request $request) {
        $user = auth()->user();
        if (!$user->isActingAs(Role::admin())) {
            return response()->json([
                'error' => 'unauthorized',
                'message' => 'You do not have permission to access this resource'
            ], 403);
        }

        $query = Campaign::query();

        // filter type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // filter status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // sort
        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            $direction = 'asc';

            if (Str::of($sort)->startsWith('-')) {
                $sort = Str::of($sort)->ltrim('-');
                $direction = 'desc';
            }

            $sort_by = [
                'created_at',
                'type',
                'title',
            ];

            if (in_array($sort, $sort_by)) {
                $query->orderBy($sort, $direction);
            }
        }

        // search
        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->with('organizerRelation');
            $search_fields = [
                'title',
                'type',
            ];

            foreach ($search_fields as $field) {
                $query->orWhere($field, 'like', '%' . $term . '%');
            }

            // relation search
            $query->whereHas('organizerRelation', function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%');
            });
        }

        return new CampaignCollection($query->paginate(10));
    }
}
