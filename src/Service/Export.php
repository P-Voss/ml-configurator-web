<?php

namespace App\Service;

use App\Entity\Model;
use App\Entity\User;
use App\Repository\ModelRepository;
use Doctrine\ORM\EntityManagerInterface;

class Export
{

    use KeywordTrait;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private TrainingPathGenerator $pathGenerator,
        private Dataset $datasetService,
        private ModelRepository $modelRepository
    )
    {

    }


    public function exportToUser(Model $fromModel, User $targetUser)
    {

        $lookup = "";
        $lookupExists = true;
        while ($lookupExists) {
            $lookup = $this->getSize() . $this->getAdverb() . $this->getAnimal();
            $modelWithLookup = $this->modelRepository->findOneBy(['lookup' => $lookup]);
            if (!$modelWithLookup) {
                $lookupExists = false;
            }
        }

        $model = new Model();
        $model->setStudent($targetUser)
            ->setName('Import ' . $lookup)
            ->setLookup($lookup)
            ->setCreationdate(new \DateTime())
            ->setDataset($fromModel->getDataset())
            ->setType($fromModel->getType())
            ->setHyperparameters($fromModel->getHyperparameters())
            ->setDescription($fromModel->getDescription());
        $this->entityManager->persist($model);

        if ($fromModel->getDecisiontreeConfiguration()) {
            $fromConfig = $fromModel->getDecisiontreeConfiguration()->createCopy();
            $this->entityManager->persist($fromConfig);
            $model->setDecisiontreeConfiguration($fromConfig);
        }
        if ($fromModel->getSvmConfiguration()) {
            $fromConfig = $fromModel->getSvmConfiguration()->createCopy();
            $this->entityManager->persist($fromConfig);
            $model->setSvmConfiguration($fromConfig);
        }
        if ($fromModel->getLogRegConfiguration()) {
            $fromConfig = $fromModel->getLogRegConfiguration()->createCopy();
            $this->entityManager->persist($fromConfig);
            $model->setLogRegConfiguration($fromConfig);
        }
        if ($fromModel->getLinRegConfiguration()) {
            $fromConfig = $fromModel->getLinRegConfiguration()->createCopy();
            $this->entityManager->persist($fromConfig);
            $model->setLinRegConfiguration($fromConfig);
        }
        foreach ($fromModel->getLayers() as $layer) {
            $fromConfig = $layer->createCopy();
            $this->entityManager->persist($fromConfig);
            $model->addLayer($fromConfig);
        }
        foreach ($fromModel->getFieldConfigurations() as $fieldConfiguration) {
            $fromConfig = $fieldConfiguration->createCopy();
            $this->entityManager->persist($fromConfig);
            $model->addFieldConfiguration($fromConfig);
        }
        $this->entityManager->flush();
    }

}