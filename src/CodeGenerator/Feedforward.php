<?php

namespace App\CodeGenerator;

use App\Entity\Layer;
use App\Enum\LayerTypes;
use App\Service\TrainingPathGenerator;

class Feedforward extends AbstractCodegenerator
{

    /**
     * @throws \Exception
     */

    public function generateTrainingScript(TrainingPathGenerator $pathGenerator): string
    {
        $uploadFile = $this->model->getUploadFile();
        $targetName = $uploadFile->getTargetName();
        $features = $uploadFile->getFeatures();
        if ($targetName === '') {
            throw new \Exception('invalid field configuration');
        }

        $hyperparameter = $this->model->getHyperparameters();

        $lines = [];
        $lines[] = '# deaktiviert Info-Meldungen von Tensorflow';
        $lines[] = 'import os';
        $lines[] = 'os.environ["TF_CPP_MIN_LOG_LEVEL"] = "3"';
        $lines[] = '';
        $lines[] = "import logging";
        $lines[] = "import pandas as pd";
        $lines[] = "import numpy as np";
        $lines[] = "import tensorflow as tf";
        $lines[] = "from tensorflow.keras.models import Sequential";
        $lines[] = "from tensorflow.keras.layers import Dense, Dropout";
        $lines[] = "from tensorflow.keras.callbacks import ModelCheckpoint";
        $lines[] = "from tensorflow.keras.callbacks import CSVLogger";
        $lines[] = "from sklearn.preprocessing import StandardScaler";
        $lines[] = "from sklearn.model_selection import train_test_split";
        $lines[] = "from tensorflow.keras.callbacks import EarlyStopping";
        $lines[] = '';
        $lines[] = '# Logging-Konfiguration';
        $lines[] = 'logging.basicConfig(filename="/var/www/html/backend/data/training/python/training_errors.log", level=logging.ERROR)';

        $lines[] = '';
        $lines[] = 'try:';

        $innerLines[] = '# initialisiert Logger für Trainingsfortschritt';
        $innerLines[] = sprintf(
            'csv_logger = CSVLogger("%s", append=True)',
            $logFile,
        );
        $innerLines = [];
        $innerLines[] = "model = Sequential()";

        $innerLines[] = '';
        $innerLines[] = sprintf(
            'data = pd.read_csv("%s", delimiter=";", header=0 if %s else None)',
            $dataPath,
            $uploadFile->isContainsHeader()
        );
        $innerLines[] = sprintf('target = data["%s"]',
            $targetName
        );

        $innerLines[] = sprintf(
            'features = data[[%s]]',
            implode(
                ', ',
                array_map(function (string $name) {
                    return '"' . $name . '"';
                }, $features)
            )
        );

        $innerLines[] = '';
        $innerLines[] = sprintf(
            'features_train, features_test, target_train, target_test = train_test_split(features, target, test_size=%s, random_state=42)',
            1 - ($hyperparameter['trainingPercentage'] / 100)
        );
        if ((int) $hyperparameter['testPercentage'] > 0) {
            $split = $hyperparameter['testPercentage'] / ($hyperparameter['testPercentage'] + $hyperparameter['validationPercentage']);
            $innerLines[] = sprintf(
                'features_train, x_val, target_train, y_val = train_test_split(features_train, target_train, test_size=%s, random_state=42)',
                $split
            );
        }

        foreach ($this->model->getLayers() as $layer) {
            $innerLines[] = $this->getLayerCode($layer);
        }
        $innerLines[] = '# adding a dense layer with a single neuron for the result';
        $innerLines[] = 'model.add(Dense(1))';

        $innerLines[] = '';
        $innerLines[] = ' # HyperParameter';
        $innerLines[] = sprintf(
            'optimizer = tf.keras.optimizers.%s (learning_rate=%s)',
            $this->getOptimizer($hyperparameter['optimizer']),
            $hyperparameter['learningRate']
        );
        $innerLines[] = sprintf('loss = "%s"', $this->getLossFunction($hyperparameter['costFunction']), );
        $innerLines[] = sprintf('batch_size = %s', (int) $hyperparameter['batchSize'], );
        $innerLines[] = sprintf('epochs = %s', (int) $hyperparameter['epochs'], );

        if ($hyperparameter['patience']) {
            $innerLines[] = '';
            $innerLines[] = '# defining an early-stop mechanism';
            $innerLines[] = '# training stops early, if monitored value does not improve for ' . $hyperparameter['patience'] . ' epochs';
            $innerLines[] = sprintf(
                'early_stop = EarlyStopping(monitor = "val_loss", patience = %s)',
                $hyperparameter['patience']
            );
        }

        $innerLines[] = '';
        $innerLines[] = 'model.compile(optimizer=optimizer, loss=loss)';

        $innerLines[] = '';
        $innerLines[] = sprintf(
            "history = model.fit(features_train, target_train, validation_data = (features_test, target_test), epochs = %s, batch_size = %s, verbose = 1, callbacks = [csv_logger %s])",
            $hyperparameter['epochs'],
            $hyperparameter['batchSize'],
            $hyperparameter['patience'] > 0 ? ', early_stop' : '',
        );

        $innerLines[] = '';
        $innerLines[] = 'loss = model.evaluate(features_test, target_test)';
        $innerLines[] = 'print(f"Test Loss: {loss}")';

        $formattedInnerLines = array_map(function (string $line) {
            return '    ' . $line;
        }, $innerLines);

        $endLines = [];
        $endLines[] = 'except Exception as e:';
        $endLines[] = '    logging.error("Exception occurred", exc_info=True)';
        $endLines[] = '    print(f"An error occurred: {e}")';

        $result = implode(PHP_EOL, $lines) . PHP_EOL
            . implode(PHP_EOL, $formattedInnerLines) . PHP_EOL
            . implode(PHP_EOL, $endLines);
        return $result;
    }


    /**
     * @throws \Exception
     */
    private function getLayerCode(Layer $layer): string {
        $pythonCode = 'model.add(';
        $type = LayerTypes::from($layer->getType());
        if ($type === LayerTypes::LAYER_TYPE_DENSE) {
            $pythonCode .= sprintf("Dense(units=%s, activation='%s'", $layer->getNeuronCount(), $this->getActivationFunction($layer->getActivationFunction()));
            if ($layer->getRegularizationType() != "none" && $layer->getRegularizationLambda() > 0) {
                $pythonCode .= sprintf(", kernel_regularizer=tf.keras.regularizers.%s(%s)", $layer->getRegularizationType(), $layer->getRegularizationLambda());
            }
            $pythonCode .= "))";
        }
        if ($type === LayerTypes::LAYER_TYPE_DROPOUT) {
            $pythonCode .= sprintf("Dropout(%s))", $layer->getDropoutQuote() / 100);
        }

        return $pythonCode;
    }

}