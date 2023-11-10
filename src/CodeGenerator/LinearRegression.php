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
     */
    public function generateTrainingScript(TrainingPathGenerator $pathGenerator): string
    {
        $uploadFile = $this->model->getUploadFile();
        $targetName = $uploadFile->getTargetName();
        $features = $uploadFile->getFeatures();
        if ($targetName === '') {
            throw new \Exception('invalid field configuration');
        }
        $modelPath = $pathGenerator->getModelFile('joblib');
        $reportFile = $pathGenerator->getReportFile();

        $hyperparameter = $this->model->getHyperparameters();
        $conf = $this->model->getLinRegConfiguration();
        $split = $hyperparameter['testPercentage'] / ($hyperparameter['testPercentage'] + $hyperparameter['validationPercentage']);

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
        $lines[] = "from sklearn.model_selection import train_test_split";
        $lines[] = "from sklearn.linear_model import Lasso";
        $lines[] = "from sklearn.linear_model import LinearRegression";
        $lines[] = "from sklearn.linear_model import Ridge";
        $lines[] = "from sklearn.metrics import mean_squared_error, r2_score";
        $lines[] = "from joblib import dump";
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
            'data = pd.read_csv("%s", delimiter=";", header=0 if %s else None)',
            $pathGenerator->getCsvFile(),
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
        $innerLines[] = '# gather validation data';
        $innerLines[] = 'target_pred_val = model.predict(features_val)';
        $innerLines[] = 'mse_val = mean_squared_error(target_val, target_pred_val)';
        $innerLines[] = 'r2_val = r2_score(target_val, target_pred_val)';

        $innerLines[] = '';
        $innerLines[] = '# gather performance data';
        $innerLines[] = 'target_pred_test = model.predict(features_test)';
        $innerLines[] = 'mse_test = mean_squared_error(target_test, target_pred_test)';
        $innerLines[] = 'r2_test = r2_score(target_test, target_pred_test)';

        $innerLines[] = '';
        $innerLines[] = 'end_time = time.time()';

        $innerLines[] = '';
        $innerLines[] = '# generating plot';
        $innerLines[] = 'plt.scatter(target_test, target_pred_test)';
        $innerLines[] = 'plt.xlabel("True Values")';
        $innerLines[] = 'plt.ylabel("Predictions")';
        $innerLines[] = 'plt.axis("equal")';
        $innerLines[] = 'plt.axis("square")';
        $innerLines[] = 'plt.plot([-100, 100], [-100, 100])';
        $innerLines[] = 'scatterplot = plot_to_base64(plt)';
        $innerLines[] = 'plt.close()';

        $innerLines[] = '# saving model';
        $innerLines[] = sprintf('dump(model, "%s")', $modelPath);
        $innerLines[] = '';
        $innerLines[] = '# logging';
        $innerLines[] = 'results = {';
        $innerLines[] = '    "mse_val": mse_val,';
        $innerLines[] = '    "mse_test": mse_test,';
        $innerLines[] = '    "r2_val": r2_val,';
        $innerLines[] = '    "r2_test": r2_test,';
        $innerLines[] = '    "scatterplot": scatterplot,';
        $innerLines[] = '    "duration": end_time - start_time';
        $innerLines[] = '}';
        $innerLines[] = sprintf('with open("%s", "w") as outfile:', $reportFile);
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


    public function getExampleScript(): string
    {
        $uploadFile = $this->model->getUploadFile();
        $targetName = htmlentities($uploadFile->getTargetName());
        $features = array_map('htmlentities', $uploadFile->getFeatures());
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
        $innerLines[] = 'data = pd.read_csv(__CSV_FILE__", delimiter=";", header=0 if %s else None)';
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
        $innerLines[] = '# gather validation data';
        $innerLines[] = 'target_pred_val = model.predict(features_val)';
        $innerLines[] = 'mse_val = mean_squared_error(target_val, target_pred_val)';
        $innerLines[] = 'r2_val = r2_score(target_val, target_pred_val)';

        $innerLines[] = '';
        $innerLines[] = '# gather performance data';
        $innerLines[] = 'target_pred_test = model.predict(features_test)';
        $innerLines[] = 'mse_test = mean_squared_error(target_test, target_pred_test)';
        $innerLines[] = 'r2_test = r2_score(target_test, target_pred_test)';

        $innerLines[] = '';
        $innerLines[] = 'end_time = time.time()';

        $innerLines[] = '';
        $innerLines[] = '# generating plot';
        $innerLines[] = 'plt.scatter(target_test, target_pred_test)';
        $innerLines[] = 'plt.xlabel("True Values")';
        $innerLines[] = 'plt.ylabel("Predictions")';
        $innerLines[] = 'plt.axis("equal")';
        $innerLines[] = 'plt.axis("square")';
        $innerLines[] = 'plt.plot([-100, 100], [-100, 100])';
        $innerLines[] = 'scatterplot = plot_to_base64(plt)';
        $innerLines[] = 'plt.close()';

        $innerLines[] = '# saving model';
        $innerLines[] = 'dump(model, "__MODEL_FILE__")';
        $innerLines[] = '';
        $innerLines[] = '# logging';
        $innerLines[] = 'results = {';
        $innerLines[] = '    "mse_val": mse_val,';
        $innerLines[] = '    "mse_test": mse_test,';
        $innerLines[] = '    "r2_val": r2_val,';
        $innerLines[] = '    "r2_test": r2_test,';
        $innerLines[] = '    "scatterplot": scatterplot,';
        $innerLines[] = '    "duration": end_time - start_time';
        $innerLines[] = '}';
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

}