<?php

namespace App\CodeGenerator;

use App\Service\TrainingPathGenerator;

class DecisionTree extends AbstractCodegenerator
{


    /**
     * @param TrainingPathGenerator $pathGenerator
     * @return string
     * @throws \Exception
     *
     * python script terminates with errorcode 500 in case of error
     */
    public function generateTrainingScript(TrainingPathGenerator $pathGenerator): string
    {
        $targetName = htmlentities($this->getTargetName());
        $features = array_map('htmlentities', $this->getFeatures());

        $textFeatures = array_map('htmlentities', $this->getTextFeatures());
        $numericalFeatures = array_map('htmlentities', $this->getNumericalFeatures());
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
        $lines[] = "import numpy as np";
        $lines[] = "from sklearn.metrics import confusion_matrix";
        $lines[] = "from sklearn.tree import plot_tree";
        $lines[] = "from sklearn.tree import DecisionTreeClassifier";
        $lines[] = "from sklearn.metrics import accuracy_score, classification_report";
        $lines[] = "from sklearn.model_selection import train_test_split";
        $lines[] = "from sklearn.preprocessing import OneHotEncoder";
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
            'data = pd.read_csv("%s", delimiter=";", header=0, error_bad_lines=False)',
            $this->getDataPath($pathGenerator),
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
        $innerLines[] = 'features = np.concatenate([text_features_encoded, number_features], axis=1)';
        $innerLines[] = '';
        $innerLines[] = sprintf(
            'dump(encoder, "%s")',
            $this->model->getEncoderPath()
        );
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
        $innerLines[] = 'end_time = time.time()';

        $innerLines[] = '';
        $innerLines[] = '# results and metrics for validation';
        $innerLines[] = 'target_pred_val = dtree.predict(features_val)';
        $innerLines[] = 'accuracy_val = accuracy_score(target_val, target_pred_val)';
        $innerLines[] = 'confusion_matrix_val = confusion_matrix(target_val, target_pred_val)';
        $innerLines[] = 'classification_report_val = classification_report(target_val, target_pred_val, output_dict=True)';

        $innerLines[] = '';
        $innerLines[] = '# Visualisierung';
        $innerLines[] = 'number_feature_names = number_features.columns.tolist()';
        $innerLines[] = 'text_feature_names = encoder.get_feature_names(text_features.columns).tolist()';
        $innerLines[] = 'feature_names = text_feature_names + number_feature_names';

        $innerLines[] = '';
        $innerLines[] = 'plt.figure(figsize=(20,10))';
        $innerLines[] = 'plot_tree(dtree, filled=True, feature_names=feature_names, class_names=target.unique().astype(str), proportion=True)';
        $innerLines[] = 'tree_plot = plot_to_base64(plt)';
        $innerLines[] = 'plt.close()';

        $innerLines[] = '';
        $innerLines[] = '# logging the result';
        $innerLines[] = 'results = {';
        $innerLines[] = '    "accuracy_val": accuracy_val,';
        $innerLines[] = '    "accuracy_test": 0,';
        $innerLines[] = '    "confusion_matrix_val": confusion_matrix_val.tolist(),';
        $innerLines[] = '    "confusion_matrix_test": np.zeros((2, 2)).tolist(),';
        $innerLines[] = '    "classification_report_val": classification_report_val,';
        $innerLines[] = '    "classification_report_test": {},';
        $innerLines[] = '    "tree_plot": tree_plot,';
        $innerLines[] = '    "duration": end_time - start_time';
        $innerLines[] = '}';

        if ((int) $hyperparameter['testPercentage'] > 0) {
            $innerLines[] = '';
            $innerLines[] = '# results and metrics for tests';
            $innerLines[] = 'target_pred_test = dtree.predict(features_test)';
            $innerLines[] = 'accuracy_test = accuracy_score(target_test, target_pred_test)';
            $innerLines[] = 'confusion_matrix_test = confusion_matrix(target_test, target_pred_test)';
            $innerLines[] = 'classification_report_test = classification_report(target_test, target_pred_test, output_dict=True)';
            $innerLines[] = '';
            $innerLines[] = 'results["accuracy_test"] = accuracy_test';
            $innerLines[] = 'results["confusion_matrix_test"] = confusion_matrix_test.tolist()';
            $innerLines[] = 'results["classification_report_test"] = classification_report_test';
        }

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
        $lines[] = "import numpy as np";
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
        $innerLines[] = 'data = pd.read_csv("__CSV_LOG_FILE__", delimiter=";", header=0, error_bad_lines=False)';
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
        $lines[] = "from sklearn.preprocessing import OneHotEncoder";
        $lines[] = "import numpy as np";
        $lines[] = "import pandas as pd";

        $lines[] = '';
        $lines[] = '# loading model';
        $lines[] = sprintf('model = load("%s")', $this->model->getModelPath());

        $lines[] = '';
        $lines[] = '# loading source';
        $lines[] = sprintf('data = pd.read_csv("%s", delimiter=";", header=0, error_bad_lines=False)', $sourceFile);
        $lines[] = '# loading encoder';
        $lines[] = sprintf('encoder = load("%s")', $this->model->getEncoderPath());

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
        $lines[] = 'text_features_encoded = encoder.fit_transform(text_features)';
        $lines[] = 'features = np.concatenate([text_features_encoded, number_features], axis=1)';

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
        $lines[] = "from sklearn.preprocessing import OneHotEncoder";
        $lines[] = "import numpy as np";
        $lines[] = "import pandas as pd";

        $lines[] = '';
        $lines[] = '# loading model';
        $lines[] = 'model = load("__MODEL_FILE__")';
        $lines[] = '# loading encoder';
        $lines[] = 'encoder = load("__ENCODER_FILE__")';

        $lines[] = '';
        $lines[] = '# loading source';
        $lines[] = 'data = pd.read_csv("__SOURCE_CSV_FILE__", delimiter=";", header=0, error_bad_lines=False)';

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
        $lines[] = 'features = np.concatenate([text_features_encoded, number_features], axis=1)';

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