<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\CreateProfile as ProfileCreateProfile;
use CreateProfileAction;
use Illuminate\Http\Request;

class CreateProfile extends Controller
{
    public function __invoke(ProfileCreateProfile $request, CreateProfileAction $action)
    {
        $profile = $action->execute($request->validated());

        return response()->json($profile, 201);
    }
}
