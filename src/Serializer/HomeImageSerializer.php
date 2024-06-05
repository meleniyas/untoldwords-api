<?php

namespace App\Serializer;

use App\Entity\homeImage;

class HomeImageSerializer
{
    public static function homeImage(HomeImage $homeImage): array
    {
        $image = $homeImage->getImage();
        return [
            'id' => $homeImage->getId(),
            'image' => ImageSerializer::image($image),
            'position' => $homeImage->getPosition()
        ];
    }
}
