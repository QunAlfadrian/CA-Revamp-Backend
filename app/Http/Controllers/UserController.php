<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\V1\UserResource;

class UserController extends Controller {
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request) {
        return UserResource::make(
            auth()->user()
        );
    }
}
