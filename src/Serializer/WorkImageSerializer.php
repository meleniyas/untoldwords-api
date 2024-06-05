<?php

namespace App\Serializer;

use App\Entity\WorkImage;

class WorkImageSerializer
{
    public static function workImage(WorkImage $workImage): array
    {
        $image = $workImage->getImage();
        return [
            'id' => $workImage->getId(),
            'position' => $workImage->getPosition(),
            'status' => $workImage->getStatus(),
            'workId' => $workImage->getWork()->getId(),
            'image' => ImageSerializer::image($image)

        ];
    }
}
