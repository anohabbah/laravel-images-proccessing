<?php


namespace App\Assets\Requests;


use App\Assets\Type\Image;

trait ImageUploadRequest
{
    use UploadRequest;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            $this->field() => ['required', 'image', 'max:' . config('filesystems.max_size')]
        ];
    }

    /**
     * @inheritDoc
     */
    public function directory(): string
    {
        return Image::directory();
    }
}
