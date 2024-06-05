<?php

namespace App\Controller\Api;



use App\Controller\RestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Image;
use App\Entity\Work;
use App\Entity\Home;
use App\Serializer\HomeSerializer;
use App\Serializer\WorkSerializer;


use OpenApi\Attributes as OA;


#[Route('api/public', name: 'api_public_')]
#[OA\Tag(name: 'PUBLIC')]
class PublicController extends RestController
{
    #[Route('/home', methods: ["GET"])]
    public function getHome(): JsonResponse
    {
        $home = $this->getHomeFromRepository();
        $homeImages = $this->getHomeImagesFromRepository();

        return $this->json(['home' => HomeSerializer::home($home, $homeImages)]);
    }

    #[Route('/works', methods: ["GET"])]
    public function getWorkList(): JsonResponse
    {
        $works = $this->em->getRepository(Work::class)->findBy([], ['position' => 'ASC']);
        if (empty($works)) {
            return $this->json(
                ['Error' => ['No se han encontrado trabajos']],
                418
            );
        }
        return $this->json(['works' => WorkSerializer::works($works)]);
    }

    // Utilities

    private function getHomeFromRepository(): Home | JsonResponse
    {
        $home = $this->em->getRepository(Home::class)
            ->createQueryBuilder('h')
            ->where('h.id IS NOT NULL')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$home instanceof Home) {
            return $this->json(
                ['Error' => ['No se ha inicializado el HOME']],
                418
            );
        }
        return $home;
    }

    private function getHomeImagesFromRepository(): ?array
    {

        $homeImages = $this->em->getRepository(Image::class)
            ->createQueryBuilder('i')
            ->where('i.homePosition IS NOT NULL')
            ->getQuery()
            ->getResult();


        return $homeImages;
    }
}
