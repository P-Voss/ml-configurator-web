<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ModelRepository;
use App\Service\TrainingPathGenerator;
use App\State\Modeltype\AbstractState;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApplicationController extends AbstractController
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {}

    #[Route('/{_locale<en|de>}/application/{id}', name: 'app_application', methods: ["GET"])]
    public function configurator(#[CurrentUser] User $user, ModelRepository $repository, int $id = null): Response
    {
        $model = $repository->find($id);
        if (!$model) {
            return $this->redirectToRoute('app_index');
        }
        if ($model->getStudent()->getId() !== $user->getId()) {
            return $this->redirectToRoute('app_index');
        }

        try {
            $state = AbstractState::getState($model, $this->entityManager);
        } catch (\Exception $exception) {
            return $this->redirectToRoute('app_index');
        }
        if (!$state->validTraining()) {
            return $this->redirectToRoute('app_index');
        }

        return $this->render('application/index.html.twig', [
            'modelId' => $id,
        ]);
    }

    #[Route('/application/execute', name: 'app_application_execute', methods: ['POST'])]
    public function execute(
        #[CurrentUser] User $user,
        Request $request,
        ModelRepository $repository,
        TrainingPathGenerator $pathGenerator
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

        if ($request->get('input', '') === '') {
            return new JsonResponse([
                'success' => false,
                'message' => 'invalid input',
            ], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $state = AbstractState::getState($model, $this->entityManager);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => 'invalid input',
            ], Response::HTTP_UNAUTHORIZED);
        }
        if (!$state->validTraining()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'invalid input',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $codeGenerator = $state->getCodegenerator();
        $pathGenerator->setLookup($model->getLookup());

        $lines = explode(PHP_EOL, $request->get('input'));
        if (count($lines) < 1) {
            return new JsonResponse([
                'success' => false,
                'message' => 'invalid input',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $sourceFile = fopen($pathGenerator->getExecCsvFile(), 'w');
        foreach ($lines as $line) {
            $csvLine = str_getcsv($line, ';', '');
            fputcsv($sourceFile, $csvLine, ';', chr(0));
        }
        fclose($sourceFile);

        $script = $codeGenerator->generateApplicationScript(
            $pathGenerator->getExecCsvFile(),
            $pathGenerator->getExecResultFile()
        );
        file_put_contents($pathGenerator->getExecPythonFile(), $script);

        $process = new Process([
            $this->getParameter('app.pythonpath'),
            $pathGenerator->getExecPythonFile()
        ]);
        $process->setTimeout(3600);
        $process->run();
        $message = '';
        $success = true;
        $result = [];
        if (!$process->isSuccessful()) {
            $message = $process->getErrorOutput();
            $success = false;
        } else {
            if (file_exists($pathGenerator->getExecResultFile())) {
                $file = fopen($pathGenerator->getExecResultFile(), 'r');
                while ($row = fgetcsv($file, null, ';', chr(0))) {
                    $result[] = $row[0];
                }
                fclose($file);
            }
        }

        return new JsonResponse([
            'success' => $success,
            'message' => $message,
            'result' => $result,
        ]);
    }

}
