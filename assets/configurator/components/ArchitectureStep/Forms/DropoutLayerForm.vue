<template>
    <form @submit.prevent="handleSubmit">

        <div class="mb-3">
            <label for="dropout" class="form-label">{{$t("label.dropout")}}</label>
            <div class="input-group">
                <input type="number" class="form-control" id="dropout" v-model="layer.dropoutRate" placeholder="0.0 - 1.0" min="0" max="1" step="0.01"/>
                <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('dropout')">?</button>
            </div>
            <div v-if="showingInfo === 'dropout'" class="form-text">
                {{$t("helptext.dropout")}}
            </div>
            <p class="mt-3">
                {{$t("label.dropoutIntro")}}
            </p>
        </div>

        <button type="submit" class="btn btn-primary">{{$t("button.addDropout")}}</button>

    </form>
</template>

<script>
export default {
    name: 'DropoutLayerForm',
    data() {
        return {
            layer: {
                type: 'LAYER_TYPE_DROPOUT',
                dropoutRate: 0.5,
            },
            showingInfo: null
        };
    },
    methods: {
        handleSubmit() {
            let params = {
                type: this.layer.type,
                neurons: 0,
                returnSequences: false,
                activationFunction: "",
                regularizationType: "",
                lambda: 0,
                dropoutRate: this.layer.dropoutRate,
                recurrentDropoutRate: 0,
            }

            this.$emit("add-layer", params);
        },
        toggleInfo(parameter) {
            this.showingInfo = this.showingInfo === parameter ? null : parameter;
        }
    }
}
</script>