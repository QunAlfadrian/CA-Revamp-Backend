<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\OrganizerApplication;
use App\Http\Resources\V1\OrganizerApplicationResource;
use App\Http\Resources\V1\OrganizerApplicationCollection;
use App\Http\Services\PaginateService;
use App\Http\Services\SearchService;

class OrganizerApplicationController extends Controller {
    public function __construct(
        private SearchService $searchService,
        private PaginateService $paginateService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $query = OrganizerApplication::query();

        // filter
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
                'reviewed_at',
                'status',
                'reviewed_by'
            ];
            if (in_array($sort, $sort_by)) {
                $query->orderBy($sort, $direction);
            }
        }

        // search
        if ($request->filled('search')) {
            $query->with('applicantRelation');
            $search_fields = [
                'applicantRelation.name',
                'status'
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

            return new OrganizerApplicationCollection($paginated);
        }

        return new OrganizerApplicationCollection($query->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $user = auth()->user();

        // check if user have not filled their identity
        if (is_null($user->identity())) {
            return response()->json([
                'error' => true,
                'message' => 'Please complete your identity information before applying'
            ], 403);
        }

        // check if user already applying application
        if (!is_null($user->organizerApplication())) {
            return response()->json([
                'error' => true,
                'message' => 'Can only have one active Application'
            ], 403);
        }

        $user->organizerApplicationRelation()->create([]);
        $user->refresh();

        return response()->json([
            'success' => true,
            'data' => $user->organizerApplication()
                ? OrganizerApplicationResource::make($user->organizerApplication())
                : null
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show() {
        $user = auth()->user();
        $application = $user->organizerApplication();

        if (is_null($application)) {
            return response()->json([
                'message' => 'Application not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => OrganizerApplicationResource::make($application)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request) {
        $user = auth()->user();

        if (!$user->isActingAs(Role::admin())) {
            return response()->json([
                'error' => true,
                'message' => 'You do not have permission to access this resource'
            ], 403);
        }

        $rules = [
            'id' => 'bail|required|exists:organizer_applications,id',
            'status' => 'bail|required',
            'message' => 'sometimes|max:255'
        ];

        $request->validate($rules);

        $application = OrganizerApplication::find($request->input('id'));
        $application->update([
            'status' => $request->input('status'),
            'reviewed_at' => Carbon::now()
        ]);
        $application->refresh();
        $application->reviewedBy($user);

        if ($request->input('status') === 'accepted') {
            $user = $application->applicant();
            $user->actingAs(Role::organizer());
            $user->refresh();
        }

        if ($request->input('status') === 'rejected') {
            $application->update([
                'rejected_message' => $request->input('message')
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => OrganizerApplicationResource::make($application)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        //
    }
}
