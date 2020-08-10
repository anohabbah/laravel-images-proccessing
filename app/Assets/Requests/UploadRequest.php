<?php


namespace App\Assets\Requests;


trait UploadRequest
{
    /**
     * @inheritDoc
     */
    public function field(): string
    {
        return 'file';
    }

    /**
     * @inheritDoc
     */
    public function disk(): string
    {
        return config('filesystems.default');
    }
}
