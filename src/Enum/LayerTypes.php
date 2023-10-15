<?php

namespace App\Enum;

enum LayerTypes: string
{
    case LAYER_TYPE_DENSE = "LAYER_TYPE_DENSE";
    case LAYER_TYPE_DROPOUT = "LAYER_TYPE_DROPOUT";
    case LAYER_TYPE_GRU = "LAYER_TYPE_GRU";
    case LAYER_TYPE_LSTM = "LAYER_TYPE_LSTM";

}
