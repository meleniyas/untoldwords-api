<?php

namespace App\Controller\Api;

use App\Controller\RestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

use App\Entity\Image;
use App\Entity\Work;
use App\Entity\WorkImage;
use App\Serializer\WorkSerializer;
use App\Serializer\WorkImageSerializer;

use App\Constraints\WorkImageConstraint;
use App\Util\Constants;

#[Route('api/private/work', name: 'api_private_work')]
#[OA\Tag(name: 'WORK IMAGE')]
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

class PrivateWorkImageController extends RestController
{

    #[Route('/{id}/image', methods: ["POST"])]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "Id del Trabajo"
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Añade una imagen en el Trabajo',
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(ref: "#/components/schemas/PostWorkImage")
        )
    )]
    public function postWorkImage(string $id): JsonResponse
    {
        $name = $this->request->request->get('name');
        $imageFile = $this->request->files->get('image');

        $work = $this->em->getRepository(Work::class)->findBy([
            'id' => $id
        ]);

        if ($imageFile && $imageFile->isValid()) {

            $imageData = file_get_contents($imageFile->getRealPath());

            $image = new Image();
            $image->setName($name);
            $image->setContent($imageData);

            $this->em->persist($image);

            $work = $this->em->getRepository(Work::class)->findOneBy([
                'id' => $id
            ]);
            $position = count($work->getWorkImages());

            $workImage = new WorkImage();
            $workImage->setImage($image);
            $workImage->setWork($work);
            $workImage->setPosition($position);

            $this->em->persist($workImage);

            $work->addWorkImage($workImage);
            $this->em->persist($work);

            $image->addWorkImage($workImage);

            $this->em->persist($image);

            $this->em->flush();

            return $this->json([WorkSerializer::work($work)]);
        }
        return new JsonResponse(['error' => 'Invalid image file'], 400);
    }

    #[Route('/image/{id}', methods: ["GET"])]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "Id del a Imagen del Trabajo"
    )]
    public function getWorkImage(string $id): JsonResponse
    {
        $workImage = $this->em->getRepository(WorkImage::class)->findOneBy([
            'id' => $id
        ]);
        if (!$workImage instanceof WorkImage) {
            return $this->json(
                ['Error' => ['No se han encontrado trabajos']],
                418
            );
        }
        return $this->json([WorkImageSerializer::workImage($workImage)]);
    }

    #[Route('/{id}/image/order', methods: ["PUT"])]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "Id del trabajo"
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Actualiza el orden de las imagenes',
        content: new OA\JsonContent(ref: "#/components/schemas/PutWorkImageOrder")
    )]
    public function putWorkImageOrder(string $id): JsonResponse
    {
        $data = $this->getBody();

        $this->validate($data, WorkImageConstraint::workImageUpdateOrder());

        $workImagesReference = $data['workImages'];

        if (!empty($workImagesReference)) {
            foreach ($workImagesReference as $workImageRef) {
                $repository = $this->em->getRepository(WorkImage::class);
                $wi = $repository->findOneBy([
                    'id' => $workImageRef['id']
                ]);
                if ($wi instanceof WorkImage) {
                    $wi->setPosition($workImageRef['position']);
                    $this->em->persist($wi);
                }
            }
            $this->em->flush();
        }

        $work = $this->em->getRepository(Work::class)->findOneBy([
            'id' => $id
        ], ['position' => 'ASC']);

        return $this->json([WorkSerializer::work($work)]);
    }

    #[Route('/image/{id}', methods: ["PUT"])]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "Id de la imagen del trabajo"
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Actualiza el estado de la imagen del trabajo',
        content: new OA\JsonContent(ref: "#/components/schemas/PutWorkImage")
    )]
    public function putWorkImage(string $id): JsonResponse
    {
        $data = $this->getBody();

        $this->validate($data, WorkImageConstraint::workImageUpdate());
        $workImage = $this->em->getRepository(WorkImage::class)->findOneBy(
            [
                'id' => $id
            ]
        );
        if (!$workImage instanceof WorkImage) {
            return $this->json(
                ['Error' => ['No se ha encontrado el la imagen del trabajo']],
                418
            );
        }

        $workImage->setStatus($data['status']);

        $this->em->persist($workImage);
        $this->em->flush();

        return $this->json(['workImage' => WorkImageSerializer::workImage($workImage)]);
    }

    #[Route('/image/{id}', methods: ["DELETE"])]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "Id del la image del Trabajo"
    )]
    public function deleteImageWork(string $id): JsonResponse
    {

        $workImage = $this->em->getRepository(WorkImage::class)->findOneBy(
            [
                'id' => $id,
            ]
        );
        if (!$workImage instanceof WorkImage) {
            return $this->json(
                ['Error' => ['No se ha encontrado la imagen del trabajo']],
                418
            );
        }

        $work = $workImage->getWork();

        $this->em->remove($workImage);
        $this->em->flush();

        $workImages = $work->getWorkImages()->toArray();

        usort($workImages, function ($a, $b) {
            return $a->getPosition() <=> $b->getPosition();
        });

        foreach ($workImages as $index => $wi) {
            $wi->setPosition($index);
            $this->em->persist($wi);
        }
        $this->em->flush();

        return $this->json([
            'message' => 'Imagen del Trabajo ' . $work->getName() . ' Eliminada',
            'home' => WorkSerializer::work($work)
        ]);
    }
}
