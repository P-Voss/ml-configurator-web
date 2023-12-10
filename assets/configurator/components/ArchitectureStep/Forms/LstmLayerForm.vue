<template>
    <form @submit.prevent="handleSubmit">

        <!-- Einheiten/Neuronen -->
        <div class="mb-3">
            <label for="units" class="form-label">{{$t("label.recNeurons")}}:</label>
            <div class="input-group">
                <input type="number" class="form-control" id="units" v-model="layer.neurons" min="8" step="1" />
                <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('units')">?</button>
            </div>
            <div v-if="showingInfo === 'units'" class="form-text">
                <p>
                    {{$t("helptext.lstmNeuron")}}
                </p>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-check-label">
                <input type="checkbox" class="form-check-input" v-model="layer.returnSequences">{{$t("label.recSequence")}}
            </label>
            <button type="button" class="btn btn-outline-secondary ms-2" @click="toggleInfo('returnSequences')">?</button>
            <div v-if="showingInfo === 'returnSequences'" class="form-text">
                {{$t("helptext.lstmSequence")}}
            </div>
        </div>

        <div class="mb-3">
            <label for="activation" class="form-label">{{$t("label.denseActivation")}}</label>
            <div class="input-group">
                <select class="form-select" id="activation" v-model="layer.activationFunction">
                    <option value="tanh">Tanh</option>
                    <option value="sigmoid">Sigmoid</option>
                    <option value="relu">ReLU</option>
                </select>
                <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('activation')">?</button>
            </div>
            <div v-if="showingInfo === 'activation'" class="form-text">
                {{$t("helptext.activationFunctionLstm")}}
                <p>
                    <span class="fw-bold">ReLu: </span>{{$t("helptext.activationFunctionRelu")}}
                </p>
                <p>
                    <span class="fw-bold">Sigmoid: </span>{{$t("helptext.activationFunctionSigmoid")}}
                </p>
                <p>
                    <span class="fw-bold">Tanh: </span>{{$t("helptext.activationFunctionTanh")}}
                </p>
            </div>
        </div>

        <div class="mb-3">
            <label for="regularization" class="form-label">{{$t("label.denseRegularization")}}</label>
            <div class="input-group">
                <select class="form-select" id="regularization" v-model="layer.regularizationType">
                    <option value="none">Keine</option>
                    <option value="l1">L1</option>
                    <option value="l2">L2</option>
                </select>
                <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('regularization')">?</button>
            </div>
            <div v-if="showingInfo === 'regularization'" class="form-text">
                {{$t("helptext.regularizationTypeNet")}}
                <p>
                    <span class="fw-bold">L1: </span>{{$t("helptext.regularizationTypeNetL1")}}
                </p>
                <p>
                    <span class="fw-bold">L2: </span>{{$t("helptext.regularizationTypeNetL2")}}
                </p>
            </div>
        </div>

        <div class="mb-3" v-if="layer.regularizationType !== 'none'">
            <label for="lambda" class="form-label">Lambda</label>
            <input type="number" class="form-control mt-2" id="lambda" v-model="layer.lambda" placeholder="Lambda-Wert" max="1" min="0" step="0.001" />

            <div v-if="showingInfo === 'regularization'" class="form-text">
                <p>
                    <span class="fw-bold">Lambda: </span>{{$t("helptext.regularizationTypeNetLambda")}}
                </p>
            </div>
        </div>

        <!-- Dropout -->
        <div class="mb-3">
            <label for="dropout" class="form-label">{{$t("label.dropout")}}</label>
            <div class="input-group">
                <input type="number" class="form-control" id="dropout" v-model="layer.dropoutRate" placeholder="0.0 - 1.0" min="0" max="1" step="0.01"/>
                <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('dropout')">?</button>
            </div>
            <div v-if="showingInfo === 'dropout'" class="form-text">
                {{$t("helptext.dropout")}}
            </div>
        </div>

        <!-- Rekurrenter Dropout -->
        <div class="mb-3">
            <label for="recurrentDropout" class="form-label">{{$t("label.recDropout")}}</label>
            <div class="input-group">
                <input type="number" class="form-control" id="recurrentDropout" v-model="layer.recurrentDropoutRate" placeholder="0.0 - 1.0" min="0" max="1" step="0.01"/>
                <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('recurrentDropout')">?</button>
            </div>
            <div v-if="showingInfo === 'recurrentDropout'" class="form-text">
                {{$t("helptext.recDropout")}}
            </div>
        </div>

        <!-- Button zum Hinzufügen -->
        <button type="submit" class="btn btn-primary">{{$t("button.addLstm")}}</button>

    </form>
</template>

<script>
export default {
    name: 'LstmLayerForm',
    data() {
        return {
            layer: {
                type: 'LAYER_TYPE_LSTM',
                neurons: 50,
                returnSequences: false,
                activationFunction: 'tanh',
                regularizationType: "none",
                lambda: 0,
                dropoutRate: 0,
                recurrentDropoutRate: 0
            },
            showingInfo: null
        };
    },
    methods: {
        handleSubmit() {
            let params = {
                type: this.layer.type,
                neurons: this.layer.neurons,
                returnSequences: this.layer.returnSequences,
                activationFunction: this.layer.activationFunction,
                regularizationType: this.layer.regularizationType,
                lambda: this.layer.lambda,
                dropoutRate: this.layer.dropoutRate,
                recurrentDropoutRate: this.layer.recurrentDropoutRate,
            }

            this.$emit("add-layer", params);
        },
        toggleInfo(parameter) {
            this.showingInfo = this.showingInfo === parameter ? null : parameter;
        }
    }
}
</script>