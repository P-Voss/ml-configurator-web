<template>
    <div class="row">
        <div class="col-12">
            <TrainerNavigation
                :init-step-completed="model.validFieldConfiguration"
                :hyperparameter-step-completed="model.validHyperparameterConfiguration"
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

                        <FieldList
                            @toggle-target="toggleTarget"
                            @toggle-ignore="toggleIgnore"
                            @save-configuration="saveConfiguration"
                            :field-configurations="fieldConfigurations"
                        />

                    </div>
                </div>


                <div
                    class="tab-pane fade"
                    :class="{'active': currentStep === 'HYPERPARAMETER', 'show': currentStep === 'HYPERPARAMETER'}"
                    id="pills-basis"
                    role="tabpanel"
                    aria-labelledby="trainer-hyperparameter"
                >
                    <LogRegParameters
                        v-if="model.architectureType === 'LogRegression'"
                        @save-hyperparameters="saveHyperparameter"
                        :parameters="model.hyperparameters"
                    />
                    <DecisionTreeParameters
                        v-if="model.architectureType === 'DTREE'"
                        @save-hyperparameters="saveHyperparameter"
                        :parameters="model.hyperparameters"
                    />
                    <SvmParameters
                        v-if="model.architectureType === 'SVM'"
                        @save-hyperparameters="saveHyperparameter"
                        :parameters="model.hyperparameters"
                    />
                    <LinRegParameters
                        v-if="model.architectureType === 'LinRegression'"
                        @save-hyperparameters="saveHyperparameter"
                        :parameters="model.hyperparameters"
                    />
                    <DenseParameters
                        v-if="model.architectureType === 'FNN'"
                        @save-hyperparameters="saveHyperparameter"
                        :parameters="model.hyperparameters"
                    />
                    <RnnParameters
                        v-if="model.architectureType === 'RNN'"
                        @save-hyperparameters="saveHyperparameter"
                        :parameters="model.hyperparameters"
                    />
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
import FieldList from "./DataStep/FieldList.vue";
import LogRegParameters from "./HyperparameterStep/LogRegParameters.vue";
import DecisionTreeParameters from "./HyperparameterStep/DecisionTreeParameters.vue";
import SvmParameters from "./HyperparameterStep/SvmParameters.vue";
import LinRegParameters from "./HyperparameterStep/LinRegParameters.vue";
import DenseParameters from "./HyperparameterStep/DenseParameters.vue";
import RnnParameters from "./HyperparameterStep/RnnParameters.vue";
import {isGeneratorFunction} from "regenerator-runtime";

export default {
    name: 'Trainer',
    components: {
        RnnParameters,
        DenseParameters,
        LinRegParameters,
        SvmParameters,
        DecisionTreeParameters,
        LogRegParameters,
        FieldList,
        TrainerNavigation,
    },
    props: {
        modelId: String,
        loadModelUrl: String,
        configuratorUrl: String,
        uploadUrl: String,
        ignoreFieldUrl: String,
        unignoreFieldUrl: String,
        markTargetUrl: String,
        removeTargetUrl: String,
        updateFieldUrl: String,
        saveHyperparameterUrl: String,
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
                hyperparameters: {},
                architectureType: '',
                validFieldConfiguration: false,
                validHyperparameterConfiguration: false,
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
                        this.model.validFieldConfiguration = data.model.validFieldConfiguration

                        if (data.model.uploadFile) {
                            this.fieldConfigurations = data.model.uploadFile.fieldConfigurations
                            this.uploadComplete = true

                            this.filedata.filename = data.model.uploadFile.filename
                            this.filedata.uploadDate = data.model.uploadFile.uploadDate
                            this.filedata.entryCount = data.model.uploadFile.entryCount
                        }
                        if (data.model.hyperparameters) {
                            this.model.hyperparameters = data.model.hyperparameters
                            this.model.validHyperparameterConfiguration = true
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
        async toggleTarget(params) {
            let form = new FormData()
            form.set('id', this.model.id)
            form.set('fieldname', params.fieldname)
            if (params.markTarget) {
                await DataService.toggleFieldState(this.markTargetUrl, form)
            } else {
                await DataService.toggleFieldState(this.removeTargetUrl, form)
            }
            this.loadModel(this.model.id)
        },
        async toggleIgnore(params) {
            let form = new FormData()
            form.set('id', this.model.id)
            form.set('fieldname', params.fieldname)
            if (params.setIgnore) {
                await DataService.toggleFieldState(this.ignoreFieldUrl, form)
            } else {
                await DataService.toggleFieldState(this.unignoreFieldUrl, form)
            }
            this.loadModel(this.model.id)
        },
        async saveConfiguration(form) {
            form.set('id', this.model.id)
            await DataService.updateField(this.updateFieldUrl, form)
            this.loadModel(this.model.id)
        },
        async saveHyperparameter(form) {
            form.set('id', this.model.id)
            let response = await DataService.saveHyperparameter(this.saveHyperparameterUrl, form)
            console.log(response)
            this.loadModel(this.model.id)
        }
    }
};
</script>
