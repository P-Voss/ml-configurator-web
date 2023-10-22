<template>
    <div class="row gy-2">
        <div class="col-12 col-lg-4 cardContainer" v-for="(field, index) in fieldConfigurations" :key="index">
            <div class="card bg-info" >
                <div class="card-body">
                    <h5 class="card-title">{{ field.name }}</h5>
                    <div class="row">
                        <div class="col-4">
                            <button v-if="!field.ignore" @click="toggleIgnore(field.name, true)" class="btn btn-primary btn-sm">
                                Ignorieren
                            </button>
                            <button v-if="field.ignore" @click="toggleIgnore(field.name, false)" class="btn btn-primary btn-sm">
                                Nicht ignorieren
                            </button>
                        </div>
                        <div class="col-4">
                            <button v-if="!field.isTarget" @click="toggleTarget(field.name, true)" class="btn btn-primary btn-sm">
                                Als Zielvariable setzen
                            </button>
                            <button v-if="field.isTarget" @click="toggleTarget(field.name, false)" class="btn btn-secondary btn-sm">
                                Als Feature verwenden
                            </button>
                        </div>
                        <div class="col-4">
                            <button @click="openConfigModal(field.name)" class="btn btn-primary btn-sm">
                                Konfigurieren
                            </button>
                        </div>
                    </div>
                    <div class="row" v-if="field.isTarget">
                        <div class="col-12">
                            <h4>Target</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" ref="modalRef" id="fieldModal" tabindex="-1" aria-labelledby="fieldModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="fieldModalLabel">Feldkonfiguration</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <FieldConfiguration v-if="activeField !== null" :field="activeField" @save-configuraiton="saveConfiguration" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import FieldConfiguration from "./FieldConfiguration.vue";

export default {
    name: 'FieldList',
    components: {FieldConfiguration},
    props: {
        fieldConfigurations: Array
    },
    data() {
        return {
            activeField: null,
            modalOpen: false,
            modal: null,
        }
    },
    methods: {
        toggleIgnore(fieldname, setIgnore) {
            this.$emit('toggle-ignore', {fieldname: fieldname, setIgnore: setIgnore})
        },
        toggleTarget(fieldname, markTarget) {
            this.$emit('toggle-target', {fieldname: fieldname, markTarget: markTarget})
        },
        openConfigModal(fieldname) {
            for (let field of this.fieldConfigurations) {
                if (field.name === fieldname) {
                    this.activeField = field
                }
            }
            if (this.activeField !== null) {
                this.modalOpen = true
                this.modal = new bootstrap.Modal(this.$refs.modalRef)
                this.modal.show()
            }
        },
        closeModal() {
            this.modalOpen = false
            this.modal.hide()
            this.modal = null
            this.activeField = null
        },
        saveConfiguration(form) {
            this.$emit('save-configuration', form)
        }
    }
}

</script>