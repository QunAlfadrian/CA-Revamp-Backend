<?php

namespace App\Http\Controllers;

use App\Http\Resources\V1\BookCollection;
use App\Http\Resources\V1\BookResource;
use App\Models\Book;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Services\ImageService;
use App\Http\Services\PaginateService;
use App\Models\Role;

class BookController extends Controller {
    public function __construct(
        private ImageService $imageService,
        private PaginateService $paginateService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $query = Book::query();

        // sort
        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            $direction = 'asc';

            if (Str::of($sort)->startsWith('-')) {
                $sort = Str::of($sort)->ltrim('-');
                $direction = 'desc';
            }

            $sort_by = [
                'published_year',
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
                'synopsis',
                'author_1',
                'author_2',
                'author_3'
            ];

            foreach ($search_fields as $field) {
                $query->orWhere($field, 'like', '%' . $term . '%');
            }
        }

        return new BookCollection($query->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $user = auth()->user();
        if (!($user->isActingAs(Role::admin()) || $user->isActingAs(Role::organizer()))) {
            return response()->json([
                'error' => 'true',
                'message' => 'You do not have permission to access this resource'
            ], 403);
        }

        $rules = [
            'isbn' => 'required|string|min:10|max:13|unique:books,isbn',
            'title' => 'required|string|max:255|unique:books,title',
            'synopsis' => 'nullable|string|max:2048',
            'author_1' => 'required|string|max:255',
            'author_2' => 'nullable|string|max:255',
            'author_3' => 'nullable|string|max:255',
            'published_year' => 'required|string|max:4',
            'cover_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'price' => 'required|numeric|max:1000000'
        ];

        $request->validate($rules);

        $book = Book::create([
            'isbn' => $request->input('isbn'),
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')),
            'synopsis' => $request->input('synopsis') ?? null,
            'author_1' => $request->input('author_1'),
            'author_2' => $request->input('author_2') ?? null,
            'author_3' => $request->input('author_3') ?? null,
            'published_year' => $request->input('published_year'),
            'price' => $request->input('price')
        ]);

        if ($request->hasFile('cover_image')) {
            $slug = $book->slug;
            $image = $request->file('cover_image');
            $filename = 'cover-' . $slug . "-" . now()->format('YmdHis') . ".webp";
            $path = 'images/books/';

            $this->imageService->storeImage(
                $image,
                $filename,
                $path,
                75,
            );

            $book = Book::find($request->isbn)->first();
            $book->update([
                'cover_image_url' => asset($path . $filename)
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => new BookResource($book)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book) {
        return response()->json([
            'success' => true,
            'data' => new BookResource($book)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book) {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book) {
        // get data for response
        $title = $book->title();
        $isbn = $book->isbn();

        // delete cover image from storage
        $url = $book->coverImageUrl();
        $relativePath = Str::after($url, config('app.url') . '/');
        $oldPath = public_path($relativePath);

        if (file_exists($oldPath)) {
            unlink($oldPath);
        }

        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully deleted book ' . $title . ', isbn ' . $isbn
        ], 200);
    }
}
