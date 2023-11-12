<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ModelRepository;
use App\Repository\UserRepository;
use App\Service\Export;
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

    #[Route('/', name: 'app_index_no_locale')]
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('app_index', ['_locale' => 'en']);
    }

    #[Route('/{_locale<en|de>}/index', name: 'app_index')]
    public function index(#[CurrentUser] User $user, ModelRepository $repository): Response
    {
        if ($user->isIsDemoUser()) {
            $user->setLastActionDatetime(new \DateTime());
            $this->entityManager->flush();
        }
        $models = $repository->findBy(['student' => $user->getId()], ['updatedate' => 'DESC']);
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


    #[Route('/{_locale<en|de>}/configurator/{id}', name: 'app_configurator')]
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


    #[Route('/{_locale<en|de>}/casestudy', name: 'app_casestudy')]
    public function casestudy(): Response
    {
        return $this->render('index/casestudy.html.twig', [

        ]);
    }

    #[Route('/{_locale<en|de>}/model/delete', name: 'app_model_delete', methods: ["POST"])]
    public function delete(Request $request, ModelRepository $repository, #[CurrentUser] User $user): Response
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
        $state->delete();

        $this->entityManager->flush();

        return $this->redirectToRoute('app_index');
    }

    #[Route('/{_locale<en|de>}/model/share', name: 'app_model_share', methods: ["POST"])]
    public function share(
        Request $request,
        ModelRepository $repository,
        UserRepository $userRepository,
        #[CurrentUser] User $user,
        Export $exportService
    ): Response
    {
        $student = $userRepository->find($request->get('studentId'));
        $model = $repository->find($request->get('modelId', 0));
        if (!$model) {
            return new JsonResponse([
                'success' => false,
                'message' => 'invalid modelId',
            ], Response::HTTP_NOT_FOUND);
        }
        if (!$student) {
            return new JsonResponse([
                'success' => false,
                'message' => 'invalid studentId',
            ], Response::HTTP_NOT_FOUND);
        }

        if ($model->getStudent()->getId() !== $user->getId()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'invalid modelId',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $exportService->exportToUser($model, $student);

        return $this->redirectToRoute('app_index');
    }

    #[Route('/{_locale<en|de>}/user/list', name: 'app_user_list', methods: ["GET"])]
    public function userlist(UserRepository $userRepository, #[CurrentUser] User $user): JsonResponse
    {
        $users = [];
        foreach ($userRepository->findBy([], ['email' => 'ASC']) as $student) {
            if ($student->getId() === $user->getId()) {
                continue;
            }
            $users[] = [
                'id' => $student->getId(),
                'name' => $student->getEmail()
            ];
        }

        return new JsonResponse([
            'success' => true,
            'users' => $users,
        ]);
    }

}
