<?php

namespace App\Http\Controllers;

use App\Http\Resources\V1\CampaignCollection;
use App\Http\Resources\V1\CampaignResource;
use App\Models\Campaign;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Services\SearchService;
use App\Http\Services\PaginateService;

class CampaignController extends Controller {
    public function __construct(
        private SearchService $searchService,
        private PaginateService $paginateService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $query = Campaign::query();

        // exclude pending and rejected campaigns
        $query->whereNot('status', 'pending')->whereNot('status', 'rejected');

        // filter type
        if ($request->filled('type')) {
            $query->where('type', $request->input('status'));
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
            $query->with('organizerRelation');
            $search_fields = [
                'title',
                'type',
                'organizerRelation.name'
            ];

            $result = $this->searchService->fuzzySearch(
                $query->get(),
                $request->input('search'),
                $search_fields
            );

            $paginated = $this->paginateService->paginateCollection(
                $result,
                request()->input('page', 1),
                10
            );

            return new CampaignCollection($paginated);
        }

        return new CampaignCollection($query->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $request->validate([
            'type' => 'required|string|in:fundraiser,product_donation',
        ]);

        $rules = [
            'title' => 'required|string|min:10|max:255',
            'description' => 'required|string|min:20|max:2500',
            'header_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ];

        if ($request->input('type') === 'fundraiser') {
            $rules['requested_fund_amount'] = 'required|numeric|min:50000|max:9999999999';
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
