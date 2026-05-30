<?php

namespace App\Actions\Profile;

use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateProfileAction
{
    public function execute(array $data): Profile
    {
        return DB::transaction(function () use ($data) {
            $profile = Auth::user()->profile;

            if (! $profile) {
                throw new \RuntimeException('Perfil não encontrado para o usuário autenticado.');
            }

            $avatarPath = $profile->avatar_photo;

            if (($data['avatar_photo'] ?? null) instanceof UploadedFile) {
                $newPath = $data['avatar_photo']->store('avatars', 'public');

                if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
                    Storage::disk('public')->delete($avatarPath);
                }

                $avatarPath = $newPath;
            }

            $profile->update([
                'bio' => array_key_exists('bio', $data) ? $data['bio'] : $profile->bio,
                'avatar_photo' => $avatarPath,
            ]);

            return $profile;
        });
    }
}
