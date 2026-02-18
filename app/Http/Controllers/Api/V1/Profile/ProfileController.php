<?php

namespace App\Http\Controllers\Api\V1\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Services\ProfileService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(private ProfileService $profileService) {}
    public function show(Request $request)
    {
        return $this->success([
            'user' => $request->user()
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        $avatar = $request->file('avatar') ?? null;

        // Update the user's profile using the ProfileService
        $result = $this->profileService->updateProfile($user, $data, $avatar);

        return $this->success([
            'user' => $result['user'],
            'message' => $result['email_changed'] ?
                'Profile Updated Successfully. Plesae verify your new email' :
                'Profile Updated Successfully.'
        ]);
    }

    public function destroy(Request $request)
    {
        $this->profileService->deleteAccount($request->user());

        return $this->success([
            'message' => 'Account Deleted.'
        ]);
    }
}
