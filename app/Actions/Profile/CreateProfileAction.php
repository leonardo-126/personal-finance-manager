<?php

use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateProfileAction
{
    public function execute(array $data): Profile
    {
         return DB::transaction(function () use ($data) {
            $profile = Profile::create([
                'bio' => $data['bio'] ?? null,
                'avatar_photo' => $data['avatar_photo'] ?? null,
            ]);

            return $profile;
        });
    }
}
