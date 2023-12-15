<?php

namespace App\CodeGenerator;


use App\Service\TrainingPathGenerator;

class Svm extends AbstractCodegenerator
{

    /**
     * @param TrainingPathGenerator $pathGenerator
     * @return string
     * @throws \Exception
     *
     *  python script terminates with errorcode 500 in case of error
     */
    public function generateTrainingScript(TrainingPathGenerator $pathGenerator): string
    {

        if ($this->targetIsText()) {
            return $this->getSvcScript($pathGenerator);
        } else {
            return $this->getSvrScript($pathGenerator);
        }

    }


    private function getSvrScript(TrainingPathGenerator $pathGenerator): string
    {
        $targetName = htmlentities($this->getTargetName());

        $textFeatures = array_map('htmlentities', $this->getTextFeatures());
        $numericalFeatures = array_map('htmlentities', $this->getNumericalFeatures());
        if ($targetName === '') {
            throw new \Exception('invalid field configuration');
        }
        $modelPath = $this->model->getModelPath();
        $reportFile = $pathGenerator->getReportFile();

        $hyperparameter = $this->model->getHyperparameters();

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

        $lines[] = "from sklearn.svm import SVR";

        $lines[] = "from sklearn.metrics import mean_squared_error";
        $lines[] = "from sklearn.inspection import permutation_importance";
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

        $innerLines[] = '';
        $innerLines[] = 'if hasattr(encoder, "get_feature_names"):';
        $innerLines[] = '    categorical_feature_names = encoder.get_feature_names(categorical_columns).tolist()';
        $innerLines[] = 'else:';
        $innerLines[] = '    categorical_feature_names = [f"{col}_{val}" for col in categorical_columns for val in categories]';
        $innerLines[] = 'combined_feature_names = categorical_feature_names + number_features.columns.tolist()';
        $innerLines[] = '';

        if ($this->model->getSvmConfiguration()->getKernel() === 'linear') {
            $innerLines[] = 'feature_importance = svm_model.coef_[0]';
            $innerLines[] = 'feature_importance_dict = dict(zip(combined_feature_names, feature_importance))';
        } else {
            $innerLines[] = 'pfi_result = permutation_importance(svm_model, features_val, target_val, n_repeats=30, random_state=42)';
            $innerLines[] = 'sorted_idx = pfi_result.importances_mean.argsort()';
            $innerLines[] = 'feature_importance_dict = {}';
            $innerLines[] = 'for idx in sorted_idx:';
            $innerLines[] = '    feature_importance_dict[combined_feature_names[idx]] = pfi_result.importances_mean[idx]';
        }

        $innerLines[] = 'end_time = time.time()';
        $innerLines[] = '# saving model';
        $innerLines[] = sprintf('dump(svm_model, "%s")', $modelPath);
        $innerLines[] = '';
        $innerLines[] = '# logging the result';
        $innerLines[] = 'results = {';
        $innerLines[] = '    "isText": False,';
        $innerLines[] = '    "mse_val": mse,';
        $innerLines[] = '    "mse_test": 0,';
        $innerLines[] = '    "accuracy_val": 0,';
        $innerLines[] = '    "accuracy_test": 0,';
        $innerLines[] = '    "scatterplot_val": scatterplot,';
        $innerLines[] = '    "residuals_val": residuals_plot,';
        $innerLines[] = '    "scatterplot_test": "",';
        $innerLines[] = '    "residuals_test": "",';
        $innerLines[] = '    "feature_importance": feature_importance_dict,';
        $innerLines[] = '    "duration": end_time - start_time';
        $innerLines[] = '}';

        if ((int) $hyperparameter['testPercentage'] > 0) {
            $innerLines[] = '';
            $innerLines[] = '# runs prediction against testdata';
            $innerLines[] = 'test_pred = svm_model.predict(features_test)';
            $innerLines[] = 'mse_test = mean_squared_error(target_test, test_pred)';
            $innerLines[] = '';
            $innerLines[] = '# Scatterplot';
            $innerLines[] = 'plt.scatter(target_test, test_pred)';
            $innerLines[] = 'plt.xlabel("Tatsächliche Werte")';
            $innerLines[] = 'plt.ylabel("Vorhersagen")';
            $innerLines[] = 'plt.title("Vorhersage vs. Tatsächliche Werte")';
            $innerLines[] = 'scatterplot_test = plot_to_base64()';
            $innerLines[] = 'plt.close()';

            $innerLines[] = '';
            $innerLines[] = '# Residual Plot';
            $innerLines[] = 'residuals_test = target_test - test_pred';
            $innerLines[] = 'plt.scatter(test_pred, residuals_test)';
            $innerLines[] = 'plt.xlabel("Vorhersagen")';
            $innerLines[] = 'plt.ylabel("Residuen")';
            $innerLines[] = 'plt.title("Residual Plot")';
            $innerLines[] = 'plt.axhline(y=0, color="r", linestyle="--")';
            $innerLines[] = 'residuals_test = plot_to_base64()';
            $innerLines[] = 'plt.close()';
            $innerLines[] = '';
            $innerLines[] = 'results["mse_test"] = mse_test';
            $innerLines[] = 'results["scatterplot_test"] = scatterplot_test';
            $innerLines[] = 'results["residuals_test"] = residuals_test';
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


    private function getSvcScript(TrainingPathGenerator $pathGenerator): string
    {
        $targetName = htmlentities($this->getTargetName());

        $textFeatures = array_map('htmlentities', $this->getTextFeatures());
        $numericalFeatures = array_map('htmlentities', $this->getNumericalFeatures());
        if ($targetName === '') {
            throw new \Exception('invalid field configuration');
        }
        $modelPath = $this->model->getModelPath();
        $reportFile = $pathGenerator->getReportFile();

        $hyperparameter = $this->model->getHyperparameters();

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

        $lines[] = "from sklearn.svm import SVC";
        $lines[] = "from sklearn.metrics import accuracy_score";

        $lines[] = "from joblib import dump";
        $lines[] = "from sklearn.preprocessing import StandardScaler, OneHotEncoder, LabelEncoder";
        $lines[] = '';
        $lines[] = '# Logging-Konfiguration';
        $lines[] = sprintf(
            'logging.basicConfig(filename="%s", level=logging.ERROR)',
            $pathGenerator->getErrorFile()
        );

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
        $innerLines[] = 'label_encoder = LabelEncoder()';
        $innerLines[] = 'target_encoded = label_encoder.fit_transform(target)';
        $innerLines[] = sprintf("dump(label_encoder, '%s')", $this->model->getLabelEncoderPath());
        $innerLines[] = '';

        $innerLines[] = 'scaler = StandardScaler()';
        $innerLines[] = 'number_features_scaled = scaler.fit_transform(number_features)';
        $innerLines[] = sprintf(
            "dump(scaler, '%s')",
            $this->model->getScalerPath()
        );
        $innerLines[] = 'features = number_features_scaled';
        $innerLines[] = '';
        if ((int) $hyperparameter['testPercentage'] > 0) {
            $innerLines[] = sprintf(
                'features_train, features_temp, target_train, target_temp = train_test_split(features, target_encoded, test_size=%s, random_state=42)',
                1 - ($hyperparameter['trainingPercentage'] / 100)
            );
            $testPercentage = $hyperparameter['testPercentage'] / (100 - $hyperparameter['trainingPercentage']);
            $innerLines[] = sprintf(
                'features_val, features_test, target_val, target_test = train_test_split(features_temp, target_temp, test_size=%s, random_state=42)',
                $testPercentage
            );
        } else {
            $innerLines[] = sprintf(
                'features_train, features_val, target_train, target_val = train_test_split(features, target_encoded, test_size=%s, random_state=42)',
                1 - ($hyperparameter['trainingPercentage'] / 100)
            );
        }

        $innerLines[] = '';
        $innerLines[] = '# initiates svm model';
        $innerLines[] = sprintf(
            'svm_model = SVC(kernel="%s", C=%s, degree=%s, shrinking=%s, tol=%s)',
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
        $innerLines[] = '# calculates prediction accuracy';
        $innerLines[] = 'accuracy_val = accuracy_score(target_val, target_pred)';


        $innerLines[] = 'end_time = time.time()';
        $innerLines[] = '# saving model';
        $innerLines[] = sprintf('dump(svm_model, "%s")', $modelPath);
        $innerLines[] = '';
        $innerLines[] = '# logging the result';
        $innerLines[] = 'results = {';
        $innerLines[] = '    "isText": True,';
        $innerLines[] = '    "mse_val": 0,';
        $innerLines[] = '    "mse_test": 0,';
        $innerLines[] = '    "accuracy_val": accuracy_val,';
        $innerLines[] = '    "accuracy_test": 0,';
        $innerLines[] = '    "scatterplot_val": "",';
        $innerLines[] = '    "residuals_val": "",';
        $innerLines[] = '    "scatterplot_test": "",';
        $innerLines[] = '    "residuals_test": "",';
        $innerLines[] = '    "feature_importance": {},';
        $innerLines[] = '    "duration": end_time - start_time';
        $innerLines[] = '}';

        if ((int) $hyperparameter['testPercentage'] > 0) {
            $innerLines[] = '';
            $innerLines[] = '# runs prediction against testdata';
            $innerLines[] = 'test_pred = svm_model.predict(features_test)';
            $innerLines[] = 'accuracy_test = accuracy_score(target_test, test_pred)';
            $innerLines[] = '';
            $innerLines[] = 'results["accuracy_test"] = accuracy_test';
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
        $lines[] = "import numpy as np";
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
        $innerLines[] = 'data = pd.read_csv("__DATA_FILE__", delimiter=";", header=0)';
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

    public function generateApplicationScript(string $sourceFile, string $targetFile): string
    {
        if ($this->targetIsText()) {
            return $this->getSvcApplication($sourceFile, $targetFile);
        } else {
            return $this->getSvrApplication($sourceFile, $targetFile);
        }
    }

    private function getSvcApplication(string $sourceFile, string $targetFile): string {
        $numericalFeatures = array_map('htmlentities', $this->getNumericalFeatures());

        $lines = [];
        $lines[] = '# deaktiviert Info-Meldungen von Tensorflow';
        $lines[] = 'import os';
        $lines[] = 'os.environ["TF_CPP_MIN_LOG_LEVEL"] = "3"';
        $lines[] = '';
        $lines[] = "from joblib import load";
        $lines[] = "from sklearn.preprocessing import StandardScaler, LabelEncoder";
        $lines[] = "import numpy as np";
        $lines[] = "import pandas as pd";
        $lines[] = '';
        $lines[] = '# loading model';
        $lines[] = sprintf('model = load("%s")', $this->model->getModelPath());
        $lines[] = '# loading scaler';
        $lines[] = sprintf('scaler = load("%s")', $this->model->getScalerPath());
        $lines[] = '# loading label encoder';
        $lines[] = sprintf('label_encoder = load("%s")', $this->model->getEncoderPath());
        $lines[] = '';
        $lines[] = '# loading source';
        $lines[] = sprintf('data = pd.read_csv("%s", delimiter=";", header=0)', $sourceFile);
        $lines[] = '';
        $lines[] = sprintf(
            'number_features = data[[%s]]',
            implode(', ', array_map(function (string $name) { return '"' . $name . '"'; }, $numericalFeatures))
        );
        $lines[] = '';
        $lines[] = 'number_features_scaled = scaler.transform(number_features)';
        $lines[] = 'features = number_features_scaled';
        $lines[] = '';
        $lines[] = '# executing predictions';
        $lines[] = 'predictions = model.predict(features)';
        $lines[] = '# converting predictions back to labels';
        $lines[] = 'predictions_labels = label_encoder.inverse_transform(predictions)';
        $lines[] = '';
        $lines[] = '# saving predictions';
        $lines[] = 'result_df = pd.DataFrame(predictions_labels, columns=["Prediction"])';
        $lines[] = sprintf('result_df.to_csv("%s", index=False)', $targetFile);
        $result = implode(PHP_EOL, $lines);

        return $result;
    }

    private function getSvrApplication(string $sourceFile, string $targetFile): string {
        $textFeatures = array_map('htmlentities', $this->getTextFeatures());
        $numericalFeatures = array_map('htmlentities', $this->getNumericalFeatures());

        $lines = [];
        $lines[] = '# deaktiviert Info-Meldungen von Tensorflow';
        $lines[] = 'import os';
        $lines[] = 'os.environ["TF_CPP_MIN_LOG_LEVEL"] = "3"';
        $lines[] = '';
        $lines[] = "from joblib import load";
        $lines[] = "from sklearn.preprocessing import OneHotEncoder, StandardScaler";
        $lines[] = "import numpy as np";
        $lines[] = "import pandas as pd";

        $lines[] = '';
        $lines[] = '# loading model';
        $lines[] = sprintf('model = load("%s")', $this->model->getModelPath());
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
        $lines[] = "from sklearn.preprocessing import OneHotEncoder, StandardScaler";
        $lines[] = "import numpy as np";
        $lines[] = "import pandas as pd";

        $lines[] = '';
        $lines[] = '# loading model';
        $lines[] = 'model = load("__MODEL_FILE__")';
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