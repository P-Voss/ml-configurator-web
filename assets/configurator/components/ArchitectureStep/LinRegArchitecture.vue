<template>
    <form @submit.prevent="handleSubmit" class="gx-4">
        <div class="row mb-3">
            <div class="col-12 col-lg-6">
                <label for="regularizationType" class="form-label">{{$t("label.linregRegularization")}}</label>
                <div class="input-group">
                    <select class="form-select" id="regularizationType" v-model="params.regularizationType">
                        <option value="none">{{$t("option.linregRegularizationNone")}}</option>
                        <option value="l1">{{$t("option.linregRegularizationL1")}}</option>
                        <option value="l2">{{$t("option.linregRegularizationL2")}}</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('regularizationType')">?</button>
                </div>
                <div v-if="showingInfo === 'regularizationType'" class="form-text">
                    {{$t("helptext.linregRegularization")}}
                    <p>
                        <span class="fw-bold">{{$t("option.linregRegularizationL1")}}: </span>{{$t("helptext.linregRegularizationL1")}}
                    </p>
                    <p>
                        <span class="fw-bold">{{$t("option.linregRegularizationL2")}}: </span>{{$t("helptext.linregRegularizationL2")}}
                    </p>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <label for="alpha" class="form-label">Alpha</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="alpha" v-model="params.alpha" />
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('alpha')">?</button>
                </div>
                <div v-if="showingInfo === 'alpha'" class="form-text">
                    {{$t("helptext.linregAlpha")}}
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
    name: "LinRegArchitecture",
    props: {
        configuration: Object,
    },
    data() {
        return {
            params: {
                regularizationType: 'none',
                alpha: 1,
            },
            showingInfo: null,
        };
    },
    mounted() {
        if (this.configuration.regularizationType) {
            this.params.regularizationType = this.configuration.regularizationType;
        }
        if (this.configuration.alpha) {
            this.params.alpha = this.configuration.alpha;
        }
    },
    methods: {
        handleSubmit() {
            this.$emit("save-configuration", this.params);
        },
        toggleInfo(parameter) {
            this.showingInfo = this.showingInfo === parameter ? null : parameter;
        },
    },
};
</script>