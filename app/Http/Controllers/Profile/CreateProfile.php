<?php

namespace App\Http\Controllers\Profile;

use App\Actions\Profile\CreateProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\CreateProfile as CreateProfileRequest;
use App\Http\Resources\ProfileResource;

class CreateProfile extends Controller
{
    public function __invoke(CreateProfileRequest $request, CreateProfileAction $action)
    {
        $profile = $action->execute($request->validated());

        return (new ProfileResource($profile))
            ->response()
            ->setStatusCode(201);
    }
}
