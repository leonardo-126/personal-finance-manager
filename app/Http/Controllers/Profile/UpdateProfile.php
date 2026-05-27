<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfile as ProfileUpdateRequest;
use Illuminate\Http\Request;
use UpdateProfileAction;

class UpdateProfile extends Controller
{
    public function __invoke(ProfileUpdateRequest $request, UpdateProfileAction $action)
    {
        $profile = $action->execute($request->validated());

        return response()->json($profile, 200);
    }
}
