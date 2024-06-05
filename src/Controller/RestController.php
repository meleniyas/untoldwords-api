<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Service\Validator;

// Some utilities are going to be added to be used in all controllers
class RestController extends AbstractController
{
    protected EntityManagerInterface $em;
    protected Request|null $request;
    protected Validator $validator;

    public function __construct(

        // Data access
        EntityManagerInterface $em,

        // Current Requeste access
        RequestStack $requestStack,

        // Data validation service
        Validator $validator,
    ) {
        $this->em = $em;
        $this->request = $requestStack->getCurrentRequest();
        $this->validator = $validator;
    }

    // To transform jsonBody to array 
    protected function getBody()
    {
        $data = json_decode((string) $this->request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        return $data;
    }

    // Just to send validator errors as jsonResponse
    protected function validate(array $data, Collection $constraint)
    {
        //To avoid warning
        if ($data === null) $data = [];

        $errors = $this->validator->validate($data, $constraint);

        if (count($errors) > 0) {

            throw new BadRequestHttpException(json_encode($errors, JSON_THROW_ON_ERROR));
        }
    }
}
