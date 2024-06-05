<?php

namespace App\Constraints;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

class HomeConstraint
{
    public static function timer(): Collection
    {
        return new Assert\Collection([
            'timer' => [new Assert\NotBlank(), new Assert\Type('numeric')]
        ]);
    }

    public static function home(): Collection
    {
        return new Assert\Collection([
            'timer' => [new Assert\NotBlank(), new Assert\Type('numeric')],
            'homeImages' => new Assert\All([
                'constraints' => [
                    new Assert\Collection([
                        'homeImageId' => [new Assert\NotBlank(), new Assert\Type('string')],
                        'position' => [new Assert\NotBlank(), new Assert\Type('numeric')],
                    ])
                ]
            ])
        ]);
    }
    public static function homeImages(): Collection
    {
        return new Assert\Collection([
            'homeImages' => new Assert\All([
                'constraints' => [
                    new Assert\Collection([
                        'homeImageId' => [new Assert\NotBlank(), new Assert\Type('string')],
                        'position' => [new Assert\NotBlank(), new Assert\Type('numeric')],
                    ])
                ]
            ])
        ]);
    }
    public static function homeImage(): Collection
    {
        return new Assert\Collection([
            'homeImageId' => [new Assert\NotBlank(), new Assert\Type('string')]
        ]);
    }
}
