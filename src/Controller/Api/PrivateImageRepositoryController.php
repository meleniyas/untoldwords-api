<?php

namespace App\Controller\Api;

use App\Entity\HomeImage;
use DateTime;

use App\Controller\RestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

use App\Entity\Image;
use App\Entity\Work;
use App\Entity\WorkImage;
use App\Serializer\ImageSerializer;

use App\Constraints\ImageConstraint;

#[Route('api/private/repository', name: 'api_private_repository_')]
#[OA\Tag(name: 'IMAGE REPOSITORY')]
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
class PrivateImageRepositoryController extends RestController
{
    #[Route(methods: ["GET"])]
    public function getImages(): JsonResponse
    {
        $images = $this->em->getRepository(Image::class)->findAll();

        if (empty($images)) {
            return $this->json(
                ['error' => 'No hay imagenes en el repositorio'],
                418
            );
        }

        return $this->json(['images' => ImageSerializer::images($images)]);
    }

    #[Route('/{id}', methods: ["PUT"])]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "Id de la imagen"
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Actualiza el estado de la imagen',
        content: new OA\JsonContent(ref: "#/components/schemas/PutImage")
    )]

    public function putWorkImage(string $id): JsonResponse
    {
        $data = $this->getBody();
        $homeImages = $this->getHomeImagesFromRepository();

        $this->validate($data, ImageConstraint::update());
        $image = $this->em->getRepository(Image::class)->findOneBy(
            [
                'id' => $id
            ]
        );
        if (!$image instanceof Image) {
            return $this->json(
                ['Error' => ['No se ha encontrado la imagen']],
                418
            );
        }


        $image->setName($data['name']);
        if ($data['isHome']) {
            $position = count($homeImages);
            $image->setHomePosition($position);
        }
        $imageWorkImages = $image->getWorkImage()->toArray();
        $imageWorkIds = array_map(function ($workImage) {
            return $workImage->getWork()->getId();
        }, $imageWorkImages);

        $worksUpdate = $data['works'];
        $worksUpdateIds = array_column($worksUpdate, 'id');

        // 1. Elementos iguales (IDs)
        $iguales = array_filter($worksUpdateIds, function ($id) use ($imageWorkIds) {
            return in_array($id, $imageWorkIds);
        });

        // 2. Elementos a añadir (IDs)
        $anadir = array_filter($worksUpdateIds, function ($id) use ($imageWorkIds) {
            return !in_array($id, $imageWorkIds);
        });

        // 3. Elementos a eliminar (IDs)
        $eliminar = array_filter($imageWorkIds, function ($id) use ($worksUpdateIds) {
            return !in_array($id, $worksUpdateIds);
        });

        $image = $this->eliminar($image, $eliminar);
        $image = $this->anadir($image, $anadir);


        $this->em->persist($image);
        $this->em->flush();

        // TODO

        return $this->json(['workImage' => ImageSerializer::image($image)]);
    }

    #[Route('/{id}', methods: ["DELETE"])]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "Id de la Imagen"
    )]
    public function deleteImage(string $id): JsonResponse
    {

        $image = $this->em->getRepository(Image::class)->findOneBy(
            [
                'id' => $id,
            ]
        );
        if (!$image instanceof Image) {
            return $this->json(
                ['Error' => ['No se ha encontrado la imagen']],
                418
            );
        }

        $this->em->remove($image);
        $this->em->flush();

        return $this->json([
            'message' => 'Imagen ' . $image->getName() . ' Eliminado'
        ]);
    }

    ## Utilities ##
    private function getHomeImagesFromRepository(): ?array
    {

        $homeImages = $this->em->getRepository(Image::class)
            ->createQueryBuilder('i')
            ->where('i.homePosition IS NOT NULL')
            ->getQuery()
            ->getResult();


        return $homeImages;
    }

    private function eliminar(Image $image, ?array $eliminar): Image
    {
        foreach ($image->getWorkImage() as $workImage) {
            foreach ($eliminar as $e) {
                if ($workImage->getWork()->getId() === $e) {
                    $image->removeWorkImage($workImage);
                }
            }
        }
        return $image;
    }

    private function anadir(Image $image, ?array $anadir): Image
    {
        foreach ($anadir as $id) {
            $work = $this->em->getRepository(Work::class)->findOneBy(['id' => $id]);
            $workImage = new WorkImage();
            $workImage->setImage($image);
            $workImage->setWork($work);
            $this->em->persist($workImage);
            $work->addWorkImage($workImage);
            $image->addWorkImage($workImage);
            $this->em->flush();
        }
        return $image;
    }
}
