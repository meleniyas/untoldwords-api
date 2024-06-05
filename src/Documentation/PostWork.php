<?php

namespace App\Documentation;

use OpenApi\Attributes as OA;

class PostWork
{
    #[OA\Property(type: "string", example: "work-name")]
    public $name;
    #[OA\Property(type: "string", example: "work-description")]
    public $description;
}
