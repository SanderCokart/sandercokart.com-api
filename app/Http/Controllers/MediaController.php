<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @OAS\SecurityScheme(
 *      securityScheme="bearer_token",
 *      type="http",
 *      scheme="bearer"
 * )
 */
class MediaController extends Controller
{
    /**
     * @OA\Get(
     *      path="/media?uuid={uuid}&disk={disk}&signature={signature}",
     *      tags={"sandercokart.com"},
     *      description="Retrieve media from private disk",
     *      security={ {"sanctum": {} }},
     *      @OA\Parameter(
     *          name="uuid",
     *          in="query",
     *          description="Media UUID",
     *          required=true,
     *      ),
     *      @OA\Parameter(
     *          name="disk",
     *          in="query",
     *          description="Disk name hash",
     *          required=true,
     *      ),
     *      @OA\Parameter(
     *          name="signature",
     *          in="query",
     *          description="signature",
     *          required=true,
     *      ),
     *      @OA\Response(response="200", description="Success",
     *          @OA\MediaType(
     *              mediaType="*",
     *              @OA\Schema(type="string",format="binary")
     *          )
     *      ),
     *  ),
     */
    public function show(Request $request): ?Media
    {
        if (! $request->hasValidSignature()) {
            abort(404);
        }

        /** @var Media|null $media */
        $media = Media::findByUuid($request->uuid);
        if (is_null($media)) {
            abort(404);
        }

        if ($media?->disk === sha1($request->disk)) {
            abort(404);
        }

        return $media;
    }
}
