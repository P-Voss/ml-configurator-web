<?php

namespace App\CodeGenerator;

use App\Entity\Layer;
use App\Enum\LayerTypes;
use App\Service\TrainingPathGenerator;

class Feedforward extends AbstractCodegenerator
{

    /**
     * @throws \Exception
     * @todo messy code, split components into functions later to improve readability
     */
    public function generateTrainingScript(TrainingPathGenerator $pathGenerator): string
    {
        $targetName = htmlentities($this->getTargetName());
        $features = array_map('htmlentities', $this->getFeatures());
        if ($targetName === '') {
            throw new \Exception('invalid field configuration');
        }

        $hyperparameter = $this->model->getHyperparameters();

        $lines = [];
        $lines[] = '# deaktiviert Info-Meldungen von Tensorflow';
        $lines[] = 'import os';
        $lines[] = 'os.environ["TF_CPP_MIN_LOG_LEVEL"] = "3"';
        $lines[] = '';
        $lines[] = 'import sys';
        $lines[] = "import time";
        $lines[] = "import json";
        $lines[] = "import logging";
        $lines[] = "import base64";
        $lines[] = "from io import BytesIO";
        $lines[] = "import pandas as pd";
        $lines[] = "import matplotlib.pyplot as plt";
        $lines[] = "import numpy as np";
        $lines[] = "from sklearn.metrics import r2_score";
        $lines[] = "import tensorflow as tf";
        $lines[] = "from tensorflow.keras.models import Sequential";
        $lines[] = "from tensorflow.keras.layers import Dense, Dropout";
        $lines[] = "from tensorflow.keras.callbacks import ModelCheckpoint";
        $lines[] = "from tensorflow.keras.callbacks import CSVLogger";
        $lines[] = "from sklearn.preprocessing import StandardScaler";
        $lines[] = "from sklearn.model_selection import train_test_split";
        $lines[] = "from tensorflow.keras.callbacks import EarlyStopping";

        $lines[] = '';
        $lines[] = 'def plot_to_base64(plt):';
        $lines[] = '    img = BytesIO()';
        $lines[] = '    plt.savefig(img, format="png")';
        $lines[] = '    img.seek(0)';
        $lines[] = '    return base64.b64encode(img.read()).decode("utf-8")';
        $lines[] = '';

        $lines[] = '';
        $lines[] = 'def plot_predictions_vs_actuals(features_test, target_test, predictions):';
        $lines[] = '    plt.figure(figsize=(6,6))';
        $lines[] = '    plt.scatter(target_test, predictions, alpha=0.3)';
        $lines[] = '    plt.plot([min(target_test), max(target_test)], [min(target_test), max(target_test)], color="red")';
        $lines[] = '    plt.xlabel("Tatsächliche Werte")';
        $lines[] = '    plt.ylabel("Vorhersagen")';
        $lines[] = '    plt.title("Vorhersagen vs. Tatsächliche Werte")';
        $lines[] = '    plt.tight_layout()';
        $lines[] = '    return plot_to_base64(plt)';
        $lines[] = '';

        $lines[] = '';
        $lines[] = 'def plot_learning_curves(history):';
        $lines[] = '    plt.figure(figsize=(6,6))';
        $lines[] = '    plt.subplot(1,2,1)';
        $lines[] = '    plt.plot(history.history["loss"], label="Trainingsverlust")';
        $lines[] = '    plt.plot(history.history["val_loss"], label="Validierungsverlust")';
        $lines[] = '    plt.xlabel("Epoche")';
        $lines[] = '    plt.ylabel("Verlust")';
        $lines[] = '    plt.title("Lernkurven (Verlust)")';
        $lines[] = '    plt.legend()';
        $lines[] = '    plt.tight_layout()';
        $lines[] = '    return plot_to_base64(plt)';
        $lines[] = '';

        $lines[] = '# Vorheriges stdout-Objekt speichern';
        $lines[] = 'original_stdout = sys.stdout';
        $lines[] = '';
        $lines[] = '# Logging-Konfiguration';
        $lines[] = sprintf(
            'logging.basicConfig(filename="%s", level=logging.ERROR)',
            $pathGenerator->getErrorFile()
        );

        $lines[] = '';
        $lines[] = 'try:';

        $innerLines = [];
        $innerLines[] = 'start_time = time.time()';
        $innerLines[] = '';
        $innerLines[] = '# initialisiert Logger für Trainingsfortschritt';
        $innerLines[] = sprintf(
            'csv_logger = CSVLogger("%s", append=True)',
            $pathGenerator->getLogFile(),
        );
        $innerLines[] = "model = Sequential()";

        $innerLines[] = '';
        $innerLines[] = sprintf(
            'data = pd.read_csv("%s", delimiter=";", header=0)',
            $this->getDataPath($pathGenerator)
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


        $innerLines[] = '';
        $innerLines[] = sprintf(
            'checkpoint = ModelCheckpoint("%s", monitor="val_loss", verbose=1, save_best_only=True, mode="min")',
            $pathGenerator->getCheckpointFile('h5')
        );
        $innerLines[] = '';

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
            "history = model.fit(features_train, target_train, validation_data = (features_test, target_test), epochs = %s, batch_size = %s, verbose = 2, callbacks = [checkpoint, csv_logger %s])",
            $hyperparameter['epochs'],
            $hyperparameter['batchSize'],
            $hyperparameter['patience'] > 0 ? ', early_stop' : '',
        );

        $innerLines[] = '';
        $innerLines[] = 'loss = model.evaluate(features_test, target_test)';
        $innerLines[] = 'predictions = model.predict(features_test)';
        $innerLines[] = 'r2 = r2_score(target_test, predictions)';
        $innerLines[] = '';
        $innerLines[] = 'end_time = time.time()';
        $innerLines[] = '';
        $innerLines[] = '# saving model';
        $innerLines[] = sprintf('model.save("%s")', $pathGenerator->getModelFile("h5"));
        $innerLines[] = '';
        $innerLines[] = '# logging the result';
        $innerLines[] = 'results = {';
        $innerLines[] = '    "loss": loss,';
        $innerLines[] = '    "r2_score": r2,';
        $innerLines[] = '    "epochs_completed": len(history.history["loss"]),';
        $innerLines[] = '    "stopped_epoch": early_stop.stopped_epoch,';
        $innerLines[] = '    "best_val_loss": min(history.history["val_loss"]) if early_stop.stopped_epoch else history.history["val_loss"][-1],';
        $innerLines[] = '    "scatterplot": plot_predictions_vs_actuals(features_test, target_test, predictions),';
        $innerLines[] = '    "learning_curves": plot_learning_curves(history),';
        $innerLines[] = '    "duration": end_time - start_time';
        $innerLines[] = '}';
        $innerLines[] = sprintf('with open("%s", "w") as outfile:', $pathGenerator->getReportFile());
        $innerLines[] = '    json.dump(results, outfile, indent=4)';

        $formattedInnerLines = array_map(function (string $line) {
            return '    ' . $line;
        }, $innerLines);

        $innerLines = [];
        $innerLines[] = sprintf('    with open("%s", "w") as f:', $pathGenerator->getLogFile());
        $innerLines[] = '        sys.stdout = f';
        foreach ($formattedInnerLines as $line) {
            $innerLines[] = '    ' . $line;
        }
        $innerLines[] = '        sys.stdout = original_stdout';

        $endLines = [];
        $endLines[] = 'except Exception as e:';
        $endLines[] = '    logging.error("Exception occurred", exc_info=True)';

        $result = implode(PHP_EOL, $lines) . PHP_EOL
            . implode(PHP_EOL, $innerLines) . PHP_EOL
            . implode(PHP_EOL, $endLines);
        return $result;
    }

    public function getExampleScript(): string
    {
        $targetName = htmlentities($this->getTargetName());
        $features = array_map('htmlentities', $this->getFeatures());
        if ($targetName === '') {
            throw new \Exception('invalid field configuration');
        }

        $hyperparameter = $this->model->getHyperparameters();

        $lines = [];
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
        $lines[] = 'logging.basicConfig(filename=__ERROR_LOG_FILE__", level=logging.ERROR)';

        $lines[] = '';
        $lines[] = 'try:';

        $innerLines = [];
        $innerLines[] = '# initialisiert Logger für Trainingsfortschritt';
        $innerLines[] = 'csv_logger = CSVLogger("__CSV_LOG_FILE__", append=True)';
        $innerLines[] = 'start_time = time.time()';
        $innerLines[] = "model = Sequential()";

        $innerLines[] = '';
        $innerLines[] = 'data = pd.read_csv("__CSV_DATA_FILE__", delimiter=";", header=0)';
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