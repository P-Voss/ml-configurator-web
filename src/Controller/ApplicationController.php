<?php

namespace App\Controller;

use App\Entity\Model;
use App\Entity\User;
use App\Repository\ModelRepository;
use App\State\Modeltype\AbstractState;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApplicationController extends AbstractController
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {}

    #[Route('/{_locale<en|de>}/application/{id}', name: 'app_application', methods: ["GET"])]
    public function configurator(#[CurrentUser] User $user, int $id = null): Response
    {
        $validModelId = false;
        foreach ($user->getModels() as $model) {
            if ($model->getId() === $id) {
                $validModelId = true;
            }
        }
        if (!$validModelId) {
            $this->redirectToRoute('app_index');
        }

        return $this->render('application/index.html.twig', [
            'modelId' => $validModelId ? $id : null,
        ]);
    }

    #[Route('/application/execute', name: 'app_application_execute', methods: ['POST'])]
    public function execute(#[CurrentUser] User $user, Request $request, ModelRepository $repository)
    {
        $model = $repository->find($request->get('id', 0));
        if (!$model) {
            return new JsonResponse([
                'success' => false,
                'message' => 'invalid modelId',
            ], Response::HTTP_NOT_FOUND);
        }
        if ($model->getStudent()->getId() !== $user->getId()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'invalid modelId',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($request->get('input', '') === '') {
            return new JsonResponse([
                'success' => false,
                'message' => 'invalid input',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $lines = explode(PHP_EOL, $request->get('input'));
        $csv = [];
        foreach ($lines as $line) {
            $csv[] = str_getcsv($line, ';', '');
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'not yet implemented',
        ]);
    }

}
