<?php

namespace App\Documentation;

use OpenApi\Attributes as OA;

class PutHomeImage
{
    #[OA\Property(type: "string", example: "1234-5678-90")]
    public $homeImageId;
    #[OA\Property(type: "integer", example: 0)]
    public $position;
}
