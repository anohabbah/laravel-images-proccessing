<?php


namespace App\Assets;

use Illuminate\Http\UploadedFile;

interface AssetUploadRequestContract
{
    /**
     * Get field name.
     *
     * @return string
     */
    public function field(): string;

    /**
     * Get directory name.
     *
     * @return string
     */
    public function directory(): string;

    /**
     * Get disk to use name.
     *
     * @return string
     */
    public function disk(): string;

    /**
     * Get file visibility.
     *
     * @return string
     */
    public function visibility(): string;

    /**
     * Retrieve a file from request.
     * @param string|null $key
     * @param string|null $default
     * @return UploadedFile|UploadedFile[]|array|null
     */
    public function file(string $key = null, string $default = null);
}
