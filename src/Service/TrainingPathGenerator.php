<?php

namespace App\Service;

class TrainingPathGenerator
{

    /**
     * Filepattern:
     * - path to directory ending on "/"
     * - filename-prefix: describes the type of file, e.g. script, report, log
     * - lookup of the model
     * - uniquieId
     * - extension
     */
    private const PATTERN = "%s%s_%s_%s.%s";

    private string $uniqueId;
    private string $lookup;

    public function __construct(
        private string $trainingPythonDir,
        private string $trainingCsvDir,
        private string $trainingReportDir,
        private string $checkpointDir,
        private string $modelDir,
        private string $scalerDir,
        private string $errorDir,
    )
    {
        $this->uniqueId = uniqid();
    }

    private function getFilepath(string $path, string $type, string $extension): string {
        return sprintf(
            self::PATTERN,
            $path,
            $type,
            $this->lookup,
            $this->uniqueId,
            $extension
        );
    }

    public function setLookup(string $lookup)
    {
        $this->lookup = $lookup;
    }

    public function getCsvFile(): string
    {
        return $this->trainingCsvDir . 'data_' . $this->lookup . '.txt';
    }

    public function getCheckpointFile(string $extension): string
    {
        return $this->getFilepath($this->checkpointDir, 'checkpoint', $extension);
    }

    public function getModelFile(string $extension): string
    {
        return $this->getFilepath($this->modelDir, 'model', $extension);
    }

    public function getScalerFile(string $extension): string
    {
        return $this->getFilepath($this->scalerDir, 'scaler', $extension);
    }

    public function getPythonFile(): string
    {
        return $this->getFilepath($this->trainingPythonDir, 'script', 'py');
    }

    public function getLogFile(): string
    {
        return $this->getFilepath($this->trainingReportDir, 'log', 'log');
    }

    public function getReportFile(): string
    {
        return $this->getFilepath($this->trainingReportDir, 'report', 'json');
    }

    public function getErrorFile(): string
    {
        return $this->getFilepath($this->errorDir, 'error', 'log');
    }


}