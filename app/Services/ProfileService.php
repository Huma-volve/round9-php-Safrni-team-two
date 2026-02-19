<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public function __construct(private OtpService $otpService) {}

    public function updateProfile(User $user, array $data, ?UploadedFile $avatar = null): array
    {
        $emailChanged = isset($data['email']) && $data['email'] !== $user->email;

        $updatedUser = DB::transaction(function () use ($user, $data, $emailChanged, $avatar) {
            if ($avatar) {
                $data['avatar_path'] = $this->storeAvatar($user, $avatar);
            }
            if ($emailChanged) {
                $data['is_verified'] = false;
            }
            $user->fill($data);

            $user->save();


            $createdOtp = null;
            if ($emailChanged) {
                $createdOtp = $this->otpService->create($user->email, 'email_verify', $user);
            }

            if ($emailChanged && $createdOtp) {
                DB::afterCommit(function () use ($user, $createdOtp) {
                    $this->otpService->sendNotification($user->email, $createdOtp['code'], 'email_verify');
                });
            }
            return $user->fresh();
        });

        return [
            'user' => $updatedUser,
            'email_changed' => $emailChanged
        ];
    }

    public function deleteAccount(User $user): void
    {
        $user->tokens()->delete();
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->delete();
    }

    private function storeAvatar(User $user, UploadedFile $avatar)
    {
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        return $avatar->store('avatars', 'public');
    }
}
