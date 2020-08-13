<?php


namespace App\Processors\Image\Breakpoints;


class Small extends Breakpoint
{
    /**
     * @inheritdoc
     */
    public function index(): string
    {
        return 'sm';
    }
}
