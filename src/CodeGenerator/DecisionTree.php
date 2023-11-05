<?php

namespace App\CodeGenerator;

use App\Service\TrainingPathGenerator;

class DecisionTree extends AbstractCodegenerator
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
        $conf = $this->model->getDecisiontreeConfiguration();
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
        $lines[] = "from sklearn.metrics import confusion_matrix";
        $lines[] = "from sklearn.tree import plot_tree";
        $lines[] = "from sklearn.tree import DecisionTreeClassifier";
        $lines[] = "from sklearn.metrics import accuracy_score, classification_report";
        $lines[] = "from sklearn.model_selection import train_test_split";
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
                'features_val, _, target_val, _ = train_test_split(features_temp, target_temp, test_size=%s)',
                $split
            );
        } else {
            $innerLines[] = 'features_val, target_val = features_temp, target_temp';
        }

        $innerLines[] = '';
        $innerLines[] = '# initiates and trains Decision Tree';
        $innerLines[] = sprintf(
            'dtree = DecisionTreeClassifier(max_depth=%s, max_features=%s, min_samples_leaf=%s, min_samples_split=%s, criterion="%s", splitter="%s")',
            (int) $conf->getMaxDepth(),
            min((int) $conf->getMaxFeatures(), count($features)),
            (int) $conf->getMinSamplesLeaf(),
            (int) $conf->getMinSampleSplit(),
            $conf->getQualityMeasure(),
            $hyperparameter['splitter']
        );
        $innerLines[] = 'dtree.fit(features_train, target_train)';

        $innerLines[] = '';
        $innerLines[] = '# results and metrics';
        $innerLines[] = 'end_time = time.time()';
        $innerLines[] = 'target_pred = dtree.predict(features_val)';
        $innerLines[] = 'accuracy = accuracy_score(target_val, target_pred)';
        $innerLines[] = 'conf_matrix = confusion_matrix(target_val, target_pred)';
        $innerLines[] = 'class_report = classification_report(target_val, target_pred, output_dict=True)';

        if ($split > 0) {
            /**
             * @todo add additional testrun when splitting testset
             * target_pred_test = dtree.predict(features_test)
             * accuracy_test = accuracy_score(target_test, target_pred_test)
             * conf_matrix_test = confusion_matrix(target_test, target_pred_test)
             * class_report_test = classification_report(target_test, target_pred_test)
             */
        }

        $innerLines[] = '';
        $innerLines[] = '# Visualisierung';
        $innerLines[] = 'plt.figure(figsize=(20,10))';
        $innerLines[] = 'plot_tree(dtree, filled=True, feature_names=features.columns, class_names=target.unique().astype(str), proportion=True)';
        $innerLines[] = 'tree_plot = plot_to_base64(plt)';
        $innerLines[] = 'plt.close()';

        $innerLines[] = '';
        $innerLines[] = '# logging the result';
        $innerLines[] = 'results = {';
        $innerLines[] = '    "accuracy": accuracy,';
        $innerLines[] = '    "confusion_matrix": conf_matrix.tolist(),';
        $innerLines[] = '    "classification_report": class_report,';
        $innerLines[] = '    "tree_plot": tree_plot,';
        $innerLines[] = '    "duration": end_time - start_time';
        $innerLines[] = '}';
        $innerLines[] = sprintf('with open("%s", "w") as outfile:', $pathGenerator->getReportFile());
        $innerLines[] = '    json.dump(results, outfile, indent=4)';

        $innerLines[] = '';
        $innerLines[] = '# saving the model';
        $innerLines[] = sprintf('dump(dtree, "%s")', $pathGenerator->getModelFile("joblib"));

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
        $conf = $this->model->getDecisiontreeConfiguration();
        $hyperparameter = $this->model->getHyperparameters();
        $split = $hyperparameter['testPercentage'] / ($hyperparameter['testPercentage'] + $hyperparameter['validationPercentage']);

        $innerLines = [];

        $lines = [];
        $lines[] = '';
        $lines[] = "import time";
        $lines[] = "import logging";
        $lines[] = "import base64";
        $lines[] = "from io import BytesIO";
        $lines[] = "import json";
        $lines[] = "import pandas as pd";
        $lines[] = "import matplotlib.pyplot as plt";
        $lines[] = "from sklearn.metrics import confusion_matrix";
        $lines[] = "from sklearn.tree import plot_tree";
        $lines[] = "from sklearn.tree import DecisionTreeClassifier";
        $lines[] = "from sklearn.metrics import accuracy_score, classification_report";
        $lines[] = "from sklearn.model_selection import train_test_split";
        $lines[] = "from joblib import dump";
        $lines[] = '';
        $lines[] = '# Logging-Konfiguration';
        $lines[] = 'logging.basicConfig(filename="__ERROR_LOG_FILE__", level=logging.ERROR)';

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
            'data = pd.read_csv("__CSV_LOG_FILE__", delimiter=";", header=0 if %s else None)',
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
        $innerLines[] = '# initiates and trains Decision Tree';
        $innerLines[] = sprintf(
            'dtree = DecisionTreeClassifier(max_depth=%s, max_features=%s, min_samples_leaf=%s, min_samples_split=%s, criterion="%s", splitter="%s")',
            (int) $conf->getMaxDepth(),
            min((int) $conf->getMaxFeatures(), count($features)),
            (int) $conf->getMinSamplesLeaf(),
            (int) $conf->getMinSampleSplit(),
            $conf->getQualityMeasure(),
            $hyperparameter['splitter']
        );
        $innerLines[] = 'dtree.fit(features_train, target_train)';

        $innerLines[] = '';
        $innerLines[] = '# results and metrics';
        $innerLines[] = 'end_time = time.time()';
        $innerLines[] = 'target_pred = dtree.predict(features_val)';
        $innerLines[] = 'accuracy = accuracy_score(target_val, target_pred)';
        $innerLines[] = 'conf_matrix = confusion_matrix(target_val, target_pred)';
        $innerLines[] = 'class_report = classification_report(target_val, target_pred, output_dict=True)';

        $innerLines[] = '';
        $innerLines[] = '# Visualisierung';
        $innerLines[] = 'plt.figure(figsize=(20,10))';
        $innerLines[] = 'plot_tree(dtree, filled=True, feature_names=features.columns, class_names=target.unique().astype(str), proportion=True)';
        $innerLines[] = 'tree_plot = plot_to_base64(plt)';
        $innerLines[] = 'plt.close()';

        $innerLines[] = '';
        $innerLines[] = '# logging the result';
        $innerLines[] = 'results = {';
        $innerLines[] = '    "accuracy": accuracy,';
        $innerLines[] = '    "confusion_matrix": conf_matrix.tolist(),';
        $innerLines[] = '    "classification_report": class_report,';
        $innerLines[] = '    "tree_plot": tree_plot,';
        $innerLines[] = '    "duration": end_time - start_time';
        $innerLines[] = '}';
        $innerLines[] = 'with open("__REPORT_JSON_FILE__", "w") as outfile:';
        $innerLines[] = '    json.dump(results, outfile, indent=4)';

        $innerLines[] = '';
        $innerLines[] = '# saving the model';
        $innerLines[] = 'dump(dtree, "__MODEL_FILE__")';

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