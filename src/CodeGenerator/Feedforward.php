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
     *
     *  python script terminates with errorcode 500 in case of error
     */
    public function generateTrainingScript(TrainingPathGenerator $pathGenerator): string
    {
        $targetName = htmlentities($this->getTargetName());

        $textFeatures = array_map('htmlentities', $this->getTextFeatures());
        $numericalFeatures = array_map('htmlentities', $this->getNumericalFeatures());

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
        $lines[] = "from sklearn.preprocessing import OneHotEncoder";
        $lines[] = "from sklearn.model_selection import train_test_split";
        $lines[] = "from tensorflow.keras.callbacks import EarlyStopping";
        $lines[] = "from joblib import dump";

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
            'categorical_columns = [%s]',
            implode(', ', array_map(function (string $name) { return '"' . $name . '"'; }, $textFeatures))
        );
        $innerLines[] = sprintf(
            'number_features = data[[%s]]',
            implode(', ', array_map(function (string $name) { return '"' . $name . '"'; }, $numericalFeatures))
        );

        $innerLines[] = '';
        $innerLines[] = '# gathering total categories for encoder';
        $innerLines[] = 'categories = []';
        $innerLines[] = 'for column in categorical_columns:';
        $innerLines[] = '    unique_values = data[column].unique().tolist()';
        $innerLines[] = '    categories.append(unique_values)';
        $innerLines[] = '';
        $innerLines[] = 'encoder = OneHotEncoder(categories=categories, sparse=False, handle_unknown="ignore")';
        $innerLines[] = '';

        $innerLines[] = 'text_features = data[categorical_columns]';
        $innerLines[] = 'text_features_encoded = encoder.fit_transform(text_features)';
        $innerLines[] = 'scaler = StandardScaler()';
        $innerLines[] = 'number_features_scaled = scaler.fit_transform(number_features)';
        $innerLines[] = sprintf(
            "dump(scaler, '%s')",
            $this->model->getScalerPath()
        );
        $innerLines[] = sprintf(
            'dump(encoder, "%s")',
            $this->model->getEncoderPath()
        );
        $innerLines[] = 'features = np.concatenate([text_features_encoded, number_features_scaled], axis=1)';
        $innerLines[] = '';
        if ((int) $hyperparameter['testPercentage'] > 0) {
            $innerLines[] = sprintf(
                'features_train, features_temp, target_train, target_temp = train_test_split(features, target, test_size=%s, random_state=42)',
                1 - ($hyperparameter['trainingPercentage'] / 100)
            );
            $testPercentage = $hyperparameter['testPercentage'] / (100 - $hyperparameter['trainingPercentage']);
            $innerLines[] = sprintf(
                'features_val, features_test, target_val, target_test = train_test_split(features_temp, target_temp, test_size=%s, random_state=42)',
                $testPercentage
            );
        } else {
            $innerLines[] = sprintf(
                'features_train, features_val, target_train, target_val = train_test_split(features, target, test_size=%s, random_state=42)',
                1 - ($hyperparameter['trainingPercentage'] / 100)
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
            $this->model->getCheckpointPath()
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
            "history = model.fit(features_train, target_train, validation_data = (features_val, target_val), epochs = %s, batch_size = %s, verbose = 2, callbacks = [checkpoint, csv_logger %s])",
            $hyperparameter['epochs'],
            $hyperparameter['batchSize'],
            $hyperparameter['patience'] > 0 ? ', early_stop' : '',
        );

        $innerLines[] = '';
        $innerLines[] = 'loss = model.evaluate(features_val, target_val)';
        $innerLines[] = 'predictions = model.predict(features_val)';
        $innerLines[] = 'r2 = r2_score(target_val, predictions)';
        $innerLines[] = '';
        $innerLines[] = 'end_time = time.time()';
        $innerLines[] = '';
        $innerLines[] = '# saving model';
        $innerLines[] = sprintf('model.save("%s")', $pathGenerator->getModelFile("h5"));
        $innerLines[] = '';
        $innerLines[] = '# logging the result';
        $innerLines[] = 'results = {';
        $innerLines[] = '    "loss": loss,';
        $innerLines[] = '    "r2_score_val": r2,';
        $innerLines[] = '    "scatterplot_val": plot_predictions_vs_actuals(features_val, target_val, predictions),';
        $innerLines[] = '    "epochs_completed": len(history.history["loss"]),';
        $innerLines[] = '    "learning_curves": plot_learning_curves(history),';
        $innerLines[] = '    "duration": end_time - start_time';
        $innerLines[] = '}';

        if ($hyperparameter['patience'] > 0) {
            $innerLines[] = 'results["stopped_epoch"] = early_stop.stopped_epoch';
            $innerLines[] = 'results["best_val_loss"] = min(history.history["val_loss"]) if early_stop.stopped_epoch else history.history["val_loss"][-1]';
        } else {
            $innerLines[] = 'results["stopped_epoch"] = 0';
            $innerLines[] = 'results["best_val_loss"] = min(history.history["val_loss"])';
        }

        if ((int) $hyperparameter['testPercentage'] > 0) {
            $innerLines[] = 'predictions_test = model.predict(features_test)';
            $innerLines[] = 'results["scatterplot_test"] = plot_predictions_vs_actuals(features_test, target_test, predictions_test)';
            $innerLines[] = 'results["r2_score_test"] = r2_score(target_test, predictions_test)';
        } else {
            $innerLines[] = 'results["scatterplot_test"] = ""';
            $innerLines[] = 'results["r2_score_test"] = 0';
        }
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
        $endLines[] = '    sys.exit(500)';

        $result = implode(PHP_EOL, $lines) . PHP_EOL
            . implode(PHP_EOL, $innerLines) . PHP_EOL
            . implode(PHP_EOL, $endLines);
        return $result;
    }

    public function getExampleScript(): string
    {
        $targetName = htmlentities($this->getTargetName());

        $textFeatures = array_map('htmlentities', $this->getTextFeatures());
        $numericalFeatures = array_map('htmlentities', $this->getNumericalFeatures());
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
        $lines[] = "from sklearn.preprocessing import OneHotEncoder";
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
            'text_features = data[[%s]]',
            implode(', ', array_map(function (string $name) { return '"' . $name . '"'; }, $textFeatures))
        );
        $innerLines[] = sprintf(
            'number_features = data[[%s]]',
            implode(', ', array_map(function (string $name) { return '"' . $name . '"'; }, $numericalFeatures))
        );
        $innerLines[] = 'encoder = OneHotEncoder(sparse=False)';
        $innerLines[] = 'text_features_encoded = encoder.fit_transform(text_features)';
        $innerLines[] = 'scaler = StandardScaler()';
        $innerLines[] = 'number_features_scaled = scaler.fit_transform(number_features)';
        $innerLines[] = 'features = np.concatenate([text_features_encoded, number_features_scaled], axis=1)';
        $innerLines[] = "dump(scaler, '__SCALER_FILE__')";

        $innerLines[] = '';
        if ((int) $hyperparameter['testPercentage'] > 0) {
            $innerLines[] = sprintf(
                'features_train, features_temp, target_train, target_temp = train_test_split(features, target, test_size=%s, random_state=42)',
                1 - ($hyperparameter['trainingPercentage'] / 100)
            );
            $testPercentage = $hyperparameter['testPercentage'] / (100 - $hyperparameter['trainingPercentage']);
            $innerLines[] = sprintf(
                'features_val, features_test, target_val, target_test = train_test_split(features_temp, target_temp, test_size=%s, random_state=42)',
                $testPercentage
            );
        } else {
            $innerLines[] = sprintf(
                'features_train, features_val, target_train, target_val = train_test_split(features, target, test_size=%s, random_state=42)',
                1 - ($hyperparameter['trainingPercentage'] / 100)
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
            "history = model.fit(features_train, target_train, validation_data = (features_val, target_val), epochs = %s, batch_size = %s, verbose = 1, callbacks = [csv_logger %s])",
            $hyperparameter['epochs'],
            $hyperparameter['batchSize'],
            $hyperparameter['patience'] > 0 ? ', early_stop' : '',
        );

        $innerLines[] = '';
        $innerLines[] = 'loss = model.evaluate(features_val, target_val)';


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

    public function generateApplicationScript(string $sourceFile, string $targetFile): string
    {
        $textFeatures = array_map('htmlentities', $this->getTextFeatures());
        $numericalFeatures = array_map('htmlentities', $this->getNumericalFeatures());

        $lines = [];
        $lines[] = '# deaktiviert Info-Meldungen von Tensorflow';
        $lines[] = 'import os';
        $lines[] = 'os.environ["TF_CPP_MIN_LOG_LEVEL"] = "3"';
        $lines[] = '';
        $lines[] = "from joblib import load";
        $lines[] = "import tensorflow as tf";
        $lines[] = "from sklearn.preprocessing import OneHotEncoder, StandardScaler";
        $lines[] = "import numpy as np";
        $lines[] = "import pandas as pd";

        $lines[] = '';
        $lines[] = '# loading model';
        $lines[] = sprintf('model = tf.keras.models.load_model("%s")', $this->model->getModelPath());
        $lines[] = '# loading scaler';
        $lines[] = sprintf('scaler = load("%s")', $this->model->getScalerPath());
        $lines[] = '# loading encoder';
        $lines[] = sprintf('encoder = load("%s")', $this->model->getEncoderPath());

        $lines[] = '';
        $lines[] = '# loading source';
        $lines[] = sprintf('data = pd.read_csv("%s", delimiter=";", header=0)', $sourceFile);

        $lines[] = '';
        $lines[] = sprintf(
            'text_features = data[[%s]]',
            implode(', ', array_map(function (string $name) { return '"' . $name . '"'; }, $textFeatures))
        );
        $lines[] = sprintf(
            'number_features = data[[%s]]',
            implode(', ', array_map(function (string $name) { return '"' . $name . '"'; }, $numericalFeatures))
        );
        $lines[] = '';
        $lines[] = 'text_features_encoded = encoder.transform(text_features)';
        $lines[] = 'number_features_scaled = scaler.transform(number_features)';
        $lines[] = 'features = np.concatenate([text_features_encoded, number_features_scaled], axis=1)';

        $lines[] = '';
        $lines[] = '# executing predictions';
        $lines[] = 'predictions = model.predict(features)';

        $lines[] = '';
        $lines[] = '# saving predictions';
        $lines[] = 'result_df = pd.DataFrame(predictions, columns=["Prediction"])';
        $lines[] = sprintf('result_df.to_csv("%s", index=False)', $targetFile);
        $result = implode(PHP_EOL, $lines);

        return $result;
    }

    public function getExampleApplicationScript(): string
    {
        $textFeatures = array_map('htmlentities', $this->getTextFeatures());
        $numericalFeatures = array_map('htmlentities', $this->getNumericalFeatures());

        $lines = [];
        $lines[] = '';
        $lines[] = "from joblib import load";
        $lines[] = "import tensorflow as tf";
        $lines[] = "from sklearn.preprocessing import OneHotEncoder, StandardScaler";
        $lines[] = "import numpy as np";
        $lines[] = "import pandas as pd";

        $lines[] = '';
        $lines[] = '# loading model';
        $lines[] = 'model = tf.keras.models.load_model("__MODEL_FILE__")';
        $lines[] = '# loading scaler';
        $lines[] = 'scaler = load("__SCALER_FILE__")';
        $lines[] = '# loading encoder';
        $lines[] = 'encoder = load("__ENCODER_FILE__")';

        $lines[] = '';
        $lines[] = '# loading source';
        $lines[] = 'data = pd.read_csv("__SOURCE_CSV_FILE__", delimiter=";", header=0)';

        $lines[] = '';
        $lines[] = sprintf(
            'text_features = data[[%s]]',
            implode(', ', array_map(function (string $name) { return '"' . $name . '"'; }, $textFeatures))
        );
        $lines[] = sprintf(
            'number_features = data[[%s]]',
            implode(', ', array_map(function (string $name) { return '"' . $name . '"'; }, $numericalFeatures))
        );
        $lines[] = '';
        $lines[] = 'text_features_encoded = encoder.transform(text_features)';
        $lines[] = 'number_features_scaled = scaler.transform(number_features)';
        $lines[] = 'features = np.concatenate([text_features_encoded, number_features_scaled], axis=1)';

        $lines[] = '';
        $lines[] = '# executing predictions';
        $lines[] = 'predictions = model.predict(features)';

        $lines[] = '';
        $lines[] = '# saving predictions';
        $lines[] = 'result_df = pd.DataFrame(predictions, columns=["Prediction"])';
        $lines[] = 'result_df.to_csv("__TARGET_CSV_FILE__", index=False)';
        $result = implode(PHP_EOL, $lines);

        return $result;
    }


}