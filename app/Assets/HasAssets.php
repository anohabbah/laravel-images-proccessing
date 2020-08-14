<?php


namespace App\Assets;

use App\Asset;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasAssets
{
    /**
     * @inheritdoc
     */
    public function assets(): MorphMany
    {
        return $this->morphMany(Asset::class, 'assetable');
    }
}
