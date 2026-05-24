<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LogoutUserAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke(Request $request, LogoutUserAction $action): JsonResponse
    {
        $action->execute($request);

        return response()->json(['message' => 'Logout realizado.']);
    }
}
