<?php


namespace App\Services;

use App\Asset;
use App\Assets\AssetableContract;
use App\Assets\Type\Image;
use App\Assets\UploadedAsset;
use App\Processors\Image\Breakpoints\Large;
use App\Processors\Image\Breakpoints\Medium;
use App\Processors\Image\Breakpoints\Small;
use App\Processors\Image\Breakpoints\XLarge;
use App\Repositories\Contracts\AssetRepositoryContract;
use Intervention\Image\Image as ImageInstance;

class ProductService
{
    /**
     * @var AssetRepositoryContract
     */
    private AssetRepositoryContract $assetRepository;

    /**
     * ProductService constructor.
     *
     * @param AssetRepositoryContract $assetRepository
     */
    public function __construct(AssetRepositoryContract $assetRepository)
    {
        $this->assetRepository = $assetRepository;
    }

    public function saveImage(AssetableContract $product, UploadedAsset $file): Asset
    {
        return $this->assetRepository->create(
            $product,
            Image::class,
            $file,
            [
                new Small(function (ImageInstance $image) {
                    return $image->fit(400, 300)->flip('v');
                }),
                new Medium(function (ImageInstance $image) {
                    return $image->fit(600, 400)->colorize(-50, 0, 80);
                }),
                new Large(function (ImageInstance $image) {
                    return $image->fit(800, 600)->flip('h');
                }),
                new XLarge(function (ImageInstance $image) {
                    return $image->fit(1000, 800)->flip('v');
                }),
            ]
        );
    }

    /**
     * Remove product image;
     * @param int $file
     */
    public function removeImage(int $file): void
    {
        $this->assetRepository->remove(Asset::find($file));
    }
}
