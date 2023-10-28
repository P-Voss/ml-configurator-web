<template>
    <form @submit.prevent="handleSubmit" class="gx-4">
        <div class="row mb-3">

            <div class="col-12 col-lg-6">
                <label for="kernel" class="form-label">Kernel</label>
                <div class="input-group">
                    <select class="form-select" id="kernel" v-model="params.kernel">
                        <option value="linear">Linear</option>
                        <option value="poly">Polynomial</option>
                        <option value="rbf">RBF (Radial Basis Function)</option>
                        <option value="sigmoid">Sigmoid</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('kernel')">?</button>
                </div>
                <div v-if="showingInfo === 'kernel'" class="form-text">
                    {{$t("helptext.svmKernel")}}
                    <p>
                        <span class="fw-bold">Linear: </span>{{$t("helptext.svmKernelLinear")}}
                    </p>
                    <p>
                        <span class="fw-bold">Polynomial: </span>{{$t("helptext.svmKernelPolynomial")}}
                    </p>
                    <p>
                        <span class="fw-bold">RBF (Radial Basis Function): </span>{{$t("helptext.svmKernelRBF")}}
                    </p>
                    <p>
                        <span class="fw-bold">Sigmoid: </span>{{$t("helptext.svmKernelSigmoid")}}
                    </p>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <label for="c" class="form-label">{{$t("label.svmC")}}</label>
                <div class="input-group">
                    <input type="range" class="form-range me-2" style="max-width: 90%;" min="0" step="0.01" max="1000" id="c" v-model="params.c" />
                    <button type="button" class="btn btn-outline-secondary rounded-circle" @click="toggleInfo('c')">?</button>
                    <span class="ms-3" style="margin-top: -5px">{{ params.c }}</span>
                </div>
                <div v-if="showingInfo === 'c'" class="form-text">
                    {{$t("helptext.svmC")}}
                </div>
            </div>
        </div>

        <div class="row mb-3">

            <div class="col-12 col-lg-6">
                <label for="degree" class="form-label">{{$t("label.svmDegree")}}</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="degree" v-model="params.degree" :disabled="params.kernel !== 'poly'" />
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('degree')">?</button>
                </div>
                <div v-if="showingInfo === 'degree'" class="form-text">
                    {{$t("helptext.svmDegree")}}
                </div>
                <small v-if="params.kernel !== 'poly'" class="form-text text-muted">
                    {{$t("helptext.svmDegreeHint")}}
                </small>
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
    name: "SvmArchitecture",
    props: {
        architecture: Object,
    },
    data() {
        return {
            params: {
                kernel: 'linear',
                c: 1,
                degree: 3
            },
            showingInfo: null
        }
    },
    mounted() {
        if (this.architecture.kernel) {
            this.params.kernel = this.architecture.kernel
        }
        if (this.architecture.c) {
            this.params.c = this.architecture.c
        }
        if (this.architecture.degree) {
            this.params.degree = this.architecture.degree
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
