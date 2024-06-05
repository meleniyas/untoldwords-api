<?php

namespace App\Serializer;

use App\Entity\User;

class UserSerializer
{
    public static function user(User $user): array
    {

        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'lastName1' => $user->getLastName1(),
            'lastName2' => $user->getLastName2(),
            'email' => $user->getEmail(),
            'rol' => $user->getRol(),
        ];
    }
}
