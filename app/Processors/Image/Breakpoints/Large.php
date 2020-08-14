<?php


namespace App\Processors\Image\Breakpoints;

class Large extends Breakpoint
{
    /**
     * @inheritdoc
     */
    public function index(): string
    {
        return 'lg';
    }
}
