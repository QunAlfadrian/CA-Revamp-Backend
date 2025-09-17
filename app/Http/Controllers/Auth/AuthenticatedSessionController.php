<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\V1\RoleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthenticatedSessionController extends Controller {
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request) {
        $request->authenticate();

        $user = auth()->user();

        $token = $user->createToken($user->name())->plainTextToken;
        $cookie = Cookie::make('auth-token', $token, 60, '/', null, true, true);
        return response()->json([
            'success' => true,
            'data' => [
                'token' => $user->createToken($user->name())->plainTextToken,
                'user' => [
                    'name' => $user->name(),
                    'email' => $user->email(),
                    'profile_image' => $user->identity() ? $user->identity()->profileImageUrl() : null,
                    'roles' => RoleResource::collection($user->roles())
                ]
            ],
            'message' => 'User logged in!',
        ])->cookie('auth-token', $token, 60, '/', null, true, true);
        // return response('OK')->cookie('auth-token', $token, 60, '/', null, true, true);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse {
        Auth::user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'User logged out!'
        ]);
    }
}
