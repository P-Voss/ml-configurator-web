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
                    <div>
                        <h4>Trainingsdaten</h4>
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
                    <Training
                        :model="this.model"
                        :load-tasks-url="this.loadTasksUrl"
                        :submit-task-url="this.submitTaskUrl"
                        :execute-task-url="this.executeTaskUrl"
                        :load-example-url="this.loadExampleUrl"
                        @rollback="this.rollback"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import TrainerNavigation from "./Navigation.vue"
import {loadModel, rollback} from "../../configurator/services/ModelService.js"
import DataService from "../services/DataService.js"

import FieldList from "./DataStep/FieldList.vue";
import LogRegParameters from "./HyperparameterStep/LogRegParameters.vue";
import DecisionTreeParameters from "./HyperparameterStep/DecisionTreeParameters.vue";
import SvmParameters from "./HyperparameterStep/SvmParameters.vue";
import LinRegParameters from "./HyperparameterStep/LinRegParameters.vue";
import DenseParameters from "./HyperparameterStep/DenseParameters.vue";
import RnnParameters from "./HyperparameterStep/RnnParameters.vue";
import Training from "./TrainingStep/Training.vue";

export default {
    name: 'Trainer',
    components: {
        Training,
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
        submitTaskUrl: String,
        executeTaskUrl: String,
        loadTasksUrl: String,
        loadExampleUrl: String,
        rollbackUrl: String,
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
                configurationHash: "",
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
                        this.model.configurationHash = data.model.configurationHash

                        this.fieldConfigurations = data.model.fieldConfigurations

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
        async toggleTarget(params) {
            let form = new FormData()
            form.set('id', this.model.id)
            form.set('fieldId', params.fieldId)
            if (params.markTarget) {
                let response = await DataService.toggleFieldState(this.markTargetUrl, form)
                if (response.data.success) {
                    this.fieldConfigurations = response.data.fieldConfigurations
                }
            } else {
                let response = await DataService.toggleFieldState(this.removeTargetUrl, form)
                if (response.data.success) {
                    this.fieldConfigurations = response.data.fieldConfigurations
                }
            }
        },
        async toggleIgnore(params) {
            let form = new FormData()
            form.set('id', this.model.id)
            form.set('fieldId', params.fieldId)
            if (params.setIgnore) {
                let response = await DataService.toggleFieldState(this.ignoreFieldUrl, form)
                if (response.data.success) {
                    this.fieldConfigurations = response.data.fieldConfigurations
                }
            } else {
                let response = await DataService.toggleFieldState(this.unignoreFieldUrl, form)
                if (response.data.success) {
                    this.fieldConfigurations = response.data.fieldConfigurations
                }
            }
        },
        async saveConfiguration(form) {
            form.set('id', this.model.id)
            await DataService.updateField(this.updateFieldUrl, form)
            this.loadModel(this.model.id)
        },
        async saveHyperparameter(form) {
            form.set('id', this.model.id)
            let response = await DataService.saveHyperparameter(this.saveHyperparameterUrl, form)
            this.loadModel(this.model.id)
        },
        async rollback(taskId) {
            let form = new FormData()
            form.set('id', this.model.id)
            form.set('taskId', taskId)
            let response = await rollback(this.rollbackUrl, form)
            this.loadModel(this.model.id)
        }
    }
};
</script>
