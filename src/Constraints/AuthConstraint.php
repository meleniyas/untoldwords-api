<?php

namespace App\Constraints;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

class AuthConstraint
{

    public static function login(): Collection
    {
        return new Assert\Collection([
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'password' => [new Assert\NotBlank(), new Assert\Type('string')],
        ]);
    }
}
