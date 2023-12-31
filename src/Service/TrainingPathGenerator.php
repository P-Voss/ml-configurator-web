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
        private string $execPythonDir,
        private string $trainingCsvDir,
        private string $execCsvDir,
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

    public function getExecPythonFile(): string
    {
        return $this->getFilepath($this->execPythonDir, 'script', 'py');
    }

    public function getExecCsvFile(): string
    {
        return $this->execCsvDir . $this->lookup . '_source.csv';
    }

    public function getExecResultFile(): string
    {
        return $this->execCsvDir . $this->lookup . '_result.csv';
    }

    public function getCsvFile(string $filename): string
    {
        return $this->trainingCsvDir . $filename;
    }

    public function getCheckpointFile(string $extension): string
    {
        return $this->checkpointDir . 'checkpoint_' . $this->lookup . '.' . $extension;
    }

    public function getModelFile(string $extension): string
    {
        return $this->modelDir . 'model_' . $this->lookup . '.' . $extension;
    }

    public function getScalerFile(string $extension): string
    {
        return $this->scalerDir . 'scaler_' . $this->lookup . '.' . $extension;
    }

    public function getEncoderFile(string $extension): string
    {
        return $this->scalerDir . 'encoder_' . $this->lookup . '.' . $extension;
    }

    public function getLabelEncoderFile(string $extension): string
    {
        return $this->scalerDir . 'labelEncoder_' . $this->lookup . '.' . $extension;
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