<?php

namespace App\Http\Requests\Product;

use App\Assets\AssetUploadRequestContract;
use App\Assets\Requests\ImageUploadRequest;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest implements AssetUploadRequestContract
{
    use ImageUploadRequest;

    /**
     * @inheritDoc
     */
    public function visibility(): string
    {
        return Filesystem::VISIBILITY_PUBLIC;
    }
}
