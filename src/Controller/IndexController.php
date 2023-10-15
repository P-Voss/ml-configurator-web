<?php

namespace App\Controller;

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

class IndexController extends AbstractController
{


    public function __construct(private readonly EntityManagerInterface $entityManager)
    {}

    #[Route('/index', name: 'app_index')]
    public function index(#[CurrentUser] User $user): Response
    {
        $models = $user->getModels();
        return $this->render('index/index.html.twig', [
            'models' => $models
        ]);
    }


    #[Route('/loadmodel', name: 'app_configurator_loadmodel', methods: ["POST"])]
    public function loadModel(Request $request, ModelRepository $repository, #[CurrentUser] User $user): Response
    {
        $model = $repository->find($request->get('modelId', 0));
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

        try {
            $state = AbstractState::getState($model, $this->entityManager);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return new JsonResponse([
            'success' => true,
            'model' => $state,
        ]);
    }


    #[Route('/configurator/{id}', name: 'app_configurator')]
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

        return $this->render('index/configurator.html.twig', [
            'modelId' => $validModelId ? $id : null,
        ]);
    }


    #[Route('/casestudy', name: 'app_casestudy')]
    public function casestudy(): Response
    {
        return $this->render('index/casestudy.html.twig', [

        ]);
    }

}
