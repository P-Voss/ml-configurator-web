<?php

namespace App\Service;

use App\Entity\FieldConfiguration;
use App\Entity\Layer;
use App\Entity\Model;
use App\Entity\TrainingTask;
use Doctrine\ORM\EntityManagerInterface;

class Rollback
{

    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }


    public function rollback(Model $model, TrainingTask $targetTask): void
    {
        $configuration = json_decode(base64_decode($targetTask->getEncodedModel()));

        if (count($configuration->fieldConfigurations) > 0) {
            foreach ($model->getFieldConfigurations() as $fieldConfiguration) {
                $model->removeFieldConfiguration($fieldConfiguration);
                $this->entityManager->remove($fieldConfiguration);
            }
            foreach ($configuration->fieldConfigurations as $fieldConfiguration) {
                $field = new FieldConfiguration();
                $field->setName($fieldConfiguration->name)
                    ->setType($fieldConfiguration->type)
                    ->setIsIgnored($fieldConfiguration->isIgnored)
                    ->setIsTarget($fieldConfiguration->isTarget);
                $this->entityManager->persist($field);
                $model->addFieldConfiguration($field);
            }
        }
        if (count($configuration->layers) > 0) {
            foreach ($model->getLayers() as $layer) {
                $model->removeLayer($layer);
                $this->entityManager->remove($layer);
            }
            foreach ($configuration->layers as $layer) {
                $newLayer = new Layer();
                $newLayer->setType($layer->type)
                    ->setNeuronCount($layer->neurons)
                    ->setActivationFunction($layer->activationFunction)
                    ->setDropoutQuote($layer->dropoutQuote * 100)
                    ->setRecurrentDropoutRate($layer->recurrentDropoutRate * 100)
                    ->setRegularizationType($layer->regularizationType)
                    ->setRegularizationLambda($layer->regularizationLambda * 100)
                    ->setReturnSequences($layer->returnSequences);
                $this->entityManager->persist($newLayer);
                $model->addLayer($newLayer);
            }
        }
        if ($configuration->decisiontreeConfiguration) {
            $conf = $model->getDecisiontreeConfiguration();
            $conf->setQualityMeasure($configuration->decisiontreeConfiguration->qualityMeasure)
                ->setMissingValueHandling($configuration->decisiontreeConfiguration->missingValueHandling)
                ->setMinSamplesLeaf($configuration->decisiontreeConfiguration->minSamplesLeaf)
                ->setMinSampleSplit($configuration->decisiontreeConfiguration->minSampleSplit)
                ->setMaxDepth($configuration->decisiontreeConfiguration->maxDepth)
                ->setMaxFeatures($configuration->decisiontreeConfiguration->maxFeatures);
        }
        if ($configuration->logRegConfiguration) {
            $conf = $model->getLogRegConfiguration();
            $conf->setLambda($configuration->logRegConfiguration->lambda)
                ->setRegularizerType($configuration->logRegConfiguration->regularizerType)
                ->setSolver($configuration->logRegConfiguration->solver);
        }
        if ($configuration->linRegConfiguration) {
            $conf = $model->getLinRegConfiguration();
            $conf->setAlpha($configuration->linRegConfiguration->alpha)
                ->setRegularizationType($configuration->linRegConfiguration->regularizationType);
        }
        if ($configuration->svmConfiguration) {
            $conf = $model->getSvmConfiguration();
            $conf->setC($configuration->svmConfiguration->c)
                ->setKernel($configuration->svmConfiguration->kernel)
                ->setDegree($configuration->svmConfiguration->degree);
        }
        if ($configuration->hyperparameters) {
            $model->setHyperparameters((array) $configuration->hyperparameters);
        }
        $model->setUpdatedate(new \DateTime());
        $this->entityManager->flush();
    }

}