<?php

namespace App\Enum;

enum ModelTypes: string
{
    case MODEL_TYPE_DTREE = "MODEL_TYPE_DTREE";
    case MODEL_TYPE_LOG_REGR = "MODEL_TYPE_LOG_REGR";
    case MODEL_TYPE_SVM = "MODEL_TYPE_SVM";
    case MODEL_TYPE_LIN_REGR = "MODEL_TYPE_LIN_REGR";
    case MODEL_TYPE_NEUR = "MODEL_TYPE_NEUR";
    case MODEL_TYPE_RNN = "MODEL_TYPE_RNN";

    public static function getModeltypeName(self $value): string {
        return match ($value) {
            ModelTypes::MODEL_TYPE_DTREE => 'Decision Tree',
            ModelTypes::MODEL_TYPE_LOG_REGR => 'Logistische Regression',
            ModelTypes::MODEL_TYPE_SVM => 'Support Vector Machine (SVM)',
            ModelTypes::MODEL_TYPE_LIN_REGR => 'Lineare Regression',
            ModelTypes::MODEL_TYPE_NEUR => 'Neuronales Netz (Feedforward)',
            ModelTypes::MODEL_TYPE_RNN => 'Recurrent Neural Network (RNN)',
        };
    }
    /**
     * @param ModelTypes $modelType
     * @param LayerTypes $layerType
     * @return bool
     */
    public static function isValidLayertype(ModelTypes $modelType, LayerTypes $layerType): bool {
        if ($modelType === ModelTypes::MODEL_TYPE_DTREE) {
            return false;
        }
        if ($modelType === ModelTypes::MODEL_TYPE_LIN_REGR) {
            return false;
        }
        if ($modelType === ModelTypes::MODEL_TYPE_LOG_REGR) {
            return false;
        }
        if ($modelType === ModelTypes::MODEL_TYPE_SVM) {
            return false;
        }
        if ($modelType === ModelTypes::MODEL_TYPE_NEUR) {
            if ($layerType === LayerTypes::LAYER_TYPE_GRU) {
                return false;
            }
            if ($layerType === LayerTypes::LAYER_TYPE_LSTM) {
                return false;
            }
        }

        return true;
    }
}
