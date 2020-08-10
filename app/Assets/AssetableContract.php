<?php


namespace App\Assets;


use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * Interface AssetableContract
 * @package App\Assets
 *
 * @property Collection $assets
 */
interface AssetableContract
{
    /**
     * Get associated assets.
     *
     * @return MorphMany
     */
    public function assets(): MorphMany;
}
