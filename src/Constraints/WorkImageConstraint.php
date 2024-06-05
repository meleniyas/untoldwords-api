<?php

namespace App\Constraints;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

class WorkImageConstraint
{
    public static function workImage(): Collection
    {
        return new Assert\Collection([
            'name' => [new Assert\NotBlank(), new Assert\Type('string')],
            'description' => [new Assert\NotBlank(), new Assert\Type('string')],
        ]);
    }
    public static function workImageUpdate(): Collection
    {
        return new Assert\Collection([
            'status' => [new Assert\NotBlank(), new Assert\Type('numeric')],
        ]);
    }
    public static function workImageUpdateOrder(): Collection
    {
        return new Assert\Collection([
            'workImages' => new Assert\All([
                'constraints' => [
                    new Assert\Collection([
                        'id' => [new Assert\NotBlank(), new Assert\Type('string')],
                        'position' => [new Assert\NotBlank(), new Assert\Type('numeric')],
                    ])
                ]
            ])
        ]);
    }
}
