<template>
    <div>
        <h3>Hyperparameter für Decision Trees</h3>

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
                    <label for="splitter" class="form-label">Splitter:</label>
                    <div class="input-group">
                        <select class="form-control" id="splitter" v-model="params.splitter" required>
                            <option value="best">Best</option>
                            <option value="random">Random</option>
                        </select>
                        <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('splitter')">?</button>
                    </div>
                    <div v-if="showingInfo === 'splitter'" class="form-text">
                        Erklärung für Splitter...
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
    name: "DecisionTreeParameters",
    props: {
        parameters: Object,
    },
    data() {
        return {
            params: {
                trainingPercentage: 60,
                validationPercentage: 40,
                splitter: 'best',
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
        if (this.parameters.splitter) {
            this.params.splitter = this.parameters.splitter
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
