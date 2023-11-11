<template>
    <form @submit.prevent="handleSubmit" class="gx-4">
        <div class="row mb-3">
            <div class="col-12 col-lg-6">
                <label for="maxDepth" class="form-label">{{$t("label.dtreeMaxDepth")}}</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="maxDepth" v-model="params.maxDepth" />
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('maxDepth')">?</button>
                </div>
                <div v-if="showingInfo === 'maxDepth'" class="form-text">
                    {{$t("helptext.dtreeMaxDepth")}}

                </div>
            </div>
            <div class="col-12 col-lg-6">
                <label for="maxFeatures" class="form-label">{{$t("label.dtreeMaxFeatures")}}</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="maxFeatures" v-model="params.maxFeatures" />
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('maxFeatures')">?</button>
                </div>
                <div v-if="showingInfo === 'maxFeatures'" class="form-text">
                    {{$t("helptext.dtreeMaxFeatures")}}
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12 col-lg-6">
                <label for="minSampleSplit" class="form-label">{{$t("label.dtreeMinSampleSplit")}}</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="minSampleSplit" v-model="params.minSampleSplit" />
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('minSampleSplit')">?</button>
                </div>
                <div v-if="showingInfo === 'minSampleSplit'" class="form-text">
                    {{$t("helptext.dtreeMinSampleSplit")}}
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <label for="minSamplesLeaf" class="form-label">{{$t("label.dtreeMinSamplesLeaf")}}</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="minSamplesLeaf" v-model="params.minSamplesLeaf" />
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('minSamplesLeaf')">?</button>
                </div>
                <div v-if="showingInfo === 'minSamplesLeaf'" class="form-text">
                    {{$t("helptext.dtreeMinSamplesLeaf")}}
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12 col-lg-6">
                <label for="missingValueHandling" class="form-label">{{$t("label.dtreeMissingValues")}}</label>
                <div class="input-group">
                    <select class="form-select" id="missingValueHandling" v-model="params.missingValueHandling">
                        <option value="median">{{$t("option.dtreeMedian")}}</option>
                        <option value="drop">{{$t("option.dtreeDrop")}}</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('missingValueHandling')">?</button>
                </div>
                <div v-if="showingInfo === 'missingValueHandling'" class="form-text">
                    {{$t("helptext.dtreeMissingValues")}}
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <label for="qualityMeasure" class="form-label">{{$t("label.dtreeQuality")}}</label>
                <div class="input-group">
                    <select class="form-select" id="qualityMeasure" v-model="params.qualityMeasure">
                        <option value="gini">{{$t("option.dtreeGini")}}</option>
                        <option value="entropy">{{$t("option.dtreeEntropy")}}</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('qualityMeasure')">?</button>
                </div>
                <div v-if="showingInfo === 'qualityMeasure'" class="form-text">
                    {{$t("helptext.dtreeQuality")}}

                    <p>
                        <span class="fw-bold">{{$t("option.dtreeGini")}}: </span> {{$t("helptext.dtreeGini")}}
                    </p>
                    <p>
                        <span class="fw-bold">{{$t("option.dtreeEntropy")}}: </span> {{$t("helptext.dtreeEntropy")}}
                    </p>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <button type="submit" class="btn btn-primary">{{$t("button.save")}}</button>
            </div>
        </div>
    </form>
</template>

<script>

export default {
    name: "DtreeArchitecture",
    props: {
        architecture: Object,
    },
    data() {
        return {
            params: {
                maxDepth: 1,
                minSampleSplit: 2,
                maxFeatures: 1,
                minSamplesLeaf: 1,
                missingValueHandling: 'median',
                qualityMeasure: 'gini'
            },
            showingInfo: null
        };
    },
    mounted() {
        if (this.architecture.maxDepth) {
            this.params.maxDepth = this.architecture.maxDepth
        }
        if (this.architecture.minSampleSplit) {
            this.params.minSampleSplit = this.architecture.minSampleSplit
        }
        if (this.architecture.maxFeatures) {
            this.params.maxFeatures = this.architecture.maxFeatures
        }
        if (this.architecture.minSamplesLeaf) {
            this.params.minSamplesLeaf = this.architecture.minSamplesLeaf
        }
        if (this.architecture.missingValueHandling) {
            this.params.missingValueHandling = this.architecture.missingValueHandling
        }
        if (this.architecture.qualityMeasure) {
            this.params.qualityMeasure = this.architecture.qualityMeasure
        }
    },
    methods: {
        handleSubmit() {
            this.$emit("save-configuration", this.params);
        },
        toggleInfo(parameter) {
            this.showingInfo = this.showingInfo === parameter ? null : parameter;
        }
    }
}

</script>