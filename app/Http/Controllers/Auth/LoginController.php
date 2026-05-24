<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoginUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request, LoginUserAction $action): JsonResponse
    {
        $user = $action->execute($request->validated());

        $request->session()->regenerate();

        return response()->json([
            'user' => new UserResource($user),
        ]);
    }
}
