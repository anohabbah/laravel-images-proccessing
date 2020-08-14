<?php


namespace App\Processors\Image\Breakpoints;

class Medium extends Breakpoint
{
    /**
     * @inheritdoc
     */
    public function index(): string
    {
        return 'md';
    }
}
