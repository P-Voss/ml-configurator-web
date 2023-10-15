<template>
    <div class="row">
        <div class="col-12">
            <TrainerNavigation
                :init-step-completed="initTrainingStepCompleted"
                :current-step="currentStep"
                :configurator-url="configuratorUrl"
                @set-step="val => currentStep = val"
            />

            <div class="tab-content" id="pills-tabContent" v-if="modelState === 'COMPLETED'">
                <div
                    class="tab-pane fade"
                    :class="{'active': currentStep === 'DATA_PREP', 'show': currentStep === 'DATA_PREP'}"
                    id="pills-basis"
                    role="tabpanel"
                    aria-labelledby="trainer-data-prep"
                >
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <h3>Testdaten hochladen</h3>
                            <form @submit.prevent="uploadData">
                                <div class="mb-3">
                                    <label for="dataFile" class="form-label">Wählen Sie Ihre Datei aus:</label>
                                    <input type="file" class="form-control" id="dataFile" @change="setFile" ref="file" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </form>
                        </div>
                        <div class="col-12 col-lg-6" v-if="uploadComplete">
                            <h3>Datei Informationen</h3>
                            <div>
                                <span class="fw-bold">Dateiname: </span> {{filedata.filename}}
                            </div>
                            <div>
                                <span class="fw-bold">Zeitpunkt Upload: </span> {{filedata.uploadDate}}
                            </div>
                            <div>
                                <span class="fw-bold">Anzahl Einträge: </span> {{filedata.entryCount}}
                            </div>
                        </div>
                    </div>


                    <div v-if="uploadComplete && fieldConfigurations && fieldConfigurations.length > 0">
                        <h4>Datei-Details</h4>
                        <div class="row gy-2">
                            <div class="col-12 col-lg-4 cardContainer" v-for="(field, index) in fieldConfigurations" :key="index">
                                <div class="card bg-info" >
                                    <div class="card-body">
                                        <h5 class="card-title">{{ field.name }}</h5>
                                        <div class="row" v-if="!field.isTarget">
                                            <div class="col-6">
                                                <button @click="setTargetVariable(field.name)" class="btn btn-primary btn-sm">
                                                    Als Zielvariable setzen
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button @click="openConfigModal(field)" class="btn btn-primary btn-sm">
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
                        </div>
                    </div>
                </div>


                <div
                    class="tab-pane fade"
                    :class="{'active': currentStep === 'HYPERPARAMETER', 'show': currentStep === 'HYPERPARAMETER'}"
                    id="pills-basis"
                    role="tabpanel"
                    aria-labelledby="trainer-hyperparameter"
                >

                </div>


                <div
                    class="tab-pane fade"
                    :class="{'active': currentStep === 'TRAINING', 'show': currentStep === 'TRAINING'}"
                    id="pills-basis"
                    role="tabpanel"
                    aria-labelledby="trainer-training"
                >

                </div>
            </div>
        </div>
    </div>
</template>

<script>

import TrainerNavigation from "./Navigation.vue"
import {loadModel} from "../../configurator/services/ModelService.js"
import DataService from "../services/DataService.js"

export default {
    name: 'Trainer',
    components: {
        TrainerNavigation,
    },
    props: {
        modelId: String,
        loadModelUrl: String,
        configuratorUrl: String,
        uploadUrl: String,
    },
    data() {
        return {
            modelState: "PENDING",
            currentStep: "DATA_PREP",
            initTrainingStepCompleted: false,
            model: {
                id: '',
                name: '',
                description: '',
                type: '',
                architectureType: '',
            },
            filedata: {
                filename: '',
                uploadDate: '',
                entryCount: 0,
            },
            containsHeader: false,
            file: null,
            uploadComplete: false,
            fieldConfigurations: [],
        }
    },
    mounted() {
        if (!this.modelId) {
            this.modelState = "COMPLETED"
            return
        }
        this.loadModel(this.modelId)
    },
    methods: {
        loadModel(modelId) {
            this.modelState = "PENDING"
            let loadForm = new FormData()
            loadForm.set('modelId', modelId)
            loadModel(this.loadModelUrl, loadForm)
                .then(response => {
                    let data = response.data

                    if (data.success) {
                        this.model.id = modelId
                        this.model.name = data.model.name
                        this.model.description = data.model.description
                        this.model.type = data.model.type
                        this.model.architectureType = data.model.architectureType

                        if (data.model.uploadFile) {
                            this.fieldConfigurations = data.model.uploadFile.fieldConfigurations
                            this.uploadComplete = true

                            this.filedata.filename = data.model.uploadFile.filename
                            this.filedata.uploadDate = data.model.uploadFile.uploadDate
                            this.filedata.entryCount = data.model.uploadFile.entryCount
                        }

                    }
                    this.modelState = "COMPLETED"
                })
                .catch(error => {
                    console.log(error)
                })
        },
        setFile() {
            this.file = this.$refs.file.files[0]
        },
        async uploadData() {
            if (!this.file) {
                alert('Bitte eine Datei auswählen.');
                return;
            }

            let form = new FormData()
            form.set('id', this.model.id)
            form.append('file', this.file)

            DataService.uploadFile(this.uploadUrl, form)
                .then(response => {
                    console.log(response)
                    if (response.data.success) {
                        this.uploadComplete = true
                    }
                    this.loadModel(this.modelId)
                })
                .catch(error => {
                    console.log("error uploading")
                    console.log(error)
                })
        },
        setTargetVariable(headerItem) {

        },
        openConfigModal(headerItem) {

        },

    }
};
</script>
