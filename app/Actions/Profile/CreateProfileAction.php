<?php

namespace App\Actions\Profile;

use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateProfileAction
{
    public function execute(array $data): Profile
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();

            if ($user->profile) {
                throw new \RuntimeException('O usuário autenticado já possui um perfil.');
            }

            $avatarPath = null;

            if (($data['avatar_photo'] ?? null) instanceof UploadedFile) {
                $avatarPath = $data['avatar_photo']->store('avatars', 'public');
            }

            return $user->profile()->create([
                'bio' => $data['bio'] ?? null,
                'avatar_photo' => $avatarPath,
            ]);
        });
    }
}
