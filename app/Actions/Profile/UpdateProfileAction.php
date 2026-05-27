<?php

use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateProfileAction
{
    public function execute(array $data): Profile
    {
        return DB::transaction(function () use ($data) {
            $profile = Auth::user()->profile;

            if (! $profile) {
                throw new \Exception('Perfil não encontrado para o usuário autenticado.');
            }

            $profile->update([
                'bio' => $data['bio'] ?? $profile->bio,
                'avatar_photo' => $data['avatar_photo'] ?? $profile->avatar_photo,
            ]);

            return $profile;
        });
    }
}
