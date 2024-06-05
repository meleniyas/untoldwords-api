<?php

namespace App\Constraints;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

class WorkConstraint
{
    public static function work(): Collection
    {
        return new Assert\Collection([
            'name' => [new Assert\NotBlank(), new Assert\Type('string')],
            'architects' => [new Assert\Type('string')],
            'description' => [new Assert\Type('string')],
        ]);
    }
    public static function workUpdate(): Collection
    {
        return new Assert\Collection([
            'name' => [new Assert\NotBlank(), new Assert\Type('string')],
            'architects' => [new Assert\Type('string')],
            'description' => [new Assert\Type('string')],
            'status' => [new Assert\Type('numeric')],
        ]);
    }
    public static function workUpdateOrder(): Collection
    {
        return new Assert\Collection([
            'works' => new Assert\All([
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
