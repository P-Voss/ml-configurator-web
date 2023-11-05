<?php

namespace App\CodeGenerator;


use App\Service\TrainingPathGenerator;

class Svm extends AbstractCodegenerator
{

    /**
     * @param TrainingPathGenerator $pathGenerator
     * @return string
     * @throws \Exception
     * @todo datasplit does not use testset
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
        $lines[] = "from sklearn.svm import SVR";
        $lines[] = "from sklearn.metrics import mean_squared_error";
        $lines[] = "from sklearn.inspection import permutation_importance";
        $lines[] = "from joblib import dump";
        $lines[] = '';
        $lines[] = '# Logging-Konfiguration';
        $lines[] = sprintf(
            'logging.basicConfig(filename="%s", level=logging.ERROR)',
            $pathGenerator->getErrorFile()
        );

        $lines[] = '';
        $lines[] = 'def plot_to_base64():';
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
                'features_val, _, target_val, _ = train_test_split(features_temp, target_temp, test_size=%s)',
                $split
            );
        } else {
            $innerLines[] = 'features_val, target_val = features_temp, target_temp';
        }

        $innerLines[] = '';
        $innerLines[] = '# initiates svm model';
        $innerLines[] = sprintf(
            'svm_model = SVR(kernel="%s", C=%s, degree=%s, shrinking=%s, tol=%s)',
            $this->model->getSvmConfiguration()->getKernel(),
            $this->model->getSvmConfiguration()->getC() / 100,
            $this->model->getSvmConfiguration()->getDegree(),
            $hyperparameter['shrinking'] ? 'True' : 'False',
            $hyperparameter['tolerance']
        );
        $innerLines[] = '';
        $innerLines[] = '# initiates training';
        $innerLines[] = 'svm_model.fit(features_train, target_train)';
        $innerLines[] = '';
        $innerLines[] = '# runs prediction against validation data';
        $innerLines[] = 'target_pred = svm_model.predict(features_val)';
        $innerLines[] = '';
        $innerLines[] = '# calculates prediction error';
        $innerLines[] = 'mse = mean_squared_error(target_val, target_pred)';


        $innerLines[] = '';
        $innerLines[] = '# Scatterplot';
        $innerLines[] = 'plt.scatter(target_val, target_pred)';
        $innerLines[] = 'plt.xlabel("Tatsächliche Werte")';
        $innerLines[] = 'plt.ylabel("Vorhersagen")';
        $innerLines[] = 'plt.title("Vorhersage vs. Tatsächliche Werte")';
        $innerLines[] = 'scatterplot = plot_to_base64()';
        $innerLines[] = 'plt.close()';

        $innerLines[] = '';
        $innerLines[] = '# Residual Plot';
        $innerLines[] = 'residuals = target_val - target_pred';
        $innerLines[] = 'plt.scatter(target_pred, residuals)';
        $innerLines[] = 'plt.xlabel("Vorhersagen")';
        $innerLines[] = 'plt.ylabel("Residuen")';
        $innerLines[] = 'plt.title("Residual Plot")';
        $innerLines[] = 'plt.axhline(y=0, color="r", linestyle="--")';
        $innerLines[] = 'residuals_plot = plot_to_base64()';
        $innerLines[] = 'plt.close()';


        if ($this->model->getSvmConfiguration()->getKernel() === 'linear') {
            $innerLines[] = 'feature_importance = svm_model.coef_[0]';
            $innerLines[] = 'feature_importance_dict = dict(zip(features.columns, feature_importance))';
        } else {
            $innerLines[] = 'pfi_result = permutation_importance(svm_model, features_val, target_val, n_repeats=30, random_state=42)';
            $innerLines[] = 'sorted_idx = pfi_result.importances_mean.argsort()';
            $innerLines[] = 'feature_importance_dict = {}';
            $innerLines[] = 'for idx in sorted_idx:';
            $innerLines[] = '    feature_importance_dict[features.columns[idx]] = pfi_result.importances_mean[idx]';
        }

        $innerLines[] = 'end_time = time.time()';
        $innerLines[] = '# saving model';
        $innerLines[] = sprintf('dump(svm_model, "%s")', $modelPath);
        $innerLines[] = '';
        $innerLines[] = '# logging the result';
        $innerLines[] = 'results = {';
        $innerLines[] = '    "mse": mse,';
        $innerLines[] = '    "scatterplot": scatterplot,';
        $innerLines[] = '    "residuals": residuals_plot,';
        $innerLines[] = '    "feature_importance": feature_importance_dict,';
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
            $targetName = '__TARGET_FIELD__';
        }

        $hyperparameter = $this->model->getHyperparameters();
        $split = $hyperparameter['testPercentage'] / ($hyperparameter['testPercentage'] + $hyperparameter['validationPercentage']);

        $lines = [];
        $lines[] = '';
        $lines[] = "import time";
        $lines[] = "import logging";
        $lines[] = "import base64";
        $lines[] = "from io import BytesIO";
        $lines[] = "import json";
        $lines[] = "import pandas as pd";
        $lines[] = "import matplotlib.pyplot as plt";
        $lines[] = "from sklearn.model_selection import train_test_split";
        $lines[] = "from sklearn.svm import SVR";
        $lines[] = "from sklearn.metrics import mean_squared_error";
        $lines[] = "from sklearn.inspection import permutation_importance";
        $lines[] = "from joblib import dump";
        $lines[] = '';
        $lines[] = '# Logging-Konfiguration';
        $lines[] = 'logging.basicConfig(filename="__ERROR_LOG_FILE__", level=logging.ERROR)';
        $lines[] = '';

        $lines[] = 'def plot_to_base64():';
        $lines[] = '    img = BytesIO()';
        $lines[] = '    plt.savefig(img, format="png")';
        $lines[] = '    img.seek(0)';
        $lines[] = '    return base64.b64encode(img.read()).decode("utf-8")';

        $lines[] = 'try:';

        $innerLines[] = 'start_time = time.time()';
        $innerLines[] = '';
        $innerLines[] = sprintf(
            'data = pd.read_csv("__DATA_FILE__", delimiter=";", header=0 if %s else None)',
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
                'features_val, _, target_val, _ = train_test_split(features_temp, target_temp, test_size=%s)',
                $split
            );
        }

        $innerLines[] = '';
        $innerLines[] = '# initiates svm model';
        $innerLines[] = sprintf(
            'svm_model = SVR(kernel="%s", C=%s, degree=%s, shrinking=%s, tol=%s)',
            $this->model->getSvmConfiguration()->getKernel(),
            $this->model->getSvmConfiguration()->getC() / 100,
            $this->model->getSvmConfiguration()->getDegree(),
            $hyperparameter['shrinking'] ? 'True' : 'False',
            $hyperparameter['tolerance']
        );
        $innerLines[] = '';
        $innerLines[] = '# initiates training';
        $innerLines[] = 'svm_model.fit(features_train, target_train)';
        $innerLines[] = '';
        $innerLines[] = '# runs prediction against validation data';
        $innerLines[] = 'target_pred = svm_model.predict(features_val)';
        $innerLines[] = '';
        $innerLines[] = '# calculates prediction error';
        $innerLines[] = 'mse = mean_squared_error(target_val, target_pred)';


        $innerLines[] = '';
        $innerLines[] = '# Scatterplot';
        $innerLines[] = 'plt.scatter(target_val, target_pred)';
        $innerLines[] = 'plt.xlabel("Tatsächliche Werte")';
        $innerLines[] = 'plt.ylabel("Vorhersagen")';
        $innerLines[] = 'plt.title("Vorhersage vs. Tatsächliche Werte")';
        $innerLines[] = 'scatterplot = plot_to_base64()';
        $innerLines[] = 'plt.close()';

        $innerLines[] = '';
        $innerLines[] = '# Residual Plot';
        $innerLines[] = 'residuals = target_val - target_pred';
        $innerLines[] = 'plt.scatter(target_pred, residuals)';
        $innerLines[] = 'plt.xlabel("Vorhersagen")';
        $innerLines[] = 'plt.ylabel("Residuen")';
        $innerLines[] = 'plt.title("Residual Plot")';
        $innerLines[] = 'plt.axhline(y=0, color="r", linestyle="--")';
        $innerLines[] = 'residuals_plot = plot_to_base64()';
        $innerLines[] = 'plt.close()';


        if ($this->model->getSvmConfiguration()->getKernel() === 'linear') {
            $innerLines[] = 'feature_importance = svm_model.coef_[0]';
            $innerLines[] = 'feature_importance_dict = dict(zip(features.columns, feature_importance))';
        } else {
            $innerLines[] = 'pfi_result = permutation_importance(svm_model, features_val, target_val, n_repeats=30, random_state=42)';
            $innerLines[] = 'sorted_idx = pfi_result.importances_mean.argsort()';
            $innerLines[] = 'feature_importance_dict = {}';
            $innerLines[] = 'for idx in sorted_idx:';
            $innerLines[] = '    feature_importance_dict[features.columns[idx]] = pfi_result.importances_mean[idx]';
        }

        $innerLines[] = 'end_time = time.time()';
        $innerLines[] = '# saving model';
        $innerLines[] = 'dump(svm_model, "__MODEL_FILE__")';
        $innerLines[] = '';
        $innerLines[] = '# logging the result';
        $innerLines[] = 'results = {';
        $innerLines[] = '    "mse": mse,';
        $innerLines[] = '    "scatterplot": scatterplot,';
        $innerLines[] = '    "residuals": residuals_plot,';
        $innerLines[] = '    "feature_importance": feature_importance_dict,';
        $innerLines[] = '    "duration": end_time - start_time';
        $innerLines[] = '}';
        $innerLines[] = 'with open("__REPORT_JSON_FILE__", "w") as outfile:';
        $innerLines[] = '    json.dump(results, outfile, indent=4)';

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

}