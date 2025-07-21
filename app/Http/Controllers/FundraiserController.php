<?php

namespace App\Http\Controllers;

use App\Http\Resources\V1\CampaignResource;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Services\ImageService;
use App\Http\Services\PaginateService;

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
            'slug' => Str::slug($request->input('attributes.title')),
            'description' => $request->input('attributes.description'),
            'requested_fund_amount' => $request->input('attributes.requested_fund_amount'),
        ]);

        // store and update header image
        $campaign = $user->campaignsRelation()
            ->where('slug', Str::slug($request->input('attributes.title')))
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
            'data' => new CampaignResource($campaign)
        ], 200);
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
