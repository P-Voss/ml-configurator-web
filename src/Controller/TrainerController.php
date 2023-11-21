<?php

namespace App\Controller;

use App\Entity\TrainingTask;
use App\Entity\UploadFile;
use App\Entity\User;
use App\Repository\ModelRepository;
use App\Repository\TrainingTaskRepository;
use App\Service\TrainingPathGenerator;
use App\State\Modeltype\AbstractState;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrainerController extends AbstractController
{


    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/{_locale<en|de>}/trainer/index/{id}', name: 'app_trainer', methods: ["GET"])]
    public function index(#[CurrentUser] User $user, int $id = null): Response
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

        return $this->render('trainer/index.html.twig', [
            'modelId' => $id,
        ]);
    }

    #[Route('/trainer/data/marktarget', name: 'app_trainer_data_marktarget', methods: ['POST'])]
    public function marktarget(#[CurrentUser] User $user, Request $request, ModelRepository $repository)
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

        foreach ($model->getFieldConfigurations() as $field) {
            if ($field->getId() === (int) $request->get('fieldId', '')) {
                $field->setIsTarget(true)
                    ->setIsIgnored(false);
            } else {
                $field->setIsTarget(false);
            }
        }
        $model->setUpdatedate(new \DateTime());
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'fieldConfigurations' => $model->getFieldConfigurations()->toArray(),
        ]);
    }


    #[Route('/trainer/data/removetarget', name: 'app_trainer_data_removetarget', methods: ['POST'])]
    public function removetarget(#[CurrentUser] User $user, Request $request, ModelRepository $repository)
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

        foreach ($model->getFieldConfigurations() as $field) {
            if ($field->getId() === (int) $request->get('fieldId', '')) {
                $field->setIsTarget(false)
                    ->setIsIgnored(false);
            }
        }
        $model->setUpdatedate(new \DateTime());
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'fieldConfigurations' => $model->getFieldConfigurations()->toArray(),
        ]);
    }

    #[Route('/trainer/data/ignore', name: 'app_trainer_data_ignore', methods: ['POST'])]
    public function ignore(#[CurrentUser] User $user, Request $request, ModelRepository $repository)
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
        foreach ($model->getFieldConfigurations() as $field) {
            if ($field->getId() === (int) $request->get('fieldId', '')) {
                $field->setIsTarget(false)
                    ->setIsIgnored(true);
            }
        }
        $model->setUpdatedate(new \DateTime());
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'fieldConfigurations' => $model->getFieldConfigurations()->toArray(),
        ]);
    }

    #[Route('/trainer/data/unignore', name: 'app_trainer_data_unignore', methods: ['POST'])]
    public function unignore(#[CurrentUser] User $user, Request $request, ModelRepository $repository)
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

        foreach ($model->getFieldConfigurations() as $field) {
            if ($field->getId() === (int) $request->get('fieldId', '')) {
                $field->setIsTarget(false)
                    ->setIsIgnored(false);
            }
        }
        $model->setUpdatedate(new \DateTime());
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'fieldConfigurations' => $model->getFieldConfigurations()->toArray(),
        ]);
    }

    /**
     * @param User $user
     * @param Request $request
     * @param ModelRepository $repository
     * @return JsonResponse
     *  currently not used
     */
    #[Route('/trainer/data/update', name: 'app_trainer_data_update', methods: ['POST'])]
    public function update(#[CurrentUser] User $user, Request $request, ModelRepository $repository)
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
        /**
         * @todo Fieldconfiguration direkt in Model oder eigene Trainings-Entity legen
         */
        $uploadFile = $model->getUploadFile();
        if (!$uploadFile) {
            return new JsonResponse([
                'success' => false,
                'message' => 'invalid modelId',
            ], Response::HTTP_NOT_FOUND);
        }
        $configuration = $uploadFile->getFieldConfigurations();
        if (!$configuration) {
            return new JsonResponse([
                'success' => false,
                'message' => 'invalid modelId',
            ], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($configuration);
        foreach ($data as $field) {
            if ($field->name === $request->get('fieldname', '')) {
                $field['augmentation'] = [

                ];
                $configuration = [
                    ...$field,
                    'completed' => true,
                    'datatype' => false,
                    'normalization' => $request->get('normalizationMethod', 'l1'),
                    'encoding' => 'none',
                    'transformation' => 'none',
                    'outlierTreatment' => 'none',
                    'augmentation' => [],
                ];
            }
        }
        $configuration = json_encode(array_values($data));
        $uploadFile->setFieldConfigurations($configuration);
        $model->setUpdatedate(new \DateTime());
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
        ]);
    }

    #[Route('/trainer/hyperparameter/save', name: 'app_trainer_hyperparameter_save', methods: ['POST'])]
    public function saveHyperparameter(#[CurrentUser] User $user, Request $request, ModelRepository $repository)
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
        $state->setHyperParameter($request->request->all());
        $this->entityManager->flush();

        return new JsonResponse(['success' => true]);
    }


    #[Route('/trainer/training/load', name: 'app_trainer_training_load', methods: ['POST'])]
    public function loadTasks(#[CurrentUser] User $user, Request $request, ModelRepository $repository,): Response
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

        $completedTasks = [];
        foreach ($model->getTrainingTasks() as $task) {
            if ($task->getState() === TrainingTask::STATE_COMPLETED) {
                $completedTasks[] = $task;
            }
        }
        usort($completedTasks, function (TrainingTask $t1, TrainingTask $t2) {
            return $t1->getId() > $t2->getId();
        });

        return new JsonResponse([
            'success' => true,
            'bestTrainingId' => $state->getBestTrainingId(),
            'completedTasks' => $completedTasks,
        ]);
    }

    #[Route('/{_locale<en|de>}/trainer/training/submit', name: 'app_trainer_training_submit', methods: ['POST'])]
    public function createTask(
        #[CurrentUser] User $user,
        Request $request,
        ModelRepository $repository,
        TrainingPathGenerator $pathGenerator,
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
        $pathGenerator->setLookup($model->getLookup());

        try {
            $state = AbstractState::getState($model, $this->entityManager);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }
        $codegenerator = $state->getCodegenerator();

        try {
            $modelData = json_encode($model);
            $task = new TrainingTask();
            $task->setCreationDatetime(new \DateTime())
                ->setState(TrainingTask::STATE_OPEN)
                ->setReportPath($pathGenerator->getReportFile())
                ->setLogPath($pathGenerator->getLogFile())
                ->setScriptPath($pathGenerator->getPythonFile())
                ->setEncodedModel(base64_encode($modelData))
                ->setModelHash(md5($modelData));
            $this->entityManager->persist($task);

            $state->addTrainingTask($task)
                ->setModelFile($pathGenerator)
                ->setCheckpointFile($pathGenerator)
                ->setEncoderFile($pathGenerator)
                ->setLabelEncoderFile($pathGenerator)
                ->setScalerFile($pathGenerator);

            $this->entityManager->flush();

            $script = $codegenerator->generateTrainingScript($pathGenerator);
            file_put_contents($pathGenerator->getPythonFile(), $script);
        } catch (\Exception $exception) {
            dd($exception);
        }
        return new JsonResponse([
            'success' => true,
            'taskId' => $task->getId()
        ]);
    }


    #[Route('/{_locale<en|de>}/trainer/training/execute', name: 'app_trainer_training_execute', methods: ['POST'])]
    public function executeTask(
        Request $request,
        TrainingTaskRepository $taskRepository,
    )
    {
        $runningTask = $taskRepository->findBy(['state' => TrainingTask::STATE_PROGRESS]);
        if ($runningTask) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Another task is already running',
            ]);
        }

        $task = $taskRepository->find($request->get('taskId'));
        if (!$task) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid task specified',
            ]);
        }
        if ($task->getState() === TrainingTask::STATE_COMPLETED) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid task specified',
            ]);
        }

        $task->setStartDatetime(new \DateTime());
        $process = new Process([
            $this->getParameter('app.pythonpath'),
            $task->getScriptPath()
        ]);
        $process->setTimeout(3600);
        $process->run();
        $success = true;
        $newState = TrainingTask::STATE_COMPLETED;
        if (!$process->isSuccessful()) {
            $success = false;
            $newState = TrainingTask::STATE_ERROR;
            file_put_contents($task->getReportPath(), json_encode($process->getErrorOutput()));
        }
        $task->setEndDatetime(new \DateTime())
            ->setState($newState);
        $this->entityManager->flush();
        try {
            $state = AbstractState::getState($task->getModel(), $this->entityManager);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return new JsonResponse([
            'success' => $success,
            'validTraining' => $state->validTraining(),
        ]);
    }

    #[Route('/{_locale<en|de>}/trainer/training/example', name: 'app_trainer_training_example', methods: ['POST'])]
    public function exampleScript(
        #[CurrentUser] User $user,
        Request $request,
        ModelRepository $repository,
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
        try {
            $state = AbstractState::getState($model, $this->entityManager);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }
        $codegenerator = $state->getCodegenerator();
        $script = $codegenerator->getExampleScript();
        return new JsonResponse([
            'success' => true,
            'code' => $script
        ]);
    }

    #[Route('/{_locale<en|de>}/trainer/training/delete', name: 'app_trainer_training_delete', methods: ['POST'])]
    public function deleteTask(
        #[CurrentUser] User $user,
        Request $request,
        TrainingTaskRepository $repository,
    )
    {
        $task = $repository->find($request->get('taskId', 0));
        if (!$task) {
            return new JsonResponse([
                'success' => false,
                'message' => 'invalid id',
            ], Response::HTTP_NOT_FOUND);
        }
        if ($task->getModel()->getStudent()->getId() !== $user->getId()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'invalid id',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($task->getState() !== TrainingTask::STATE_COMPLETED && $task->getState() !== TrainingTask::STATE_ERROR) {
            return new JsonResponse([
                'success' => false,
                'message' => 'can not delete task at this moment',
            ], Response::HTTP_UNAUTHORIZED);
        }
        try {
            $state = AbstractState::getState($task->getModel(), $this->entityManager);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }
        $state->deleteTrainingTask($task);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'validTraining' => $state->validTraining(),
        ]);
    }

}
