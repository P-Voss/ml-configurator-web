<template>
    <form @submit.prevent="handleSubmit">
        <div class="mb-3">
            <label for="neurons" class="form-label">{{$t("label.denseNeurons")}}</label>
            <div class="input-group">
                <input type="number" class="form-control" id="neurons" v-model="layer.neurons" />
                <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('neurons')">?</button>
            </div>
            <div v-if="showingInfo === 'neurons'" class="form-text">
                {{$t("helptext.denseneuron")}}
            </div>
        </div>

        <div class="mb-3">
            <label for="activation" class="form-label">{{$t("label.denseActivation")}}</label>
            <div class="input-group">
                <select class="form-select" id="activation" v-model="layer.activationFunction">
                    <option value="relu">ReLu</option>
                    <option value="sigmoid">Sigmoid</option>
                    <option value="tanh">Tanh</option>
                </select>
                <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('activation')">?</button>
            </div>
            <div v-if="showingInfo === 'activation'" class="form-text">
                {{$t("helptext.activationFunction")}}
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
                    <option value="none">None</option>
                    <option value="l1">L1</option>
                    <option value="l2">L2</option>
                </select>
                <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('regularization')">?</button>
            </div>
            <div v-if="showingInfo === 'regularization'" class="form-text">
                {{$t("helptext.regularizetionTypeNet")}}
                <p>
                    <span class="fw-bold">L1: </span>{{$t("helptext.regularizetionTypeNetL1")}}
                </p>
                <p>
                    <span class="fw-bold">L2: </span>{{$t("helptext.regularizetionTypeNetL2")}}
                </p>
            </div>
        </div>

        <div class="mb-3" v-if="layer.regularizationType !== 'none'">
            <label for="lambda" class="form-label">Lambda</label>
            <input type="number" class="form-control mt-2" id="lambda" v-model="layer.lambda" placeholder="Lambda-Wert" max="1" min="0" step="0.001" />

            <div v-if="showingInfo === 'regularization'" class="form-text">
                <p>
                    <span class="fw-bold">Lambda: </span>{{$t("helptext.regularizetionTypeNetLambda")}}
                </p>
            </div>
        </div>

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

        <button type="submit" class="btn btn-primary">{{$t("buttons.addDense")}}</button>
    </form>
</template>

<script>

export default {
    name: 'DenseLayerForm',
    props: {

    },
    data() {
        return {
            showingInfo: '',
            layer: {
                type: 'LAYER_TYPE_DENSE',
                neurons: 0,
                activationFunction: "relu",
                regularizationType: "none",
                lambda: 0,
                dropoutRate: 0
            }
        };
    },
    methods: {
        handleSubmit() {
            let params = {
                type: this.layer.type,
                neurons: this.layer.neurons,
                returnSequences: false,
                activationFunction: this.layer.activationFunction,
                regularizationType: this.layer.regularizationType,
                lambda: this.layer.lambda,
                dropoutRate: this.layer.dropoutRate,
                recurrentDropoutRate: 0,
            }

            this.$emit("add-layer", params);
        },
        toggleInfo(param) {
            if (this.showingInfo === param) {
                this.showingInfo = '';  // Toggle-Verhalten
            } else {
                this.showingInfo = param;
            }
        },
        resetForm() {
            this.layer = {
                neurons: 0,
                activationFunction: "relu",
                initialization: "glorot",
                regularizationType: "none",
                lambda: 0,
                dropoutRate: 0
            };
        }
    }
}

</script>
