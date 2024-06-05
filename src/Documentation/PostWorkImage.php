<?php

namespace App\Documentation;

use OpenApi\Attributes as OA;

class PostWorkImage
{
    #[OA\Property(type: "string", example: "test-work-image")]
    public $name;
    #[OA\Property(type: "string", format: "binary", description: "Archivo de imagen")]
    public $image;
}
