<?php

namespace App\CodeGenerator;

use App\Service\TrainingPathGenerator;

class LinearRegression extends AbstractCodegenerator
{

    /**
     * @param TrainingPathGenerator $pathGenerator
     * @return string
     * @throws \Exception
     * @todo Import-Statements je nach Regularisierungstyp aussteuern
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
        $modelPath = $pathGenerator->getModelFile('joblib');
        $reportFile = $pathGenerator->getReportFile();

        $hyperparameter = $this->model->getHyperparameters();
        $conf = $this->model->getLinRegConfiguration();

        $innerLines = [];

        $lines = [];
        $lines[] = '# deaktiviert Info-Meldungen von Tensorflow';
        $lines[] = 'import os';
        $lines[] = 'os.environ["TF_CPP_MIN_LOG_LEVEL"] = "3"';
        $lines[] = '';
        $lines[] = "import time";
        $lines[] = "import logging";
        $lines[] = "import base64";
        $lines[] = "from io import BytesIO";
        $lines[] = "import json";
        $lines[] = "import pandas as pd";
        $lines[] = "import matplotlib.pyplot as plt";
        $lines[] = "import numpy as np";
        $lines[] = "from sklearn.model_selection import train_test_split";
        $lines[] = "from sklearn.linear_model import Lasso";
        $lines[] = "from sklearn.linear_model import LinearRegression";
        $lines[] = "from sklearn.linear_model import Ridge";
        $lines[] = "from sklearn.metrics import mean_squared_error, r2_score";
        $lines[] = "from joblib import dump";
        $lines[] = "from sklearn.preprocessing import StandardScaler";
        $lines[] = "from sklearn.preprocessing import OneHotEncoder";
        $lines[] = '';
        $lines[] = '# Logging-Konfiguration';
        $lines[] = sprintf(
            'logging.basicConfig(filename="%s", level=logging.ERROR)',
            $pathGenerator->getErrorFile()
        );

        $lines[] = '';
        $lines[] = 'def plot_to_base64(plt):';
        $lines[] = '    img = BytesIO()';
        $lines[] = '    plt.savefig(img, format="png")';
        $lines[] = '    img.seek(0)';
        $lines[] = '    return base64.b64encode(img.read()).decode("utf-8")';
        $lines[] = '';

        $lines[] = 'try:';

        $innerLines[] = 'start_time = time.time()';
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

        $innerLines[] = '';
        $innerLines[] = '# initiate the model';

        if ($conf->getRegularizationType() === "lasso") {
            $innerLines[] = sprintf(
                'model = Lasso(alpha = %s, max_iter = %s, tol = %s)',
                $conf->getAlpha(),
                $hyperparameter['maxIterations'],
                $hyperparameter['tolerance']
            );
        } elseif ($conf->getRegularizationType() === "ridge") {
            $innerLines[] = sprintf(
                'model = Ridge(alpha = %s, max_iter = %s, tol = %s)',
                $conf->getAlpha(),
                $hyperparameter['maxIterations'],
                $hyperparameter['tolerance']
            );
        } else {
            $innerLines[] = 'model = LinearRegression()';
        }
        $innerLines[] = '';
        $innerLines[] = '# execute training';
        $innerLines[] = 'model.fit(features_train, target_train)';

        $innerLines[] = '';
        $innerLines[] = 'end_time = time.time()';

        $innerLines[] = '';
        $innerLines[] = '# gather validation data';
        $innerLines[] = 'target_pred_val = model.predict(features_val)';
        $innerLines[] = 'mse_val = mean_squared_error(target_val, target_pred_val)';
        $innerLines[] = 'r2_val = r2_score(target_val, target_pred_val)';

        $innerLines[] = '';
        $innerLines[] = '# generating plot';
        $innerLines[] = 'plt.scatter(target_val, target_pred_val)';
        $innerLines[] = 'plt.xlabel("True Values")';
        $innerLines[] = 'plt.ylabel("Predictions")';
        $innerLines[] = 'plt.axis("equal")';
        $innerLines[] = 'plt.axis("square")';
        $innerLines[] = 'plt.plot([-100, 100], [-100, 100])';
        $innerLines[] = 'scatterplot_val = plot_to_base64(plt)';
        $innerLines[] = 'plt.close()';

        $innerLines[] = '# saving model';
        $innerLines[] = sprintf('dump(model, "%s")', $modelPath);
        $innerLines[] = '';
        $innerLines[] = '# logging';
        $innerLines[] = 'results = {';
        $innerLines[] = '    "mse_val": mse_val,';
        $innerLines[] = '    "mse_test": 0,';
        $innerLines[] = '    "r2_val": r2_val,';
        $innerLines[] = '    "r2_test": 0,';
        $innerLines[] = '    "scatterplot_val": scatterplot_val,';
        $innerLines[] = '    "scatterplot_test": "",';
        $innerLines[] = '    "duration": end_time - start_time';
        $innerLines[] = '}';

        if ((int) $hyperparameter['testPercentage'] > 0) {
            $innerLines[] = '';
            $innerLines[] = '# gather performance data';
            $innerLines[] = 'target_pred_test = model.predict(features_test)';
            $innerLines[] = 'mse_test = mean_squared_error(target_test, target_pred_test)';
            $innerLines[] = 'r2_test = r2_score(target_test, target_pred_test)';

            $innerLines[] = '';
            $innerLines[] = '# generating plot';
            $innerLines[] = 'plt.scatter(target_test, target_pred_test)';
            $innerLines[] = 'plt.xlabel("True Values")';
            $innerLines[] = 'plt.ylabel("Predictions")';
            $innerLines[] = 'plt.axis("equal")';
            $innerLines[] = 'plt.axis("square")';
            $innerLines[] = 'plt.plot([-100, 100], [-100, 100])';
            $innerLines[] = 'scatterplot_test = plot_to_base64(plt)';
            $innerLines[] = 'plt.close()';

            $innerLines[] = 'results["mse_test"] = mse_test';
            $innerLines[] = 'results["r2_test"] = r2_test';
            $innerLines[] = 'results["scatterplot_test"] = scatterplot_test';
        }

        $innerLines[] = sprintf('with open("%s", "w") as outfile:', $reportFile);
        $innerLines[] = '    json.dump(results, outfile, indent=4)';

        $formattedInnerLines = array_map(function (string $line) {
            return '    ' . $line;
        }, $innerLines);

        $endLines = [];
        $endLines[] = 'except Exception as e:';
        $endLines[] = '    logging.error("Exception occurred", exc_info=True)';
        $endLines[] = '    sys.exit(500)';

        $result = implode(PHP_EOL, $lines) . PHP_EOL
            . implode(PHP_EOL, $formattedInnerLines) . PHP_EOL
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
        $conf = $this->model->getLinRegConfiguration();
        $split = $hyperparameter['testPercentage'] / ($hyperparameter['testPercentage'] + $hyperparameter['validationPercentage']);

        $innerLines = [];

        $lines = [];
        $lines[] = "import time";
        $lines[] = "import logging";
        $lines[] = "import base64";
        $lines[] = "from io import BytesIO";
        $lines[] = "import json";
        $lines[] = "import pandas as pd";
        $lines[] = "import matplotlib.pyplot as plt";
        $lines[] = "import numpy as np";
        $lines[] = "from sklearn.model_selection import train_test_split";
        $lines[] = "from sklearn.linear_model import Lasso";
        $lines[] = "from sklearn.linear_model import LinearRegression";
        $lines[] = "from sklearn.linear_model import Ridge";
        $lines[] = "from sklearn.metrics import mean_squared_error, r2_score";
        $lines[] = "from joblib import dump";
        $lines[] = '';
        $lines[] = '# Logging-Konfiguration';
        $lines[] = 'logging.basicConfig(filename="__ERROR_FILE__", level=logging.ERROR)';

        $lines[] = '';
        $lines[] = 'def plot_to_base64(plt):';
        $lines[] = '    img = BytesIO()';
        $lines[] = '    plt.savefig(img, format="png")';
        $lines[] = '    img.seek(0)';
        $lines[] = '    return base64.b64encode(img.read()).decode("utf-8")';
        $lines[] = '';

        $lines[] = 'try:';

        $innerLines[] = 'start_time = time.time()';
        $innerLines[] = '';
        $innerLines[] = 'data = pd.read_csv(__CSV_FILE__", delimiter=";", header=0)';
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
        $innerLines[] = 'train_size = ' . $hyperparameter['trainingPercentage'] / 100;
        $innerLines[] = 'features_train, features_temp, target_train, target_temp = train_test_split(features, target, test_size=1-train_size)';
        if ($split > 0) {
            $innerLines[] = sprintf(
                'features_val, features_test, target_val, target_test  = train_test_split(features_temp, target_temp, test_size=%s)',
                $split
            );
        } else {
            $innerLines[] = 'features_val, target_val = features_temp, target_temp';
        }

        $innerLines[] = '';
        $innerLines[] = '# initiate the model';

        if ($conf->getRegularizationType() === "lasso") {
            $innerLines[] = sprintf(
                'model = Lasso(alpha = %s, max_iter = %s, tol = %s)',
                $conf->getAlpha(),
                $hyperparameter['maxIterations'],
                $hyperparameter['tolerance']
            );
        } elseif ($conf->getRegularizationType() === "ridge") {
            $innerLines[] = sprintf(
                'model = Ridge(alpha = %s, max_iter = %s, tol = %s)',
                $conf->getAlpha(),
                $hyperparameter['maxIterations'],
                $hyperparameter['tolerance']
            );
        } else {
            $innerLines[] = 'model = LinearRegression()';
        }
        $innerLines[] = '';
        $innerLines[] = '# execute training';
        $innerLines[] = 'model.fit(features_train, target_train)';

        $innerLines[] = '';
        $innerLines[] = 'end_time = time.time()';

        $innerLines[] = '';
        $innerLines[] = '# gather validation data';
        $innerLines[] = 'target_pred_val = model.predict(features_val)';
        $innerLines[] = 'mse_val = mean_squared_error(target_val, target_pred_val)';
        $innerLines[] = 'r2_val = r2_score(target_val, target_pred_val)';

        $innerLines[] = '';
        $innerLines[] = '# generating plot';
        $innerLines[] = 'plt.scatter(target_val, target_pred_val)';
        $innerLines[] = 'plt.xlabel("True Values")';
        $innerLines[] = 'plt.ylabel("Predictions")';
        $innerLines[] = 'plt.axis("equal")';
        $innerLines[] = 'plt.axis("square")';
        $innerLines[] = 'plt.plot([-100, 100], [-100, 100])';
        $innerLines[] = 'scatterplot_val = plot_to_base64(plt)';
        $innerLines[] = 'plt.close()';

        $innerLines[] = '# saving model';
        $innerLines[] = 'dump(model, "__MODEL_FILE__")';
        $innerLines[] = '';
        $innerLines[] = '# logging';
        $innerLines[] = 'results = {';
        $innerLines[] = '    "mse_val": mse_val,';
        $innerLines[] = '    "mse_test": 0,';
        $innerLines[] = '    "r2_val": r2_val,';
        $innerLines[] = '    "r2_test": 0,';
        $innerLines[] = '    "scatterplot_val": scatterplot_val,';
        $innerLines[] = '    "scatterplot_test": "",';
        $innerLines[] = '    "duration": end_time - start_time';
        $innerLines[] = '}';

        if ((int) $hyperparameter['testPercentage'] > 0) {

            $innerLines[] = '';
            $innerLines[] = '# gather performance data';
            $innerLines[] = 'target_pred_test = model.predict(features_test)';
            $innerLines[] = 'mse_test = mean_squared_error(target_test, target_pred_test)';
            $innerLines[] = 'r2_test = r2_score(target_test, target_pred_test)';

            $innerLines[] = '';
            $innerLines[] = '# generating plot';
            $innerLines[] = 'plt.scatter(target_test, target_pred_test)';
            $innerLines[] = 'plt.xlabel("True Values")';
            $innerLines[] = 'plt.ylabel("Predictions")';
            $innerLines[] = 'plt.axis("equal")';
            $innerLines[] = 'plt.axis("square")';
            $innerLines[] = 'plt.plot([-100, 100], [-100, 100])';
            $innerLines[] = 'scatterplot_test = plot_to_base64(plt)';
            $innerLines[] = 'plt.close()';

            $innerLines[] = '';
            $innerLines[] = 'results["mse_test"] = mse_test';
            $innerLines[] = 'results["r2_test"] = r2_test';
            $innerLines[] = 'results["scatterplot_test"] = scatterplot_test';
        }

        $innerLines[] = 'with open("__REPORT_FILE__", "w") as outfile:';
        $innerLines[] = '    json.dump(results, outfile, indent=4)';

        $formattedInnerLines = array_map(function (string $line) {
            return '    ' . $line;
        }, $innerLines);

        $endLines = [];
        $endLines[] = 'except Exception as e:';
        $endLines[] = '    logging.error("Exception occurred", exc_info=True)';

        $result = implode(PHP_EOL, $lines) . PHP_EOL
            . implode(PHP_EOL, $formattedInnerLines) . PHP_EOL
            . implode(PHP_EOL, $endLines);
        return $result;
    }

    public function generateApplicationScript(string $sourceFile, string $targetFile): string
    {
        return "";
    }


}