<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\UploadImageRequest;
use App\Http\Resources\AssetResource;
use App\Product;
use App\Services\FileUploadService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{

    /**
     * @var FileUploadService
     */
    private FileUploadService $fileUploadService;
    private ProductService $productService;

    public function __construct(ProductService $productService, FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
        $this->productService = $productService;
    }

    /**
     * Store new image.
     * @param UploadImageRequest $request
     * @param Product $product
     * @return AssetResource
     */
    public function store(UploadImageRequest $request, Product $product): AssetResource
    {
        $asset = $this->productService->saveImage(
            $product,
            $this->fileUploadService->upload($request),
        );

        return new AssetResource($asset);
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
