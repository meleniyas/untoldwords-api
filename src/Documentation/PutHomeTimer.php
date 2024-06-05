<?php

namespace App\Documentation;

use OpenApi\Attributes as OA;

class PutHomeTimer
{
    #[OA\Property(type: "int", example: "2")]
    public $timer;
}
