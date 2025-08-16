<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Book;
use App\Models\supply;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\ImageService;
use App\Http\Services\PaginateService;
use App\Http\Resources\V1\CampaignResource;
use App\Models\RequestedSupply;

class ProductDonationController extends Controller {
    public function __construct(
        private ImageService $imageService,
        private PaginateService $paginateService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $rules = [
            'selection_mode' => 'required|string|in:bulk,manual',
        ];
        $request->validate($rules);

        if ($request->input('selection_mode') === 'bulk') {
            $rules += [
                'attributes.requested_item_quantity' => 'required|numeric|min:50|max:500',
            ];
        }

        if ($request->input('selection_mode') === 'manual') {
            $rules += [
                'attributes.requested_item_quantity' => 'required|numeric|min:1|max:500',
                'books' => 'nullable|array',
                'books.*.isbn' => 'required_with:books|string|min:10|max:13|exists:books,isbn',
                'books.*.quantity' => 'required_with:books|numeric|min:1|max:10',
                'supplies' => 'nullable|array',
                'supplies.*.name' => 'required_with:supplies|string|min:5|max:50',
                'supplies.*.description' => 'required_with:supplies|string|min:25|max:255',
                'supplies.*.quantity' => 'required_with:supplies|numeric|min:1|max:10'
            ];
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $user->campaignsRelation()->create([
                'type' => $request->input('type'),
                'title' => $request->input('attributes.title'),
                'slug' => $request->filled('attributes.slug')
                    ? $request->input('attributes.slug')
                    : Str::slug($request->input('attributes.title')),
                'description' => $request->input('attributes.description'),
                'requested_item_quantity' => $request->input('attributes.requested_item_quantity')
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

            // store books if any
            if ($request->input('selection_mode') === 'manual' && $request->filled('books')) {
                $books = $request->input('books');
                foreach ($books as $book) {
                    $bookInstance = Book::find($book['isbn']);
                    $campaign->requestBook(
                        $bookInstance,
                        $book['quantity']
                    );
                }
                $campaign->refresh();
            }

            // store supplies if any
            if ($request->input('selection_mode') === 'manual' && $request->filled('supplies')) {
                $supplies = $request->input('supplies');
                foreach ($supplies as $supply) {
                    $supplyInstance = RequestedSupply::make([
                        'name' => $supply['name'],
                        'description' => $supply['description'],
                        'requested_quantity' => $supply['quantity']
                    ]);
                    $campaign->requestSupply($supplyInstance);
                }
                $campaign->refresh();
            }

            //

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Campaign successfully created',
                'data' => new CampaignResource($campaign)
            ], 201);
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'unprocessable_content',
                'message' => $e->getMessage()
            ], 403);
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
