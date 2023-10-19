<?php

namespace App\Controller;

use App\Entity\DecisiontreeConfiguration;
use App\Entity\Layer;
use App\Entity\LinRegConfiguration;
use App\Entity\LogRegConfiguration;
use App\Entity\SvmConfiguration;
use App\Entity\User;
use App\Enum\LayerTypes;
use App\Enum\ModelTypes;
use App\Repository\LayerRepository;
use App\Repository\ModelRepository;
use App\State\Modeltype\AbstractState;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class LayerController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager)
    {}

    #[Route('/configurator/layer/add', name: 'app_configurator_layer_add', methods: ["POST"])]
    public function add(Request $request, ModelRepository $repository, #[CurrentUser] User $user): JsonResponse
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
            $modeltype = ModelTypes::from($model->getType());
            $layertype = LayerTypes::from($request->get('type'));
        } catch (\ValueError $error) {
            throw new \Exception("invalid modeltype");
        }
        if (!ModelTypes::isValidLayertype($modeltype, $layertype)) {
            throw new \Exception("invalid layertype");
        }
        $state = AbstractState::getState($model, $this->entityManager);

        $layer = new Layer();
        $layer->setType($request->get('type'))
            ->setNeuronCount($request->get('neurons', 0))
            ->setActivationFunction($request->get('activationFunction', 'sigmoid'))
            ->setRecurrentDropoutRate($request->get('recurrrentDropoutRate', 0) * 100)
            ->setRegularizationType($request->get('regularizationType'))
            ->setRegularizationLambda($request->get('regularizationLambda', 0) * 100)
            ->setReturnSequences($request->get('returnSequences', false))
            ->setDropoutQuote($request->get('dropoutRate', 0) * 100);

        $this->entityManager->persist($layer);

        try {
            $state->addLayer($layer);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return new JsonResponse([
            'success' => true,
        ]);
    }


    #[Route('/configurator/layer/remove', name: 'app_configurator_layer_remove', methods: ["POST"])]
    public function remove(Request $request, ModelRepository $repository, LayerRepository $layerRepository, #[CurrentUser] User $user): JsonResponse
    {
        $model = $repository->find($request->get('modelId', 0));
        $layer = $layerRepository->find($request->get('layerId', 0));
        if (!$model) {
            return new JsonResponse([
                'success' => false,
                'message' => 'invalid modelId',
            ], Response::HTTP_NOT_FOUND);
        }
        if (!$layer) {
            return new JsonResponse([
                'success' => false,
                'message' => 'invalid layerId',
            ], Response::HTTP_NOT_FOUND);
        }
        if ($model->getStudent()->getId() !== $user->getId()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'invalid modelId',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $state = AbstractState::getState($model, $this->entityManager);
        $state->removeLayer($layer);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
        ]);
    }


    #[Route('/configurator/architecture/dtree', name: 'app_configurator_save_dtree', methods: ["POST"])]
    public function dtree(Request $request, ModelRepository $repository, #[CurrentUser] User $user): JsonResponse
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
        $state = AbstractState::getState($model, $this->entityManager);
        $decisionTreeConfiguration = new DecisiontreeConfiguration();
        $decisionTreeConfiguration->setMaxDepth($request->get('maxDepth', 1))
            ->setMinSampleSplit($request->get('minSampleSplit', 1))
            ->setMaxFeatures($request->get('maxFeatures', 1))
            ->setMinSamplesLeaf($request->get('minSamplesLeaf', 1))
            ->setMissingValueHandling($request->get('missingValueHandling', 'mean'))
            ->setQualityMeasure($request->get('qualityMeasure', 'gini'));

        try {
            $state->setDecisiontreeConfiguration($decisionTreeConfiguration);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return new JsonResponse([
            'success' => true,
        ]);
    }


    #[Route('/configurator/architecture/svm', name: 'app_configurator_save_svm', methods: ["POST"])]
    public function svm(Request $request, ModelRepository $repository, #[CurrentUser] User $user): JsonResponse
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
        $state = AbstractState::getState($model, $this->entityManager);
        $svmConfiguration = new SvmConfiguration();
        $svmConfiguration->setKernel($request->get('kernel', 1))
            ->setC((int)((float) $request->get('c', 1) * 100))
            ->setDegree($request->get('degree', 1));

        try {
            $state->setSvmConfiguration($svmConfiguration);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return new JsonResponse([
            'success' => true,
        ]);
    }


    #[Route('/configurator/architecture/logreg', name: 'app_configurator_save_logreg', methods: ["POST"])]
    public function logreg(Request $request, ModelRepository $repository, #[CurrentUser] User $user): JsonResponse
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
        $state = AbstractState::getState($model, $this->entityManager);
        $logRegConfiguration = new LogRegConfiguration();
        $logRegConfiguration->setRegularizerType($request->get('regularizerType', 'none'))
            ->setSolver($request->get('solver', 'liblinear'))
            ->setLambda($request->get('lambda', 0));

        try {
            $state->setLogRegConfiguration($logRegConfiguration);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return new JsonResponse([
            'success' => true,
        ]);
    }


    #[Route('/configurator/architecture/linreg', name: 'app_configurator_save_linreg', methods: ["POST"])]
    public function linreg(Request $request, ModelRepository $repository, #[CurrentUser] User $user): JsonResponse
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
        $state = AbstractState::getState($model, $this->entityManager);
        $linRegConfiguration = new LinRegConfiguration();
        $linRegConfiguration->setRegularizationType($request->get('regularizationType', 'none'))
            ->setAlpha($request->get('alpha', 0));

        try {
            $state->setLinRegConfiguration($linRegConfiguration);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return new JsonResponse([
            'success' => true,
        ]);
    }

}
