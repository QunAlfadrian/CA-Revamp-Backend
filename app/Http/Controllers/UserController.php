<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\V1\RoleResource;
use App\Http\Resources\V1\UserCollection;
use App\Http\Resources\V1\UserResource;
use App\Http\Resources\V1\UserSummaryResource;

class UserController extends Controller {
    public function adminIndex(Request $request) {
        $user = auth()->user();

        if (!$user || !$user->isActingAs(Role::admin()) || !$user->isActingAs(Role::superAdmin())) {
            return response()->json([
                'error' => 'unauthorized',
                'message' => 'You do not have permission to access this resource'
            ], 403);
        }

        $query = User::query();

        $perPage = 10;
        if ($request->filled('per_page')) {
            $perPage = $request->input('per_page');
        }

        return new UserCollection($query->paginate($perPage));
    }

    public function show(Request $request, User $user) {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => UserSummaryResource::make($user)
            ]
        ], 200);
    }
}
