<?php

namespace App\Documentation;

use OpenApi\Attributes as OA;

class PutWork
{

    #[OA\Property(type: "string", example: "test-name")]
    public $name;
    #[OA\Property(type: "string", example: "test-description")]
    public $description;
    #[OA\Property(type: "string", example: "test-architects")]
    public $architects;
    #[OA\Property(type: "int", example: 1)]
    public $status;
}
