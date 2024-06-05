<?php

namespace App\Controller\Api;

use DateTime;

use App\Controller\RestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use App\Serializer\UserSerializer;
use App\Service\Security;
use App\Util\Constants;
use App\Constraints\AuthConstraint;
use OpenApi\Annotations\JsonContent;
use OpenApi\Attributes as OA;
use RuntimeException;

#[Route('api/public', name: 'api_public_')]
#[OA\Tag(name: 'AUTHORIZATION')]
#[OA\Response(
    response: 400,
    description: 'request with errors'
)]
#[OA\Response(
    response: 418,
    description: 'message => string'
)]
class AuthController extends RestController
{

    #[Route('/user-login', name: 'user-login', methods: ["POST"])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(
                    property: "email",
                    type: "string",
                    example: "email@email.com"
                ),
                new OA\Property(
                    property: "password",
                    type: "string",
                    example: "password"
                )
            ]
        )
    )]
    public function userlogin(): JsonResponse
    {
        $data = $this->getBody();
        $this->validate($data, AuthConstraint::login());

        $user = $this->em->getRepository(User::class)->findOneBy([
            'email' => $data['email'],
            'rol'   => Constants::USER_ROLES['ADMIN'],
            'is_removed' => false
        ]);

        if (!$user instanceof User) {
            return $this->json(
                ['Acceso denegado' => ['Usuario o contraseña incorrectos']],
                418
            );
        }

        if ($user->getStatus() === Constants::USER_STATUS['NOT_ACTIVE']) {
            return $this->json(
                ['Acceso denegado' => ['Usuario bloqueado']],
                418
            );
        }

        if (!password_verify($data['password'], $user->getPassword())) {
            return $this->json(
                ['Acceso denegado' => ['Usuario o contraseña incorrectos']],
                418
            );
        }

        return $this->authResponse($user);
    }


    ## Utilities ## 

    private function authResponse(User $user): JsonResponse
    {
        $response = [
            'user' => UserSerializer::user($user),
            'token' => Security::encodeToken($user->getId())
        ];

        $this->request->attributes->set('user', $user);
        return $this->json($response);
    }
}
