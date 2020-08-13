<?php


namespace App\Repositories\Eloquent;


use App\Asset;
use App\Assets\AssetableContract;
use App\Assets\UploadedAsset;
use App\Processors\Image\ImageVariantProcessor;
use App\Repositories\Contracts\AssetRepositoryContract;
use Closure;

class AssetRepository implements AssetRepositoryContract
{
    /**
     * @var ImageVariantProcessor
     */
    private ImageVariantProcessor $processor;


    /**
     * AssetRepository constructor.
     */
    public function __construct(ImageVariantProcessor $processor)
    {
        $this->processor = $processor;
    }

    public function create(AssetableContract $model, string $type, UploadedAsset $file, array $variants = [], Closure $process = null): Asset
    {
        return $model->assets()->create([
            'type' => $type,
            'disk' => $file->disk,
            'visibility' => $file->visibility,
            'sort' => $this->nextSortFor($type),
            'path' => $file->path,
            'original_name' => $file->original_name,
            'extension' => $file->extension,
            'mime' => $file->mime,
            'size' => $file->size,
            'caption' => $file->original_name,
            'variants' => $file->isImage ? $this->processor->generateVariants($file, $variants, $process) : []
        ]);
    }

    /**
     * @inheritdoc
     */
    public function nextSortFor(AssetableContract $model, string $type): int
    {
        $last = $model->assets()->where('type', $type)->sorted()->get()->last();

        return !is_null($last) ? $last->sort + 1 : 1;
    }

    /**
     * @inheritdoc
     */
    public function remove($assets): void
    {
    }
}
