<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Services\SearchService;
use App\Models\OrganizerApplication;
use App\Http\Services\PaginateService;
use App\Http\Resources\V1\OrganizerApplicationResource;
use App\Http\Resources\V1\OrganizerApplicationCollection;

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

        DB::beginTransaction();

        try {
            // check if user have not filled their identity
            if (is_null($user->identity())) {
                return response()->json([
                    'error' => "identities",
                    'message' => 'Please complete your identity information before applying'
                ], 403);
            }

            // check if user already applying application
            if (!is_null($user->organizerApplication())) {
                return response()->json([
                    'error' => "organizer_applications",
                    'message' => 'Can only have one active Application'
                ], 403);
            }

            $user->organizerApplicationRelation()->create([]);
            $user->refresh();

            DB::commit();
            return response()->json([
                'success' => true,
                'data' => $user->organizerApplication()
                    ? OrganizerApplicationResource::make($user->organizerApplication())
                    : null
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json([
                'error' => $e->getMessage(),
                'message' => "Internal Server Error"
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show() {
        $user = auth()->user();
        $application = $user->organizerApplication();

        if (!$application) {
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
            'id' => 'bail|required|exists:organizer_applications,organizer_application_id',
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
