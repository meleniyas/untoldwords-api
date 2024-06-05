<?php

namespace App\Listener;

use App\Entity\User;
use App\Service\Security;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestListener
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws Exception
     */
    public function onKernelRequest(RequestEvent $event): ?RequestEvent
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        $params = $request->attributes->get('_route_params');
        $routeArray = explode('_', $route);

        if (count($routeArray) !== null && count($routeArray) > 1) {
            $route = $routeArray[1];

            if ($route === "private") {

                $token = (string)$request->headers->get('authorization', null);
                if (!Security::validateToken($token)) {
                    $event->setResponse(new JsonResponse(['message' => 'Token no valido'], 401));
                    return null;
                }

                $data = Security::decodeToken($token);
                $user = $this->em->getRepository(User::class)->findOneBy(
                    ['id' => $data['user_id'], 'is_removed' => false]
                );
                if (!$user instanceof User) {
                    $event->setResponse(new JsonResponse(['message' => 'Usuario no valido'], 401));
                    return null;
                }

                $request->attributes->set('user', $user);
            }
        }

        return null;
    }
}
