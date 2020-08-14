<?php


namespace App\Repositories\Contracts;

use App\Asset;
use App\Assets\AssetableContract;
use App\Assets\UploadedAsset;
use Closure;
use Illuminate\Support\Collection;

interface AssetRepositoryContract
{
    /**
     * Create new record fro a model.
     *
     * @param AssetableContract $model
     * @param string $type
     * @param UploadedAsset $file
     * @param array $variants
     * @param Closure|null $process
     * @return Asset
     */
    public function create(
        AssetableContract $model,
        string $type,
        UploadedAsset $file,
        array $variants = [],
        Closure $process = null
    ): Asset;

    /**
     * Get next sort for a type.
     *
     * @param string $type
     * @return int
     */
    public function nextSortFor(AssetableContract $model, string $type): int;

    /**
     * @param Collection|Asset $assets
     */
    public function remove($assets): void;
}
