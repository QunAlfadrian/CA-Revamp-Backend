<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller {
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request) {
        $request->authenticate();

        $user = auth()->user();

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $user->createToken($user->name())->plainTextToken,
                'name' => $user->name(),
            ],
            'message' => 'User logged in!',
        ]);
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
