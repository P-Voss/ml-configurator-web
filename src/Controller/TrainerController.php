<?php

namespace App\Controller;

use App\Entity\UploadFile;
use App\Entity\User;
use App\Repository\ModelRepository;
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

        $fieldConfigurations = [];
        $defaultConfiguration = [
            'name' => '',
            'completed' => false,
            'isTarget' => false,
            'datatype' => false,
            'normalization' => 'none',
            'encoding' => 'none',
            'transformation' => 'none',
            'outlierTreatment' => 'none',
            'augmentation' => [],
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
        $model->setUploadFile($uploadFile);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'file' => $uploadFile,
        ]);
    }

}
