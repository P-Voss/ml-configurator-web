<?php

namespace App\Controller;

use App\Entity\UploadFile;
use App\Entity\User;
use App\Repository\ModelRepository;
use App\State\Modeltype\AbstractState;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrainerController extends AbstractController
{


    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/trainer/index/{id}', name: 'app_trainer')]
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

    #[Route('/trainer/upload', name: 'app_trainer_upload', methods: ['POST'])]
    public function upload(#[CurrentUser] User $user, Request $request, ModelRepository $repository, SluggerInterface $slugger)
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

        $currentUpload = $model->getUploadFile();
        if ($currentUpload) {
            $this->entityManager->remove($currentUpload);
            $this->entityManager->flush();
        }

        /** @var UploadedFile $filedata */
        $filedata = $request->files->get('file');
        $originalFilename = pathinfo($filedata->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $filename = $safeFilename . '-' . uniqid() . '.' . $filedata->guessExtension();

        /**
         * @todo auslagern; Delimiter aus Eingabe bestimmen?
         */

        $content =  $filedata->getContent();
        $content = explode(PHP_EOL, $content);

        $headerLine = array_shift($content);
        $header = str_getcsv($headerLine, ';');

        /**
         * @todo eigene Entities für Feld + Feldkonfiguration
         * @todo relevante Konfigurationsparameter recherchieren
         */
        $fieldConfigurations = [];
        $defaultConfiguration = [
            'name' => '',
            'ignore' => false,
            'completed' => true,
            'isTarget' => false,
            'datatype' => 'text',
            'configuration' => [
                'scalingMethod' => 'standardization',
                'outlierTreatment' => 'remove',
                'normalizationMethod' => 'l1',
                'tokenizationMethod' => 'word',
                'specialCharacterHandling' => 'remove',
                'vectorizationMethod' => 'tfidf',
                'padding' => false,
                'paddingType' => 'none',
                'paddingSize' => 0,
            ],
        ];
        $i = 0;
        foreach ($header as $column) {
            $fieldConfigurations[] = [
                ...$defaultConfiguration,
                'name' => $column,
                'isTarget' => $i === 1,
            ];
            $i++;
        }

        $uploadFile = new UploadFile();
        $uploadFile->setModel($model)
            ->setContent($filedata->getContent())
            ->setHash(md5($filedata->getContent()))
            ->setFilename($filename)
            ->setUploadDate(new \DateTime())
            ->setEntryCount(count($content))
            ->setContainsHeader(true)
            ->setFieldConfigurations(json_encode(array_values($fieldConfigurations)))
            ->setHeader(json_encode($header));
        $this->entityManager->persist($uploadFile);
        $model->setUploadFile($uploadFile)
            ->setUpdatedate(new \DateTime());
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'file' => $uploadFile,
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
                $field->isTarget = true;
                $field->ignore = false;
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
                $field->isTarget = false;
                $field->ignore = false;
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

        $data = json_decode($configuration, );
        foreach ($data as $field) {
            if ($field->name === $request->get('fieldname', '')) {
                $field->isTarget = false;
                $field->ignore = true;
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
                $field->isTarget = false;
                $field->ignore = false;
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

}
