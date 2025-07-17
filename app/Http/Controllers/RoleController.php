<?php

namespace App\Http\Controllers;

use App\Http\Resources\V1\RoleResource;
use Illuminate\Http\Request;

class RoleController extends Controller {
    public function __invoke(Request $request) {
        return response()->json([
            'success' => true,
            'data' => RoleResource::collection(auth()->user()->roles())
        ], 200);
    }
}
