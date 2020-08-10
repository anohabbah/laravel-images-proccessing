<?php

namespace App;

use App\Assets\AssetableContract;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Class Asset
 * @package App
 *
 * @property int $id
 * @property int $assetable_id
 * @property string $assetable_type
 * @property string $type
 * @property string $disk
 * @property string $visibility
 * @property string $path
 * @property string $original_name
 * @property string $mime
 * @property string $extension
 * @property int $size
 * @property string|null $caption
 * @property array $variants
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
* @property AssetableContract $assetable
 * @property string $url
 */
class Asset extends Model
{
    use Sortable;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'assetable_id', 'assetable_type', 'type', 'disk', 'visibility', 'sort', 'path',
        'original_name', 'extension', 'mime', 'size', 'caption', 'variants',
    ];

    /**
     * @inheritdoc
     */
    protected $casts = ['variants' => 'array'];

    /**
     * @inheritdoc
     */
    protected $appends = ['url'];

    /**
     * Get assetable model.
     *
     * @return MorphTo
     */
    public function assetable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get url attribute.
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}
