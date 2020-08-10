<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\UploadImageRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    /**
     * Store new image.
     */
    public function store(UploadImageRequest $request)
    {
    }

    /**
     * Remove image.
     *
     * @return JsonResponse
     */
    public function destroy(): JsonResponse
    {
        return new JsonResponse(['success' => true]);
    }
}
