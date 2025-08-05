<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\V1\RoleResource;
use App\Http\Resources\V1\UserResource;
use App\Http\Resources\V1\UserSummaryResource;

class UserController extends Controller {
    /**
     * Handle the incoming request.
     */
    // public function __invoke(Request $request) {
    //     $user = auth()->user();
    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'user' => [
    //                 'name' => $user->name(),
    //                 'email' => $user->email()
    //             ],
    //             'roles' => RoleResource::collection($user->roles())
    //         ]
    //     ], 200);
    // }

    public function show(Request $request, User $user) {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => UserSummaryResource::make($user)
            ]
        ]);
    }
}
