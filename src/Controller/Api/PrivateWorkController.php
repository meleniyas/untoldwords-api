<?php

namespace App\Controller\Api;

use App\Controller\RestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

use App\Entity\Work;
use App\Serializer\WorkSerializer;

use App\Constraints\WorkConstraint;
use App\Util\Constants;

#[Route('api/private/work', name: 'api_private_work_')]
#[OA\Tag(name: 'WORK')]
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

class PrivateWorkController extends RestController
{
    #[Route(methods: ["GET"])]
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

    #[Route(methods: ["POST"])]
    #[OA\RequestBody(
        required: true,
        description: 'Añade un nuevo trabajo',
        content: new OA\JsonContent(ref: "#/components/schemas/PostWork")
    )]
    public function postWork(): JsonResponse
    {
        $data = $this->getBody();
        $this->validate($data, WorkConstraint::work());

        $work = new Work();
        $work->setDescription($data['description']);
        $work->setArchitects($data['architects']);
        $work->setName($data['name']);

        $works = $this->em->getRepository(Work::class)->findAll();
        if (!empty($works)) {
            foreach ($works as $w) {
                $w->setPosition($w->getPosition() + 1);
                $this->em->persist($w);
            }
        }
        $work->setPosition(0);
        $this->em->persist($work);
        $this->em->flush();

        $works = $this->em->getRepository(Work::class)->findAll();
        usort($works, function ($a, $b) {
            return $a->getPosition() <=> $b->getPosition();
        });

        return $this->json([WorkSerializer::works($works)]);
    }

    #[Route('/order', name: 'order', methods: ["PUT"])]
    #[OA\RequestBody(
        required: true,
        description: 'Actualiza nombre y descripcion del trabajo',
        content: new OA\JsonContent(ref: "#/components/schemas/PutWorkOrder")
    )]
    public function updateWorkOrder(): JsonResponse
    {
        $data = $this->getBody();

        $this->validate($data, WorkConstraint::workUpdateOrder());

        $worksReference = $data['works'];

        if (!empty($worksReference)) {
            foreach ($worksReference as $workRef) {
                $repository = $this->em->getRepository(Work::class);
                $w = $repository->findOneBy([
                    'id' => $workRef['id']
                ]);
                if ($w instanceof Work) {
                    $w->setPosition($workRef['position']);
                    $this->em->persist($w);
                }
            }
            $this->em->flush();
        }

        $works = $this->em->getRepository(Work::class)->findBy([], ['position' => 'ASC']);

        return $this->json([WorkSerializer::works($works)]);
    }

    ## ------------------------------------------------------------------------ ##
    #[Route('/{id}', methods: ["GET"])]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "Id del Trabajo"
    )]
    public function getWork(string $id): JsonResponse
    {
        $works = $this->em->getRepository(Work::class)->findBy([
            'id' => $id
        ]);
        if (empty($works)) {
            return $this->json(
                ['Error' => ['No se han encontrado trabajos']],
                418
            );
        }
        return $this->json([WorkSerializer::works($works)]);
    }

    #[Route('/{id}', methods: ["PUT"])]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "Id del Trabajo"
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Actualiza los datos trabajo',
        content: new OA\JsonContent(ref: "#/components/schemas/PutWork")
    )]
    public function putWork(string $id): JsonResponse
    {
        $data = $this->getBody();

        $this->validate($data, WorkConstraint::workUpdate());
        $work = $this->em->getRepository(Work::class)->findOneBy(
            [
                'id' => $id,
                'status' => Constants::IMAGE_STATUS['ACTIVE']
            ]
        );
        if (!$work instanceof Work) {
            return $this->json(
                ['Error' => ['No se ha encontrado el trabajo']],
                418
            );
        }

        $work->setName($data['name']);
        $work->setArchitects($data['architects']);
        $work->setDescription($data['description']);
        $work->setStatus($data['status']);

        $this->em->persist($work);
        $this->em->flush();

        return $this->json([WorkSerializer::work($work)]);
    }


    #[Route('/{id}', methods: ["DELETE"])]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "Id del Trabajo"
    )]
    public function deleteWork(string $id): JsonResponse
    {

        $work = $this->em->getRepository(Work::class)->findOneBy(
            [
                'id' => $id,
            ]
        );
        if (!$work instanceof Work) {
            return $this->json(
                ['Error' => ['No se ha encontrado el trabajo']],
                418
            );
        }

        $this->em->remove($work);
        $this->em->flush();

        $works = $this->em->getRepository(Work::class)->findAll();

        usort($works, function ($a, $b) {
            return $a->getPosition() <=> $b->getPosition();
        });

        foreach ($works as $index => $w) {
            $w->setPosition($index);
            $this->em->persist($w);
        }
        $this->em->flush();

        return $this->json([
            'message' => 'Trabajo ' . $work->getName() . ' Eliminado',
            'home' => WorkSerializer::works($works)
        ]);
    }
    // Utilities

}
