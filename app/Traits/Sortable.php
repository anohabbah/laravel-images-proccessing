<?php


namespace App\Traits;


use Illuminate\Database\Eloquent\Builder;

/**
 * Trait Sortable
 * @package App\Traits
 *
 * @property int $sort
 *
 * @method sorted
 */
trait Sortable
{
    /**
     * Sorts records in ascending order.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeSorted(Builder $query): Builder
    {
        return $query->orderBy('sort');
    }
}
