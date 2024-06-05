<?php

namespace App\Serializer;

use App\Entity\Image;

class ImageSerializer
{
    public static function images(array $images): array
    {
        $imagesReturned = [];

        foreach ($images as $image) {
            $imagesReturned[] = ImageSerializer::image($image);
        }
        return [
            'images' => $imagesReturned
        ];
    }
    public static function image(Image $image): array
    {

        $wir = [];
        $workImage = $image->getWorkImage()->toArray();

        foreach ($workImage as $wi) {
            $wir[] = [
                'id' => $wi->getId(),
                'work' => WorkSerializer::workMin($wi->getWork()),
            ];
        }


        $content = $image->getContent();

        if (is_resource($content)) {
            $imageContent = stream_get_contents($content);
        } elseif (is_string($content)) {
            $imageContent = $content;
        }

        $base64Content = base64_encode($imageContent);

        return [
            'id' => $image->getId(),
            'name' => $image->getName(),
            'homePosition' => $image->getHomePosition(),
            'works' => $wir,
            'image' => $base64Content,
        ];
    }
}
