<?php


namespace App\Assets;

use Illuminate\Support\Facades\Storage;

class Variant
{
    public string $path;
    public string $url;

    /**
     * Variant constructor.
     * @param string $path
     * @param string|null $disk
     */
    public function __construct(string $path, string $disk = null)
    {
        $this->path = $path;
        $this->url = Storage::disk($disk)->url($path);
    }
}
