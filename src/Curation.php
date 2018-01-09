<?php

namespace Digbang\Files;

use Intervention\Image\Facades\Image;
use Intervention\Image\Image as InterventionImage;

class Curation
{
    /** @var array */
    private $actions;

    public function __construct(array $actions)
    {
        $this->actions = $actions;
    }

    public function apply($image): InterventionImage
    {
        if (!$image instanceof InterventionImage) {
            $image = Image::make($image);
        }

        foreach ($this->actions as $action) {
            $action($image);
        }

        return $image;
    }
}
