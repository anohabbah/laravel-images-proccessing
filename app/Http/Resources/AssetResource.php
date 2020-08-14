<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class AssetResource
 * @package App\Http\Resources
 *
 * @property int $id
 * @property string $original_name
 * @property string $extension
 * @property string $url
 * @property array $variants
 */
class AssetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->original_name,
            'url' => $this->url,
            'extension' => $this->extension,
            'variants' => $this->variants,
            'preload' => $this->preload(),
        ];
    }

    /**
     * Get preload urls.
     *
     * @return array
     */
    private function preload(): array
    {
        if (empty($this->variants)) {
            return [];
        }
        $urls = array_merge([$this->url], array_map(function (array $variant) {
            return $variant['url'];
        }, $this->variants));

        return array_unique(array_values($urls));
    }
}
