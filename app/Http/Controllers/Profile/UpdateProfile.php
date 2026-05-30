<?php

namespace App\Http\Controllers\Profile;

use App\Actions\Profile\UpdateProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfile as UpdateProfileRequest;
use App\Http\Resources\ProfileResource;

class UpdateProfile extends Controller
{
    public function __invoke(UpdateProfileRequest $request, UpdateProfileAction $action)
    {
        $profile = $action->execute($request->validated());

        return new ProfileResource($profile);
    }
}
