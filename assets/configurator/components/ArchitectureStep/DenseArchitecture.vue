<template>
    <div class="row g-3">
        <div class="col-12">
            Die Architektur eines neuronalen Netzes ist ein entscheidender Faktor für dessen Leistung und
            Anpassungsfähigkeit. Jede Schicht des Netzes hat einen speziellen Zweck und kann dazu beitragen, unterschiedliche
            Aspekte deiner Daten zu modellieren. Hier wirst du die Möglichkeit haben, deine
            eigene Netzwerkarchitektur Schicht für Schicht aufzubauen.
        </div>

        <div class="col-12 col-lg-8">
            <div class="h2">Art der Schicht</div>
            <div class="row gy-3">
                <div class="col-12 col-lg-6 cardContainer">
                    <div
                        class="card selectionCard"
                        @click="openModal(layerTypes.LAYER_TYPE_DENSE)"
                        :class="{'activeCard' : type === layerTypes.LAYER_TYPE_DENSE}"
                    >
                        <div class="card-body">
                            <h5 class="card-title">Dense</h5>
                            <h6 class="card-subtitle mb-2 text-muted">(Vollverknüpft)</h6>
                            <p class="card-text">
                                Dies sind die Standard-Schichten, die du in den meisten neuronalen Netzen findest.
                                Jedes Neuron in einer Dense-Schicht ist mit jedem Neuron in der vorherigen Schicht verbunden.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 cardContainer">
                    <div
                        class="card selectionCard"
                        @click="openModal(layerTypes.LAYER_TYPE_DROPOUT)"
                        :class="{'activeCard' : type === layerTypes.LAYER_TYPE_DROPOUT}"
                    >
                        <div class="card-body">
                            <h5 class="card-title">Dropout</h5>
                            <p class="card-text">
                                Eine Technik zur Reduzierung von Overfitting, bei der zufällig ausgewählte
                                Neuronen während des Trainingsprozesses "ausgeschaltet" werden.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-12 col-lg-4">
            <div class="h2">Modell-Architektur</div>
            <div class="row">
                <div class="col-12" v-for="layer in layers">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-10">
                                    <div class="card-title">
                                        {{layertypeName(layer.type)}}
                                    </div>
                                </div>
                                <div class="col-2">
                                    <button class="btn btn-danger" @click="removeLayer(layer.id)">x</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal -->
        <div class="modal fade" ref="modalRef" id="layerModal" tabindex="-1" aria-labelledby="layerModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="layerModalLabel" v-if="type === layerTypes.LAYER_TYPE_DENSE">Dense Layer</h5>
                        <h5 class="modal-title" id="layerModalLabel" v-if="type === layerTypes.LAYER_TYPE_DROPOUT">Dropout-Regularisierung</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="modalOpen = false"></button>
                    </div>
                    <div class="modal-body">
                        <DenseLayerForm @add-layer="addLayer" v-if="type === layerTypes.LAYER_TYPE_DENSE" />
                        <DropoutLayerForm @add-layer="addLayer" v-if="type === layerTypes.LAYER_TYPE_DROPOUT" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import DenseLayerForm from "./Forms/DenseLayerForm.vue";
import DropoutLayerForm from "./Forms/DropoutLayerForm.vue";

export default {
    name: 'RnnArchitecture',
    props: {
        layers: Array,
    },
    components: {
        DenseLayerForm,
        DropoutLayerForm
    },
    data() {
        return {
            type: '',
            layerTypes: {
                LAYER_TYPE_DENSE: "LAYER_TYPE_DENSE",
                LAYER_TYPE_DROPOUT: "LAYER_TYPE_DROPOUT",
            },
            modalOpen: false,
            modal: null,
            currentForm: "",
        }
    },
    computed: {

    },
    methods: {
        openModal(type) {
            this.type = type;
            this.modalOpen = true;
            this.modal = new bootstrap.Modal(this.$refs.modalRef);
            this.modal.show();
        },
        addLayer(params) {
            console.log(params)
            if (this.layers.length === 0 && params.type === this.layerTypes.LAYER_TYPE_DROPOUT) {
                alert('Can not place Dropout as first layer')
                return
            }
            this.modalOpen = false
            this.modal.hide();
            this.model = null
            this.type = ''
            this.$emit("add-layer", params);
        },
        removeLayer(layerId) {
            this.$emit('remove-layer', layerId)
        },
        layertypeName(type) {
            switch (type) {
                case this.layerTypes.LAYER_TYPE_DENSE:
                    return "Dense Layer"
                default:
                    return "Dropout"
            }
        }
    }
}

</script>