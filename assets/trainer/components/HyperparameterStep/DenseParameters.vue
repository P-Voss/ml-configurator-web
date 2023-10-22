<template>
    <div>
        <h3>Hyperparameter für Neuronale Netze</h3>

        <form @submit.prevent="handleSubmit">

            <div class="row">
                <div class="col-12 col-lg-6">
                    <label class="h4 mb-3">Aufteilung der Trainingsdaten</label>
                    <div class="row mb-3">
                        <div class="col-8 col-lg-9">
                            <input type="range" style="width: 100%" min="50" max="100" v-model="params.trainingPercentage" @input="adjustValidationRange">
                        </div>
                        <div class="col-4 col-lg-3 text-end">
                            Training: {{ params.trainingPercentage }}%
                        </div>
                        <hr />
                    </div>
                    <div class="row">
                        <div class="col-8 col-lg-9">
                            <input type="range" style="width: 100%" min="0" :max="100 - params.trainingPercentage" v-model="params.validationPercentage">
                        </div>
                        <div class="col-4 col-lg-3 text-end">
                            Validierung: {{ params.validationPercentage }}%
                        </div>
                        <hr />
                    </div>
                    <div class="row">
                        <div class="col-4 col-lg-3 offset-8 offset-lg-9 text-end">
                            Test: {{ 100 - params.trainingPercentage - params.validationPercentage }}%
                        </div>
                        <hr />
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <label for="optimizer" class="form-label mt-3">Optimierer:</label>
                    <div class="input-group mb-3">
                        <select class="form-control" id="optimizer" v-model="params.optimizer" required>
                            <option value="SGD">SGD</option>
                            <option value="Adam">Adam</option>
                            <option value="RMSprop">RMSprop</option>
                        </select>
                        <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('optimizer')">?</button>
                    </div>
                    <div v-if="showingInfo === 'optimizer'" class="form-text">
                        Erklärung für Optimierer...
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <label for="learningRate" class="form-label mt-3">Lernrate:</label>
                    <div class="input-group mb-3">
                        <input type="number" class="form-control" id="learningRate" v-model="params.learningRate" required min="0" step="0.01">
                        <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('learningRate')">?</button>
                    </div>
                    <div v-if="showingInfo === 'learningRate'" class="form-text">
                        Erklärung für Lernrate...
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <label for="batchSize" class="form-label mt-3">Batchsize:</label>
                    <div class="input-group mb-3">
                        <input type="number" class="form-control" id="batchSize" v-model="params.batchSize" required min="8" step="1">
                        <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('batchSize')">?</button>
                    </div>
                    <div v-if="showingInfo === 'batchSize'" class="form-text">
                        Erklärung für Batchsize...
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <label for="epochs" class="form-label mt-3">Anzahl Epochen:</label>
                    <div class="input-group mb-3">
                        <input type="number" class="form-control" id="epochs" v-model="params.epochs" required min="1" step="1">
                        <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('epochs')">?</button>
                    </div>
                    <div v-if="showingInfo === 'epochs'" class="form-text">
                        Erklärung für Anzahl Epochen...
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <label for="costFunction" class="form-label mt-3">Kostenfunktion:</label>
                    <div class="input-group mb-3">
                        <select class="form-control" id="costFunction" v-model="params.costFunction" required>
                            <option value="MSE">Mean Squared Error</option>
                            <option value="MAE">Mean Absolute Error</option>
                        </select>
                        <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('costFunction')">?</button>
                    </div>
                    <div v-if="showingInfo === 'costFunction'" class="form-text">
                        Erklärung für Kostenfunktion...
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <label for="momentum" class="form-label mt-3">Momentum:</label>
                    <div class="input-group mb-3">
                        <input type="number" class="form-control" id="momentum" v-model="params.momentum" required min="0.1" step="0.1">
                        <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('momentum')">?</button>
                    </div>
                    <div v-if="showingInfo === 'momentum'" class="form-text">
                        Erklärung für Momentum...
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <label for="patience" class="form-label mt-3">Early Stop Grenzwert:</label>
                    <div class="input-group mb-3">
                        <input type="number" class="form-control" id="patience" v-model="params.patience" required min="0" step="1">
                        <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('patience')">?</button>
                    </div>
                    <div v-if="showingInfo === 'patience'" class="form-text">
                        Erklärung für Geduld (Patience)...
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </div>
            </div>

        </form>
    </div>
</template>

<script>
export default {
    name: "DenseParameters",
    props: {
        parameters: Object,
    },
    data() {
        return {
            params: {
                trainingPercentage: 60,
                validationPercentage: 80,
                optimizer: 'SGD',
                learningRate: 0.01,
                batchSize: 32,
                epochs: 10,
                costFunction: 'MSE',
                momentum: 0.9,
                patience: 10,
            },
            showingInfo: null,
        };
    },
    mounted() {
        if (this.parameters.trainingPercentage) {
            this.params.trainingPercentage = this.parameters.trainingPercentage
        }
        if (this.parameters.validationPercentage) {
            this.params.validationPercentage = this.parameters.validationPercentage
        }
        if (this.parameters.optimizer) {
            this.params.optimizer = this.parameters.optimizer
        }
        if (this.parameters.learningRate) {
            this.params.learningRate = this.parameters.learningRate
        }
        if (this.parameters.batchSize) {
            this.params.batchSize = this.parameters.batchSize
        }
        if (this.parameters.epochs) {
            this.params.epochs = this.parameters.epochs
        }
        if (this.parameters.costFunction) {
            this.params.costFunction = this.parameters.costFunction
        }
        if (this.parameters.momentum) {
            this.params.momentum = this.parameters.momentum
        }
        if (this.parameters.patience) {
            this.params.patience = this.parameters.patience
        }
    },
    methods: {
        handleSubmit() {
            let form = new FormData();
            for (let key in this.params ) {
                form.append(key, this.params[key]);
            }
            this.$emit("save-hyperparameters", form);
        },
        adjustValidationRange() {
            if ((this.params.trainingPercentage*1 + this.params.validationPercentage*1) > 100) {
                this.params.validationPercentage = 100 - this.params.trainingPercentage
            }
        },
        toggleInfo(parameter) {
            this.showingInfo = this.showingInfo === parameter ? null : parameter;
        },
    },
};
</script>
