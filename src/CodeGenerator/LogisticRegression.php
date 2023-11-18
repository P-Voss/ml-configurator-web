<?php

namespace App\CodeGenerator;

use App\Service\TrainingPathGenerator;

class LogisticRegression extends AbstractCodegenerator
{


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
        $conf = $this->model->getLogRegConfiguration();

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
        $lines[] = "from sklearn.linear_model import LogisticRegression";
        $lines[] = "from sklearn.metrics import confusion_matrix, classification_report";
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
        $innerLines[] = sprintf(
            "dump(scaler, '%s')",
            $this->model->getScalerPath()
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
        $innerLines[] = sprintf(
            'model = LogisticRegression(penalty="%s", C=1, solver="%s", max_iter=%s, tol=%s)',
            $conf->getRegularizerType(),
            $conf->getSolver(),
            (int) $hyperparameter['maxIterations'],
            (int) $hyperparameter['tolerance']
        );
        $innerLines[] = '';
        $innerLines[] = '# execute training';
        $innerLines[] = 'model.fit(features_train, target_train)';

        $innerLines[] = '';
        $innerLines[] = 'end_time = time.time()';

        $innerLines[] = '';
        $innerLines[] = '# gather validation data';
        $innerLines[] = 'target_pred_val = model.predict(features_val)';
        $innerLines[] = 'cm_val = confusion_matrix(target_val, target_pred)';
        $innerLines[] = 'cr_val = classification_report(target_val, target_pred)';

        $innerLines[] = '# saving model';
        $innerLines[] = sprintf('dump(model, "%s")', $modelPath);
        $innerLines[] = '';
        $innerLines[] = '# logging';
        $innerLines[] = 'results = {';
        $innerLines[] = '    "cm_val": cm_val,';
        $innerLines[] = '    "cr_val": cr_val,';
        $innerLines[] = '    "cm_test": "",';
        $innerLines[] = '    "cr_test": "",';
        $innerLines[] = '    "duration": end_time - start_time';
        $innerLines[] = '}';


        if ((int) $hyperparameter['testPercentage'] > 0) {
            $innerLines[] = '';
            $innerLines[] = '# gather performance data';
            $innerLines[] = 'target_pred_test = model.predict(features_test)';
            $innerLines[] = 'cm_test = confusion_matrix(target_test, target_pred_test)';
            $innerLines[] = 'cr_test = classification_report(target_test, target_pred_test)';

            $innerLines[] = 'results["cm_test"] = cm_test';
            $innerLines[] = 'results["cr_test"] = cr_test';
        }

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
        $targetName = htmlentities($this->getTargetName());
        $features = array_map('htmlentities', $this->getFeatures());
        if ($targetName === '') {
            throw new \Exception('invalid field configuration');
        }

        $hyperparameter = $this->model->getHyperparameters();
        $conf = $this->model->getLogRegConfiguration();
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
        $lines[] = "from sklearn.metrics import roc_auc_score, log_loss";
        $lines[] = "from sklearn.model_selection import train_test_split";
        $lines[] = "from sklearn.linear_model import LogisticRegression";
        $lines[] = "from sklearn.metrics import confusion_matrix, classification_report";
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
        $innerLines[] = 'data = pd.read_csv("__CSV_FILE__", delimiter=";", header=0)';
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
        $innerLines[] = sprintf(
            'model = LogisticRegression(penalty="%s", C=1, solver="%s", max_iter=%s, tol=%s)',
            $conf->getRegularizerType(),
            $conf->getSolver(),
            (int) $hyperparameter['maxIterations'],
            (int) $hyperparameter['tolerance']
        );
        $innerLines[] = '';
        $innerLines[] = '# execute training';
        $innerLines[] = 'model.fit(features_train, target_train)';

        $innerLines[] = '';
        $innerLines[] = '# gather validation data';
        $innerLines[] = 'target_pred_val = model.predict(features_val)';
        $innerLines[] = 'confusion_matrix_val = confusion_matrix(target_val, target_pred)';
        $innerLines[] = 'classification_report_val = classification_report(target_val, target_pred)';
        $innerLines[] = 'accuracy_val = accuracy_score(target_val, target_pred_val)';
        $innerLines[] = 'roc_auc_val = roc_auc_score(target_val, model.predict_proba(features_val)[:, 1])';
        $innerLines[] = 'log_loss_val = log_loss(target_val, model.predict_proba(features_val))';

        $innerLines[] = '';
        $innerLines[] = 'end_time = time.time()';

        $innerLines[] = '# saving model';
        $innerLines[] = 'dump(model, "__MODEL_FILE__")';
        $innerLines[] = '';
        $innerLines[] = '# logging';
        $innerLines[] = 'results = {';
        $innerLines[] = '    "accuracy_val": accuracy_val,';
        $innerLines[] = '    "roc_auc_val": roc_auc_val,';
        $innerLines[] = '    "log_loss_val": log_loss_val,';
        $innerLines[] = '    "accuracy_test": 0,';
        $innerLines[] = '    "roc_auc_test": 0,';
        $innerLines[] = '    "log_loss_test": 0,';
        $innerLines[] = '    "confusion_matrix_val": confusion_matrix_val.tolist(),';
        $innerLines[] = '    "classification_report_val": classification_report_val,';
        $innerLines[] = '    "confusion_matrix_test": np.zeros((2, 2)).tolist(),';
        $innerLines[] = '    "classification_report_test": {},';
        $innerLines[] = '    "duration": end_time - start_time';
        $innerLines[] = '}';

        if ((int) $hyperparameter['testPercentage'] > 0) {
            $innerLines[] = '';
            $innerLines[] = '# gather test data';
            $innerLines[] = 'target_pred_test = model.predict(features_test)';
            $innerLines[] = 'confusion_matrix_test = confusion_matrix(target_test, target_pred_test)';
            $innerLines[] = 'classification_report_test = classification_report(target_test, target_pred_test)';
            $innerLines[] = 'accuracy_test = accuracy_score(target_test, target_pred_test)';
            $innerLines[] = 'roc_auc_test = roc_auc_score(target_test, model.predict_proba(features_test)[:, 1])';
            $innerLines[] = 'log_loss_test = log_loss(target_test, model.predict_proba(features_test))';

            $innerLines[] = '';
            $innerLines[] = 'results["confusion_matrix_test"] = confusion_matrix_test.tolist()';
            $innerLines[] = 'results["classification_report_test"] = classification_report_test';
            $innerLines[] = 'results["accuracy_test"] = accuracy_test';
            $innerLines[] = 'results["roc_auc_test"] = roc_auc_test';
            $innerLines[] = 'results["log_loss_test"] = log_loss_test';
        }

        $innerLines[] = 'with open("__LOG_FILE__", "w") as outfile:';
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