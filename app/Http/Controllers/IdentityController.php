<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Services\ImageService;
use App\Http\Resources\V1\UserResource;
use App\Http\Resources\V1\IdentityResource;

class IdentityController extends Controller {
    public function __construct(private ImageService $imageService) {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $user = auth()->user();
        $rules = [
            'full_name' => 'bail|required|string|max:127',
            'profile_image' => 'bail|sometimes|image|max:5048',
        ];

        if ($user->isActingAs(Role::donor())) {
            $rules += [
                'phone_number' => 'bail|sometimes|string|max:15',
                'gender' => ['sometimes', 'string', Rule::in(['male', 'female', 'other'])],
                'date_of_birth' => ['sometimes', 'date', 'before_or_equal:' . now()->subYears(18)->toDateString()],
                'nik' => 'sometimes|string|size:16',
                'id_card_image' => 'bail|sometimes|image|max:5048',
            ];
        }

        if ($user->isActingAs(Role::organizer())) {
            $rules += [
                'phone_number' => 'bail|required|string|max:15',
                'gender' => ['required', 'string', Rule::in(['male', 'female', 'other'])],
                'date_of_birth' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->toDateString()],
                'nik' => 'required|string|size:16',
                'id_card_image' => 'bail|required|image|max:5048',
            ];
        }

        $request->validate($rules);

        $identityData = [
            'full_name'      => $request->input('full_name'),
            'phone_number'   => $request->input('phone_number'),
            'gender'         => $request->input('gender'),
            'date_of_birth'  => $request->input('date_of_birth'),
            'nik'            => $request->input('nik'),
        ];

        $user->identityRelation()->create($identityData);

        if ($request->hasFile('profile_image')) {
            $slug = Str::slug($user->name());
            $image = $request->file('profile_image');
            $filename = 'profile-' . $slug . "-" . now()->format('YmdHis') . ".webp";
            $path = 'images/profiles/';

            $this->imageService->storeImage(
                $image,
                $filename,
                $path,
                75,
                true
            );
            $user->identity()->update([
                'profile_image_url' => asset($path . $filename)
            ]);
        }

        if ($request->hasFile('id_card_image')) {
            $slug = Str::slug($user->name());
            $image = $request->file('id_card_image');
            $filename = Str::uuid() . "-" . now()->format('YmdHis') . ".webp";
            $path = 'images/id_cards/';

            $this->imageService->storeImage(
                $image,
                $filename,
                $path,
            );

            $user->identity()->update([
                'id_card_image_url' => asset($path . $filename)
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => new IdentityResource($user->identity())
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request) {
        $user = auth()->user();

        if (!$user->identity()) {
            return response()->json([
                'message' => 'Identity not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user->identity()
                ? new IdentityResource($user->identity())
                : null,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request) {
        $user = auth()->user();
        $identity = $user->identity();

        $rules = [
            'full_name' => 'bail|required|string|max:127',
            'profile_image' => 'bail|sometimes|image|max:5048',
        ];

        if ($user->isActingAs(Role::donor())) {
            $rules += [
                'phone_number' => 'bail|sometimes|string|max:15',
                'gender' => ['sometimes', 'string', Rule::in(['male', 'female', 'other'])],
                'date_of_birth' => ['sometimes', 'date', 'before_or_equal:' . now()->subYears(18)->toDateString()],
                'nik' => 'sometimes|string|size:16',
                'id_card_image' => 'bail|sometimes|image|max:5048',
            ];
        }

        if ($user->isActingAs(Role::organizer())) {
            $rules += [
                'phone_number' => 'bail|required|string|max:15',
                'gender' => ['required', 'string', Rule::in(['male', 'female', 'other'])],
                'date_of_birth' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->toDateString()],
                'nik' => 'required|string|size:16',
                'id_card_image' => 'bail|required|image|max:5048',
            ];
        }

        $request->validate($rules);

        $identityData = [
            'full_name'      => $request->input('full_name'),
            'phone_number'   => $request->input('phone_number'),
            'gender'         => $request->input('gender'),
            'date_of_birth'  => $request->input('date_of_birth'),
            'nik'            => $request->input('nik'),
        ];

        $identity->update($identityData);

        if ($request->hasFile('profile_image')) {
            if (!is_null($identity->profileImageUrl())) {
                $url = $identity->profileImageUrl();
                $this->imageService->deleteImage($url);
            }

            $slug = Str::slug($user->name());
            $image = $request->file('profile_image');
            $filename = 'profile-' . $slug . "-" . now()->format('YmdHis') . ".webp";
            $path = 'images/profiles/';

            $this->imageService->storeImage(
                $image,
                $filename,
                $path,
                75,
                true
            );
            $user->identity()->update([
                'profile_image_url' => asset($path . $filename)
            ]);
        }

        if ($request->input('delete_profile_image')) {
            $url = $identity->profileImageUrl();
            $this->imageService->deleteImage($url);
            $identity->update([
                'profile_image_url' => null
            ]);
        }

        if ($request->hasFile('id_card_image')) {
            if (!is_null($identity->idCardImageUrl())) {
                $url = $identity->idCardImageUrl();
                $this->imageService->deleteImage($url);
                $identity->update([
                    'id_card_image_url' => null
                ]);
            }

            $slug = Str::slug($user->name());
            $image = $request->file('id_card_image');
            $filename = Str::uuid() . "-" . now()->format('YmdHis') . ".webp";
            $path = 'images/id_cards/';

            $this->imageService->storeImage(
                $image,
                $filename,
                $path,
            );

            $user->identity()->update([
                'id_card_image_url' => asset($path . $filename)
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => new IdentityResource($user->identity())
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
    }
}
