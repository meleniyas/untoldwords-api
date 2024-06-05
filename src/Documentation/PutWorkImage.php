<?php

namespace App\Documentation;

use OpenApi\Attributes as OA;

class PutWorkImage
{
    #[OA\Property(type: "number", example: 0)]
    public $status;
}
