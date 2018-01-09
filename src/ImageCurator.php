<?php

namespace Digbang\Files;

use Intervention\Image\Constraint;
use Intervention\Image\Image as InterventionImage;

class ImageCurator
{
    private $stack = [];

    public function resize(int $width = null, int $height = null, bool $constraintAspectRatio = true, bool $preventUpsizing = true): ImageCurator
    {
        $this->stack[] = function(InterventionImage $image) use ($width, $height, $constraintAspectRatio, $preventUpsizing) {
            $image->resize($width, $height, function (Constraint $constraint) use ($constraintAspectRatio, $preventUpsizing) {
                if ($constraintAspectRatio) {
                    $constraint->aspectRatio();
                }

                if ($preventUpsizing) {
                    $constraint->upsize();
                }
            });
        };

        return $this;
    }

    public function interlace(): ImageCurator
    {
        $this->stack[] = function(InterventionImage $image) {
            $image->interlace();
        };

        return $this;
    }

    /**
     * @param string $format
     *      jpg — return JPEG encoded image data
     *      png — return Portable Network Graphics (PNG) encoded image data
     *      gif — return Graphics Interchange Format (GIF) encoded image data
     *      tif — return Tagged Image File Format (TIFF) encoded image data
     *      bmp — return Bitmap (BMP) encoded image data
     *      ico — return ICO encoded image data
     *      psd — return Photoshop Document (PSD) encoded image data
     *      webp — return WebP encoded image data
     *      data-url — encode current image data in data URI scheme (RFC 2397)
     * @param int $quality
     *      0 to 100, Only useful if format is "jpg".
     * @return ImageCurator
     */
    public function encode(string $format, int $quality = 90): ImageCurator
    {
        $this->stack[] = function(InterventionImage $image) use ($format, $quality) {
            $image->encode($format, $quality);
        };

        return $this;
    }

    public function optimize(): ImageCurator
    {
        //TODO: Implement this...

        return $this;
    }

    public function buildCuration(): Curation
    {
        $curation = new Curation($this->stack);

        $this->stack = [];

        return $curation;
    }
}
