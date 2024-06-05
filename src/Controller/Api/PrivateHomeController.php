<?php

namespace App\Controller\Api;

use App\Controller\RestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

use App\Entity\Image;
use App\Entity\Home;
use App\Serializer\HomeSerializer;

use App\Constraints\HomeConstraint;

#[Route('api/private/home', name: 'api_private_home_')]
#[OA\Tag(name: 'HOME')]
#[OA\Response(
    response: 200,
    description: 'success'
)]
#[OA\Response(
    response: 400,
    description: 'request with errors'
)]
#[OA\Response(
    response: 418,
    description: 'message => string'
)]
class PrivateHomeController extends RestController
{
    #[Route(methods: ["GET"])]
    public function getHome(): JsonResponse
    {
        $home = $this->getHomeFromRepository();
        $homeImages = $this->getHomeImagesFromRepository();

        return $this->json(['home' => HomeSerializer::home($home, $homeImages)]);
    }

    #[Route('/timer', name: 'timer', methods: ["PUT"])]
    #[OA\RequestBody(
        required: true,
        description: 'Actualiza la transición de imagenes en Home',
        content: new OA\JsonContent(ref: "#/components/schemas/PutHomeTimer")
    )]
    public function putTimer(): JsonResponse
    {
        $data = $this->getBody();
        $this->validate($data, HomeConstraint::timer());

        $home = $this->getHomeFromRepository();
        $homeImages = $this->getHomeImagesFromRepository();
        $home->setTimer($data['timer']);

        $this->em->persist($home);
        $this->em->flush();

        return $this->json([HomeSerializer::home($home, $homeImages)]);
    }

    #[Route('/order', name: 'order', methods: ["PUT"])]
    #[OA\RequestBody(
        required: true,
        description: 'Actualiza el orden de las imagenes en Home',
        content: new OA\JsonContent(ref: "#/components/schemas/PutHomeOrder")
    )]
    public function putOrder(): JsonResponse
    {
        $data = $this->getBody();
        $home = $this->getHomeFromRepository();

        $this->validate($data, HomeConstraint::homeImages());
        $homeImagesReference = $data['homeImages'];

        if (!empty($homeImagesReference)) {
            foreach ($homeImagesReference as $homeImageRef) {
                $repository = $this->em->getRepository(Image::class);
                $im = $repository->findOneBy([
                    'id' => $homeImageRef['homeImageId']
                ]);
                if ($im instanceof Image) {
                    $im->setHomePosition($homeImageRef['position']);
                    $this->em->persist($im);
                }
            }
            $this->em->flush();
        }

        $homeImages = $this->getHomeImagesFromRepository();

        return $this->json([HomeSerializer::home($home, $homeImages)]);
    }

    #[Route('/image', name: 'imagePost', methods: ["POST"])]
    #[OA\RequestBody(
        required: true,
        description: 'Añade una imagen en home',
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(ref: "#/components/schemas/PostHomeImage")
        )
    )]

    public function postImage(): JsonResponse
    {

        $name = $this->request->request->get('name');
        $imageFile = $this->request->files->get('image');

        if ($imageFile && $imageFile->isValid()) {

            $home = $this->getHomeFromRepository();
            $homeImages = $this->getHomeImagesFromRepository();
            $position = count($homeImages);

            $imageData = file_get_contents($imageFile->getRealPath());

            $image = new Image();
            $image->setName($name);
            $image->setContent($imageData);
            $image->setHomePosition($position);

            $this->em->persist($image);

            $this->em->flush();

            return $this->json([HomeSerializer::home($home, $homeImages)]);
        }
        return new JsonResponse(['error' => 'Invalid image file'], 400);
    }

    #[Route('/{id}', methods: ["DELETE"])]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "Id de la image"
    )]
    public function removeImage($id): JsonResponse
    {
        $home = $this->getHomeFromRepository();
        $homeImage = $this->em->getRepository(Image::class)->findOneBy([
            'id' => $id
        ]);

        if ($homeImage instanceof Image) {
            $homeImage->setHomePosition(null);
            $this->em->persist($homeImage);
            $this->em->flush();

            // Recalcular las posiciones
            $homeImages = $this->getHomeImagesFromRepository();
            usort($homeImages, function ($a, $b) {
                return $a->getHomePosition() <=> $b->getHomePosition();
            });

            foreach ($homeImages as $index => $image) {
                $image->setHomePosition($index);
                $this->em->persist($image);
            }

            $this->em->flush();
            return $this->json([
                'message' => 'Imagen Eliminada del Home',
                'home' => HomeSerializer::home($home, $homeImages)
            ]);
        }


        return $this->json([
            'message' => 'No se ha podido eliminar la imagen'
        ]);
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
