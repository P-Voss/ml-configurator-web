<template>
    <div class="row gy-2">
        <div class="col-12 col-lg-4 cardContainer" v-for="field in fieldConfigurations" :key="field.id">
            <Field :field="field" @toggle-ignore="toggleIgnore" @toggle-target="toggleTarget" />
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
import Field from "./Field.vue";

export default {
    name: 'FieldList',
    components: {Field, FieldConfiguration},
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
        toggleIgnore(params) {
            this.$emit('toggle-ignore', {fieldId: params.id, setIgnore: params.val})
        },
        toggleTarget(params) {
            this.$emit('toggle-target', {fieldId: params.id, markTarget: params.val})
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
        },
    }
}

</script>