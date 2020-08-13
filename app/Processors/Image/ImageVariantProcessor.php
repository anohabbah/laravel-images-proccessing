<?php


namespace App\Processors\Image;


use App\Assets\UploadedAsset;
use App\Assets\Variant;
use App\Processors\Image\Breakpoints\Breakpoint;
use App\Processors\Image\Breakpoints\Large;
use App\Processors\Image\Breakpoints\Medium;
use App\Processors\Image\Breakpoints\Small;
use App\Processors\Image\Breakpoints\XLarge;
use Closure;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use InvalidArgumentException;

class ImageVariantProcessor
{
    /**
     * @var ImageManager
     */
    private ImageManager $manager;

    /**
     * ImageVariantProcessor constructor.
     * @param ImageManager $manager
     */
    public function __construct(ImageManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param UploadedAsset $file
     * @param array $variants
     * @param Closure|null $process
     * @return array
     */
    public function generateVariants(UploadedAsset $file, array $variants = [], Closure $process = null): array
    {
        if (empty($variants) || $file->mime == 'image/svg+xml') {
            return $this->generateSoleVariants($file);
        }

        return $this->flatVariants($variants, function (Breakpoint $breakpoint) use ($file, $process) {
            return $this->process($breakpoint, $file, $process);
        });
    }

    /**
     * Generates sole variants using main file for all.
     *
     * @param UploadedAsset $file
     * @return array
     */
    private function generateSoleVariants(UploadedAsset $file): array
    {
        $variant = new Variant($file->path, $file->disk);

        return $this->flatVariants(
            [new Small(), new Medium(), new Large(), new XLarge()],
            function () use ($variant) {
                return $variant;
            }
        );
    }

    /**
     * Parses variants.
     *
     * @param array $variants
     * @param Closure $process
     * @return array
     */
    private function flatVariants(array $variants, Closure $process): array
    {
        return collect($variants)->flatMap(function (Breakpoint $breakpoint) use ($process) {
            return [$breakpoint->index() => $process($breakpoint)];
        })->toArray();
    }

    /**
     * Runs process
     * @param Breakpoint $breakpoint
     * @param UploadedAsset $file
     * @param Closure|null $process
     * @return Variant
     */
    private function process(Breakpoint $breakpoint, UploadedAsset $file, ?Closure $process): Variant
    {
        $callback = !is_null($breakpoint->process) ? $breakpoint->process : $process;

        if (is_null($callback)) {
            throw new InvalidArgumentException('Process is missing');
        }

        /** @var Image $source */
        $source = $callback($this->manager->make($file->path), $breakpoint);
        $path = $this->store($file, $breakpoint, $source->stream());
        return new Variant($path, $file->disk);
    }

    /**
     * Stores processed image.
     *
     * @param UploadedAsset $file
     * @param Breakpoint $breakpoint
     * @param \Psr\Http\Message\StreamInterface $stream
     * @return string
     */
    private function store(UploadedAsset $file, Breakpoint $breakpoint, \Psr\Http\Message\StreamInterface $stream): string
    {
        Storage::put(
            $filePath = $file->directory. ds() . $breakpoint->index() . '-' . $file->original_name,
            $stream,
            [
                'disk' => $file->disk,
                'visibility' => $file->visibility,
            ]
        );

        return $filePath;
    }
}
