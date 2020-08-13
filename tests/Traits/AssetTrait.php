<?php


namespace Tests\Traits;


use App\Asset;
use App\Processors\Image\Breakpoints\Breakpoint;
use App\Processors\Image\Breakpoints\Large;
use App\Processors\Image\Breakpoints\Medium;
use App\Processors\Image\Breakpoints\Small;
use App\Processors\Image\Breakpoints\XLarge;
use Illuminate\Support\Collection;

trait AssetTrait
{
    /**
     * Get asset preload.
     *
     * @param Asset $asset
     * @return array
     */
    protected function preload(Asset $asset): array
    {
        if (empty($asset->variants)) {
            return [];
        }

        return collect($asset->variants)
            ->map(function ($variant) {
            return $variant['url'];
            })
            ->merge([$asset->url])
            ->values()
            ->all();
    }

    /**
     * Get asset variants.
     *
     * @param Asset $asset
     * @param bool $same
     * @return Collection
     */
    protected function variants(Asset $asset, bool $same = false): Collection
    {
        if (empty($asset->variants)) {
            return new Collection();
        }

        $method = $same ? 'sameVariant' : 'prefixVariant';

        return collect([new Small(), new Medium(), new Large(), new XLarge()])
            ->flatMap(function (Breakpoint $breakpoint) use ($asset, $method) {
                return [
                    $breakpoint->index() => $this[$method]($asset, $breakpoint)
                ];
            });
    }

    /**
     * Get variant with the path to the original file.
     *
     * @param Asset $asset
     * @return array
     */
    private function sameVariant(Asset $asset): array
    {
        return [
            'path' => $asset->path,
            'url' => $asset->url,
        ];
    }

    /**
     * Get breakpoint prefixed variant.
     *
     * @param Asset $asset
     * @param Breakpoint $breakpoint
     * @return array
     */
    private function prefixVariant(Asset $asset, Breakpoint $breakpoint): array
    {
        $path = pathinfo($asset->path);
        $url = pathinfo($asset->url);
        $file = $breakpoint->index() . '-' . $path['basename'];

        return [
            'path' => $path['dirname'] . '/' . $file,
            'url' => $url['dirname'] . '/' . $file,
        ];
    }
}
