<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HelloController extends Controller
{
    /**
     * @OA\Get(
     *     path="/hello-world",
     *     tags={"Testing"},
     *     @OA\Response(response="200", description="If the request is successful the API works")
     * )
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'message' => Cache::rememberForever('hello-world', fn() => 'Hello World'),
        ]);
    }
}
