<?php

namespace App\Documentation;

use OpenApi\Attributes as OA;

class DeleteHomeImage
{
    #[OA\Property(type: "string", example: "1234-5678-90")]
    public $homeImageId;
}
