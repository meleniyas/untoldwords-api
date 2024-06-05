<?php

namespace App\Constraints;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

class ImageConstraint
{
    public static function getItem(): Collection
    {
        return new Assert\Collection([
            'name' => [new Assert\NotBlank(), new Assert\Type('string')]
        ]);
    }
    public static function update(): Collection
    {
        return new Assert\Collection([
            'name' => [new Assert\Type('string')],
            'isHome' => [new Assert\Type('boolean')],
            'works' => new Assert\All([
                'constraints' => [
                    new Assert\Collection([
                        'id' => [new Assert\NotBlank(), new Assert\Type('string')],
                        'name' => [new Assert\Type('string')],
                    ])
                ]
            ])
        ]);
    }
}
