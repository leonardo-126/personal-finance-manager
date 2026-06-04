<?php

namespace App\Http\Controllers\Rendas;

use App\Http\Controllers\Controller;
use App\Http\Resources\RendaResource;
use Illuminate\Http\Request;

class ShowRenda extends Controller
{
    public function __invoke(Request $request)
    {
        $profile = $request->user()->profile;

        if (! $profile) {
            return response()->json(['data' => null]);
        }

        return RendaResource::collection($profile->rendas);
    }
}
