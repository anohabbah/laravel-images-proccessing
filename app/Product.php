<?php

namespace App;

use App\Assets\AssetableContract;
use App\Assets\HasAssets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Product
 *
 * @package App
 *
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Product extends Model implements AssetableContract
{
    use HasAssets;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
}
