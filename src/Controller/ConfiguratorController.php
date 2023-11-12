<?php

namespace App\Controller;

use App\Converter\ModelstateToJson;
use App\Entity\Hyperparameters;
use App\Entity\Model;
use App\Entity\User;
use App\Enum\ModelTypes;
use App\Repository\ModelRepository;
use App\Repository\TrainingTaskRepository;
use App\Service\Dataset;
use App\Service\FieldConfigurationGenerator;
use App\Service\KeywordTrait;
use App\Service\Rollback;
use App\State\Modeltype\AbstractState;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ConfiguratorController extends AbstractController
{

    use KeywordTrait;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {}

    #[Route('/{_locale<en|de>}/configurator/initialize', name: 'app_configurator_init', methods: ["POST"])]
    public function initialize(Request $request, ModelRepository $repository, #[CurrentUser] User $user, Dataset $datasetService): JsonResponse
    {
        try {
            $modeltype = ModelTypes::from($request->get('modeltype'));
        } catch (\Exception $error) {
            throw new \Exception("invalid modeltype");
        }
        if (!Dataset::isValidDataset($request->get('dataset', ''))) {
            throw new \Exception("invalid dataset");
        }

        $lookup = "";
        $lookupExists = true;
        while ($lookupExists) {
            $lookup = $this->getSize() . $this->getAdverb() . $this->getAnimal();
            $modelWithLookup = $repository->findOneBy(['lookup' => $lookup]);
            if (!$modelWithLookup) {
                $lookupExists = false;
            }
        }

        $model = new Model();
        $model->setName($request->get('name', 'My Model'))
            ->setLookup($lookup)
            ->setStudent($user)
            ->setCreationdate(new \DateTime())
            ->setUpdatedate(new \DateTime())
            ->setDescription($request->get('description', ''))
            ->setDataset($request->get('dataset', ''))
            ->setType($modeltype->value);

        $this->entityManager->persist($model);

        foreach ($datasetService->getDefaultConfigurations($model->getDataset()) as $configuration) {
            $configuration->setModel($model);
            $this->entityManager->persist($configuration);
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'redirectUrl' => $this->generateUrl('app_configurator', ['id' => $model->getId(), '_locale' => $request->getLocale()])
        ]);
    }

    #[Route('/configurator/update', name: 'app_configurator_update', methods: ["POST"])]
    public function update(Request $request, ModelRepository $repository, #[CurrentUser] User $user): JsonResponse
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

        try {
            $state = AbstractState::getState($model, $this->entityManager);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }
        $state->setName($request->get('name', 'My Model'));
        $state->setDescription($request->get('description', ''));

        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'model' => $model,
        ]);
    }


    #[Route('/{_locale<en|de>}/configurator/rollback', name: 'app_configurator_rollback', methods: ['POST'])]
    public function rollbackConfiguration(
        #[CurrentUser] User $user,
        Request $request,
        ModelRepository $repository,
        TrainingTaskRepository $taskRepository,
        Rollback $rollbackService
    )
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
        $task = $taskRepository->find($request->get('taskId', 0));
        if (!$task) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid task specified',
            ]);
        }
        if ($task->getModel()->getId() !==  $model->getId()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid task specified',
            ]);
        }

        $rollbackService->rollback($model, $task);

        return new JsonResponse([
            'success' => true
        ]);
    }

}
