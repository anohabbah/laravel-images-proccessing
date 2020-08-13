<?php


namespace App\Assets;


use Illuminate\Http\UploadedFile;

class UploadedAsset
{
    /**
     * @var UploadedFile
     */
    public UploadedFile $file;

    /** @var string $path */
    public string $path;

    /** @var string $disk */
    public string $disk;

    /** @var string $visibility */
    public string $visibility;

    /** @var int $size */
    public int $size;

    /** @var string $extension */
    public string $extension;

    /** @var string|null $mime */
    public ?string $mime;

    /** @var string|null $original_name */
    public ?string $original_name;

    /** @var mixed|string $filename */
    public string $filename;

    /** @var mixed|string $directory */
    public string $directory;

    /**
     * @var bool $isImage
     */
    public bool $isImage;

    /**
     * UploadedAsset constructor.
     *
     * @param UploadedFile $file
     * @param $path
     * @param string $disk
     * @param string $visibility
     */
    public function __construct(UploadedFile $file, string $path, string $disk, string $visibility)
    {
        $this->file = $file;
        $this->path = $path;
        $this->disk = $disk;
        $this->size = $file->getSize();
        $this->visibility = $visibility;
        $this->extension = $file->extension();
        $this->mime = $file->getClientMimeType();
        $this->original_name = $file->getClientOriginalName();

        $info = pathinfo($this->path);
        $this->filename = $info['basename'];
        $this->directory = $info['dirname'];

        $this->isImage = strpos($this->mime ?? '', 'image/') === 0;
    }
}
