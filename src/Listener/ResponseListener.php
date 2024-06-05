<?php

namespace App\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use Symfony\Component\Uid\Uuid;

class ResponseListener
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @throws \JsonException
     */
    public function onKernelResponse(ResponseEvent $event): ResponseEvent
    {
        $response = $event->getResponse();
        $request = $event->getRequest();
        $user = $request->attributes->get('user');


        if ($response->headers->get('Content-Type') === 'application/json') {
            $content = [
                'data' => json_decode((string)$response->getContent(), true, 512, JSON_THROW_ON_ERROR),
                'requestId' => Uuid::v4()
            ];

            $response->setContent((string)json_encode($content, JSON_THROW_ON_ERROR));
            $event->setResponse($response);
        }

        return $event;
    }

    /**
     * @throws \JsonException
     */
    public function onKernelException(ExceptionEvent $event): ExceptionEvent
    {
        $response = $event->getThrowable() instanceof BadRequestHttpException ? json_decode($event->getThrowable()->getMessage(), true, 512, JSON_THROW_ON_ERROR) : ['message' => [$event->getThrowable()->getMessage()]];
        $event->setResponse(new JsonResponse($response));
        return $event;
    }
}
