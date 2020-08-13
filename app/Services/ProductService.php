<?php


namespace App\Services;


use App\Asset;
use App\Assets\AssetableContract;
use App\Assets\Type\Image;
use App\Assets\UploadedAsset;
use App\Processors\Image\Breakpoints\Breakpoint;
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
                new Small(400,300),
                new Medium(600, 400),
                new Large(800, 600),
                new XLarge(1000, 800)
            ],
            function (ImageInstance $image, Breakpoint $breakpoint) {
                return $image->fit($breakpoint->width, $breakpoint->height);
            }
        );
    }
}
