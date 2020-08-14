<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\UploadImageRequest;
use App\Http\Resources\AssetResource;
use App\Product;
use App\Services\FileUploadService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;

class ProductImageController extends Controller
{

    /**
     * @var FileUploadService
     */
    private FileUploadService $fileUploadService;

    /**
     * @var ProductService
     */
    private ProductService $productService;

    /**
     * @var ImageManager
     */
    private ImageManager $manager;

    /**
     * ProductImageController constructor.
     * @param ImageManager $manager
     * @param ProductService $productService
     * @param FileUploadService $fileUploadService
     */
    public function __construct(ImageManager $manager, ProductService $productService, FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
        $this->productService = $productService;
        $this->manager = $manager;
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
            $this->fileUploadService->upload($request, function (UploadedFile $file) {
                if ($file->getClientMimeType() === 'image/svg+xml') {
                    return $file;
                }
                return $this->manager->make($file)->greyscale()->save()->basePath();
            }),
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
