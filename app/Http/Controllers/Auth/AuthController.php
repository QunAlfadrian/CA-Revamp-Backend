<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller {
    public function register(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $role = Role::where('name', 'donor')->first();
        $user = User::where('name', $request->name)
            ->where('email', $request->email)->first();
        $user->actingAs($role);

        $token = $user->createToken($user->name())->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'name' => $user->name(),
            ],
            'message' => 'Successfully signed up!',
        ]);
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Unauthorized'
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken($user->name())->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'name' => $user->name(),
            ],
            'message' => 'User logged in!',
        ]);
    }

    public function logout() {
        Auth::user()->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => 'User logged out!'
        ]);
    }
}
