<template>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-10">
                    <div class="card-title">
                        {{layertypeName(layer.type)}}
                    </div>
                    <div class="card-text" v-if="layer.type !== layerTypes.LAYER_TYPE_DROPOUT">
                        <div class="row">
                            <div class="col-12 text-muted">
                                Aktivierungsfunktion: {{layer.activationFunction}}
                            </div>
                            <div class="col-12 text-muted">
                                Neuronen: {{layer.neurons}}
                            </div>
                            <div class="col-12 text-muted">
                                Dropout-Rate: {{droputRate}}
                            </div>
                        </div>
                    </div>
                    <div class="card-text" v-if="layer.type === layerTypes.LAYER_TYPE_DROPOUT">
                        <div class="row">
                            <div class="col-12 text-muted">
                                Dropout-Rate: {{droputRate}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-2">
                    <button class="btn btn-danger" @click="removeLayer">x</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: 'Layer',
    props: {
        layer: Object,
    },
    data() {
        return {
            type: '',
            layerTypes: {
                LAYER_TYPE_DENSE: "LAYER_TYPE_DENSE",
                LAYER_TYPE_LSTM: "LAYER_TYPE_LSTM",
                LAYER_TYPE_GRU: "LAYER_TYPE_GRU",
                LAYER_TYPE_DROPOUT: "LAYER_TYPE_DROPOUT",
            },
        }
    },
    computed: {
        droputRate() {
            if (this.layer.dropoutQuote > 0) {
                return this.layer.dropoutQuote
            }
            return 0
        }
    },
    methods: {
        removeLayer() {
            this.$emit('remove-layer')
        },
        layertypeName(type) {
            switch (type) {
                case this.layerTypes.LAYER_TYPE_DENSE:
                    return "Dense Layer"
                case this.layerTypes.LAYER_TYPE_LSTM:
                    return "LSTM Layer"
                case this.layerTypes.LAYER_TYPE_GRU:
                    return "GRU Layer"
                default:
                    return "Dropout"
            }
        }
    }
}

</script>