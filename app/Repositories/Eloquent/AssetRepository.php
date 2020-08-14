<?php


namespace App\Repositories\Eloquent;

use App\Asset;
use App\Assets\AssetableContract;
use App\Assets\UploadedAsset;
use App\Processors\Image\ImageVariantProcessor;
use App\Repositories\Contracts\AssetRepositoryContract;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

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

    /**
     * @inheritdoc
     */
    public function create(AssetableContract $model, string $type, UploadedAsset $file, array $variants = [], Closure $process = null): Asset
    {
        return $model->assets()->create([
            'type' => $type,
            'disk' => $file->disk,
            'visibility' => $file->visibility,
            'sort' => $this->nextSortFor($model, $type),
            'path' => $file->path,
            'original_name' => $file->original_name,
            'extension' => $file->extension,
            'mime' => $file->mime,
            'size' => $file->size,
            'caption' => $file->original_name,
            'variants' => $file->isImage ? $this->processor->generateVariants($file, $variants, $process) : [],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function nextSortFor(AssetableContract $model, string $type): int
    {
        $last = $model->assets()->where('type', $type)->sorted()->get()->last();

        return ! is_null($last) ? $last->sort + 1 : 1;
    }

    /**
     * @inheritdoc
     */
    public function remove($assets): void
    {
        if ($assets instanceof Collection) {
            $assets->each([$this, 'removeSingleFile']);
            Asset::whereIn('id', $assets->pluck('id'))->delete();
        } elseif ($assets instanceof Asset) {
            $this->removeSingleFile($assets);
            $assets->delete();
        } else {
            throw new InvalidArgumentException('Argument type passed to AssetRepository#remove should be App\Asset|Illuminate\Support\Collection');
        }
    }

    /**
     * Deletes files.
     *
     * @param Asset $asset
     * @return void
     */
    private function removeSingleFile(Asset $asset): void
    {
        Storage::disk($asset->disk)->delete($asset->path);

        collect($asset->variants)->each(function (array $variant) use ($asset) {
            Storage::disk($asset->disk)->delete($variant['path']);
        });
    }
}
