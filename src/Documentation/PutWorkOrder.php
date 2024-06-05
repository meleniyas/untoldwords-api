<?php

namespace App\Documentation;

use OpenApi\Attributes as OA;

class PutWorkOrder
{
    #[OA\Property(
        type: "array",
        items: new OA\Items(
            type: "object",
            properties: [
                new OA\Property(property: "id", type: "string", example: "1234-5678-90"),
                new OA\Property(property: "position", type: "integer", example: 0)
            ]
        )
    )]
    public array $works;
}
