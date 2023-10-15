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

    public static function requiresArchitectureConfiguration(self $value): bool {
        return match ($value) {
            ModelTypes::MODEL_TYPE_DTREE, ModelTypes::MODEL_TYPE_LOG_REGR, ModelTypes::MODEL_TYPE_SVM, ModelTypes::MODEL_TYPE_LIN_REGR => false,
            ModelTypes::MODEL_TYPE_NEUR, ModelTypes::MODEL_TYPE_RNN => true,
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
