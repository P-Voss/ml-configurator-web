<template>
    <div>
        <h3>Hyperparameter für Lineare Regression</h3>

        <form @submit.prevent="handleSubmit">

            <div class="row">
                <div class="col-12 col-lg-6">
                    <label class="h4 mb-3">Aufteilung der Trainingsdaten</label>
                    <div class="row">
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
                    <label for="learningRate" class="form-label mt-3">Lernrate:</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="learningRate" v-model="params.learningRate" required min="0" step="0.01">
                        <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('learningRate')">?</button>
                    </div>
                    <div v-if="showingInfo === 'learningRate'" class="form-text">
                        Erklärung für Lernrate...
                    </div>

                    <label for="maxIterations" class="form-label mt-3">Maximale Iterationen:</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="maxIterations" v-model="params.maxIterations" required min="1" step="1">
                        <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('maxIterations')">?</button>
                    </div>
                    <div v-if="showingInfo === 'maxIterations'" class="form-text">
                        Erklärung für maximale Iterationen...
                    </div>

                    <label for="tolerance" class="form-label mt-3">Toleranz:</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="tolerance" v-model="params.tolerance" required min="0" step="0.001">
                        <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('tolerance')">?</button>
                    </div>
                    <div v-if="showingInfo === 'tolerance'" class="form-text">
                        Erklärung für Toleranz...
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
    name: "LinRegParameters",
    props: {
        parameters: Object,
    },
    data() {
        return {
            params: {
                trainingPercentage: 60,
                validationPercentage: 40,
                learningRate: 0.01,
                maxIterations: 1000,
                tolerance: 0.001,
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
        if (this.parameters.learningRate) {
            this.params.learningRate = this.parameters.learningRate
        }
        if (this.parameters.maxIterations) {
            this.params.maxIterations = this.parameters.maxIterations
        }
        if (this.parameters.tolerance) {
            this.params.tolerance = this.parameters.tolerance
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
