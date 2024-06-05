<?php

namespace App\Documentation;

use OpenApi\Attributes as OA;

class PostHomeImage
{
    #[OA\Property(type: "string", example: "test-image")]
    public $name;
    #[OA\Property(type: "string", format: "binary", description: "Archivo de imagen")]
    public $image;
}
