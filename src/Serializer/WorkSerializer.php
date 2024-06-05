<?php

namespace App\Serializer;

use App\Entity\Work;

class WorkSerializer
{
    public static function works(array $works): array
    {
        $worksReturned = [];

        foreach ($works as $work) {
            $worksReturned[] = WorkSerializer::work($work);
        }
        return $worksReturned;
    }
    public static function work(Work $work): array
    {
        $workImages = [];
        $workImageCollection = $work->getWorkImages()->toArray();

        // Aqui ordenamos por position
        usort($workImageCollection, function ($a, $b) {
            return $a->getPosition() <=> $b->getPosition();
        });

        foreach ($workImageCollection as $workImage) {

            $workImages[] = WorkImageSerializer::workImage($workImage);
        }

        return [
            'id' => $work->getId(),
            'position' => $work->getPosition(),
            'name' => $work->getName(),
            'architects' => $work->getArchitects(),
            'description' => $work->getDescription(),
            'status' => $work->getStatus(),
            'workImages' => $workImages,
        ];
    }

    public static function workMin(Work $work): array
    {
        return [
            'id' => $work->getId(),
            'position' => $work->getPosition(),
            'name' => $work->getName(),
        ];
    }
}
