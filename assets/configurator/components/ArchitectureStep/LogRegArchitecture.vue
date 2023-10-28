<template>
    <form @submit.prevent="handleSubmit" class="gx-4">
        <div class="row mb-3">

            <div class="col-12 col-lg-6">
                <label for="regularizerType" class="form-label">{{$t("label.logregRegularization")}}</label>
                <div class="input-group">
                    <select class="form-select" id="regularizerType" v-model="params.regularizerType">
                        <option value="l1">L1</option>
                        <option value="l2">L2</option>
                        <option value="none">None</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('regularizerType')">?</button>
                </div>
                <div v-if="showingInfo === 'regularizerType'" class="form-text">
                    {{$t("helptext.logregRegularization")}}
                    <p>
                        <span class="fw-bold">L1: </span>{{$t("helptext.logregRegularizationL1")}}
                    </p>
                    <p>
                        <span class="fw-bold">L2: </span>{{$t("helptext.logregRegularizationL2")}}
                    </p>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <label for="solver" class="form-label">Solver</label>
                <div class="input-group">
                    <select class="form-select" id="solver" v-model="params.solver">
                        <option value="newton-cg">Newton-CG</option>
                        <option value="lbfgs">L-BFGS</option>
                        <option value="liblinear">Liblinear</option>
                        <option value="sag">SAG</option>
                        <option value="saga">SAGA</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('solver')">?</button>
                </div>
                <div v-if="showingInfo === 'solver'" class="form-text">
                    {{$t("helptext.logregSolver")}}
                    <p v-if="params.solver === 'newton-cg'">
                        <span class="fw-bold">Newton-CG: </span>{{$t("helptext.logregSolverNewton")}}
                    </p>
                    <p v-if="params.solver === 'lbfgs'">
                        <span class="fw-bold">L-BFGS: </span>{{$t("helptext.logregSolverL")}}
                    </p>
                    <p v-if="params.solver === 'liblinear'">
                        <span class="fw-bold">Liblinear: </span>{{$t("helptext.logregSolverLiblinear")}}
                    </p>
                    <p v-if="params.solver === 'sag'">
                        <span class="fw-bold">SAG: </span>{{$t("helptext.logregSolverSAG")}}
                    </p>
                    <p v-if="params.solver === 'saga'">
                        <span class="fw-bold">SAGA: </span>{{$t("helptext.logregSolverSAGA")}}
                    </p>
                </div>
            </div>

        </div>

        <div class="row mb-3">

            <div class="col-12 col-lg-6">
                <label for="lambda" class="form-label">Lambda</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="lambda" v-model="params.lambda" />
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('lambda')">?</button>
                </div>
                <div v-if="showingInfo === 'lambda'" class="form-text">
                    {{$t("helptext.logregLambda")}}
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
    name: "LogisticRegressionConfiguration",
    props: {
        configuration: Object,
    },
    data() {
        return {
            params: {
                regularizerType: 'none',
                solver: 'liblinear',
                lambda: 1.0
            },
            showingInfo: null
        }
    },
    mounted() {
        if (this.configuration.regularizerType) {
            this.params.regularizerType = this.configuration.regularizerType
        }
        if (this.configuration.solver) {
            this.params.solver = this.configuration.solver
        }
        if (this.configuration.lambda) {
            this.params.lambda = this.configuration.lambda
        }
    },
    methods: {
        handleSubmit() {
            this.$emit("save-configuration", this.params);
        },
        toggleInfo(parameter) {
            this.showingInfo = this.showingInfo === parameter ? null : parameter
        }
    }
}
</script>