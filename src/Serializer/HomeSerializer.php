<?php

namespace App\Serializer;

use App\Entity\Home;
use App\Entity\HomeImage;

class HomeSerializer
{
    public static function home(Home $home, array $homeImages): array
    {
        $hi = [];
        // Aqui ordenamos por position
        usort($homeImages, function ($a, $b) {
            return $a->getHomePosition() <=> $b->getHomePosition();
        });

        foreach ($homeImages as $image) {
            $hi[] = ImageSerializer::image($image);
        }

        return [
            'id' => $home->getId(),
            'timer' => $home->getTimer(),
            'homeImages' => $hi
        ];
    }
}
