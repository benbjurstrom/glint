<?php

namespace BenBjurstrom\Glint;

use Exception;
use GdImage;
use kornrunner\Blurhash\Blurhash;

class Blur
{
    /**
     * @throws Exception
     */
    public static function getHashFromString(string $fileContents): string
    {
        $image = imagecreatefromstring($fileContents);
        if (! $image) {
            throw new Exception('Could not create image from string');
        }

        $image = self::resizeImage($image);

        $width = imagesx($image);
        $height = imagesy($image);

        $pixels = [];
        for ($y = 0; $y < $height; $y++) {
            $row = [];
            for ($x = 0; $x < $width; $x++) {
                $index = imagecolorat($image, $x, $y);
                if (! is_int($index)) {
                    throw new Exception('Could not get index from imagecolorat');
                }
                $colors = imagecolorsforindex($image, $index);

                $row[] = [$colors['red'], $colors['green'], $colors['blue']];
            }
            $pixels[] = $row;
        }

        $componentsX = 4;
        $componentsY = 4;

        $hash = Blurhash::encode($pixels, $componentsX, $componentsY);

        return $hash.$width.$height;
    }

    public static function resizeImage(GdImage $image): GdImage
    {
        $maxWidth = 64;
        $width = imagesx($image);
        if ($width > $maxWidth) {
            $image = imagescale($image, $maxWidth);
            if (! $image) {
                throw new Exception('Could not create imagescale');
            }
        }

        return $image;
    }
}
