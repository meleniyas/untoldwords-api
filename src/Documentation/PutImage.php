<?php

namespace App\Documentation;

use OpenApi\Attributes as OA;

class PutImage
{
    #[OA\Property(type: "string", example: "test-image-name")]
    public $name;
    #[OA\Property(type: "boolean", example: true)]
    public $isHome;
    #[OA\Property(type: "string", example: "works")]
    public $works;
}
