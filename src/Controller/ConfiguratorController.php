<?php

namespace App\Controller;

use App\Converter\ModelstateToJson;
use App\Entity\Hyperparameters;
use App\Entity\Model;
use App\Entity\User;
use App\Enum\ModelTypes;
use App\Repository\ModelRepository;
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

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {}

    #[Route('/configurator/initialize', name: 'app_configurator_init', methods: ["POST"])]
    public function initialize(Request $request, ModelRepository $repository, #[CurrentUser] User $user): JsonResponse
    {
        try {
            $modeltype = ModelTypes::from($request->get('modeltype'));
        } catch (\Exception $error) {
            throw new \Exception("invalid modeltype");
        }

        $lookup = "";
        $lookupExists = true;
        while ($lookupExists) {
            $lookup = $this->getAdverb() . $this->getAnimal();
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
            ->setDescription($request->get('description', ''))
            ->setType($modeltype->value);

        $this->entityManager->persist($model);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'modelId' => $model->getId(),
            'requiresArchitectureConfiguration' => ModelTypes::requiresArchitectureConfiguration($modeltype)
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
        try {
            $modeltype = ModelTypes::from($request->get('modeltype'));
        } catch (\ValueError $error) {
            throw new \Exception("invalid modeltype");
        }

        $state->setName($request->get('name', 'My Model'));
        $state->setDescription($request->get('description', ''));
        $state->setModeltype($modeltype);

        $model->setName($request->get('name', 'My Model'))
            ->setUpdatedate(new \DateTime())
            ->setDescription($request->get('description', ''))
            ->setType($modeltype->value);

        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'model' => $model,
        ]);
    }

    #[Route('/configurator/delete', name: 'app_configurator_delete', methods: ["POST"])]
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

        $configuration = $model->getDecisiontreeConfiguration();
        if ($configuration) {
            $this->entityManager->remove($configuration);
        }
        $configuration = $model->getSvmConfiguration();
        if ($configuration) {
            $this->entityManager->remove($configuration);
        }
        $configuration = $model->getLinRegConfiguration();
        if ($configuration) {
            $this->entityManager->remove($configuration);
        }
        $configuration = $model->getLogRegConfiguration();
        if ($configuration) {
            $this->entityManager->remove($configuration);
        }
        $hyperParameters = $model->getHyperparameters();
        if ($hyperParameters) {
            $this->entityManager->remove($hyperParameters);
        }
        foreach ($model->getLayers() as $layer) {
            $model->removeLayer($layer);
        }
        $this->entityManager->remove($model);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_index');
    }


    #[Route('/configurator/copy', name: 'app_configurator_copy', methods: ["POST"])]
    public function copy(Request $request, ModelRepository $repository, #[CurrentUser] User $user): Response
    {
        return $this->redirectToRoute('app_index');
    }

    private function getAdverb(): string {
        $adverbs = ['apt', 'amazing', 'angry', 'radiant', 'pretty', 'smart', 'cool', 'friendly', 'best', 'bold', 'busy', 'brave', 'calm', 'captivating', 'clever', 'cheerful', 'cute', 'eager', 'enchanted', 'educated', 'fair', 'fine', 'free'];
        $key = array_rand($adverbs);
        return ucfirst($adverbs[$key]);
    }

    private function getAnimal(): string {
        $animals = ['alpaca', 'bear', 'crow', 'dolphin', 'duck', 'eagle', 'ferret', 'frog', 'gecko', 'giraffe', 'goose', 'guppy', 'hare', 'hawk', 'hornet', 'horse', 'jaguar'];
        $key = array_rand($animals);
        return ucfirst($animals[$key]);
    }

}
