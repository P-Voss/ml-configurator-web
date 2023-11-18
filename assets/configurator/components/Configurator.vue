<template>
    <div class="row">
        <div class="col-12">
            <Navigation
                :init-step-completed="initStepCompleted"
                :architecture-step-completed="architectureStepCompleted"
                :training-step-completed="architectureStepCompleted"
                :trainer-url="trainerUrl"
                :executor-url="executorUrl"
                :current-step="currentStep"
                @set-step="val => currentStep = val"
                v-if="modelState === 'COMPLETED'"
            />
            <div class="tab-content" id="pills-tabContent" v-if="modelState === 'COMPLETED'">
                <div
                    class="tab-pane fade"
                    :class="{'active': currentStep === 'INIT', 'show': currentStep === 'INIT'}"
                    id="pills-basis"
                    role="tabpanel"
                    aria-labelledby="pills-basis-tab"
                >
                    <InitForm
                        :existing-id="model.id"
                        :existing-name="model.name"
                        :existing-description="model.description"
                        :existing-dataset="model.dataset"
                        :existing-type="model.type"
                        @init="saveModel"
                    />
                </div>
                <div
                    class="tab-pane fade"
                    :class="{'active': currentStep === 'ARCHITECTURE', 'show': currentStep === 'ARCHITECTURE'}"
                    id="pills-architektur"
                    role="tabpanel"
                    aria-labelledby="pills-architektur-tab"
                >
                    <RnnArchitecture
                        :model-id="model.id"
                        :layers="model.layers"
                        @add-layer="addLayer"
                        @remove-layer="removeLayer"
                        v-if="isRnnArchitecture"
                    />
                    <DenseArchitecture
                        :model-id="model.id"
                        :layers="model.layers"
                        @add-layer="addLayer"
                        @remove-layer="removeLayer"
                        v-if="isFnnArchitecture"
                    />
                    <DtreeArchitecture
                        :architecture="model.decisiontreeConfiguration"
                        @save-configuration="saveDtreeConfiguration"
                        v-if="isDtreeArchitecture"
                    />
                    <SvmArchitecture
                        :architecture="model.svmConfiguration"
                        @save-configuration="saveSvmConfiguration"
                        v-if="isSvmArchitecture"
                    />
                    <LogRegArchitecture
                        :configuration="model.logRegConfiguration"
                        @save-configuration="saveLogRegConfiguration"
                        v-if="isLogRegArchitecture"
                    />
                    <LinRegArchitecture
                        :configuration="model.linRegConfiguration"
                        @save-configuration="saveLinRegConfiguration"
                        v-if="isLinRegArchitecture"
                    />
                </div>

            </div>
        </div>
    </div>
</template>

<script>

import Navigation from "./Navigation.vue";
import InitForm from "./InitStep/InitForm.vue";
import RnnArchitecture from "./ArchitectureStep/RnnArchitecture.vue";
import DenseArchitecture from "./ArchitectureStep/DenseArchitecture.vue";
import DtreeArchitecture from "./ArchitectureStep/DtreeArchitecture.vue";
import SvmArchitecture from "./ArchitectureStep/SvmArchitecture.vue";
import LogRegArchitecture from "./ArchitectureStep/LogRegArchitecture.vue";
import LinRegArchitecture from "./ArchitectureStep/LinRegArchitecture.vue";

import {initializeModel, updateModel, loadModel} from "../services/ModelService.js";
import ArchitectureService from "../services/ArchitectureService.js";

export default {
    name: 'Configurator',
    props: {
        modelId: String,
        initializeUrl: String,
        updateUrl: String,
        loadModelUrl: String,
        trainerUrl: String,
        executorUrl: String,
        addLayerUrl: String,
        removeLayerUrl: String,
        saveDtreeConfigUrl: String,
        saveSvmConfigUrl: String,
        saveLogRegConfigUrl: String,
        saveLinRegConfigUrl: String,
    },
    components: {
        Navigation,
        RnnArchitecture,
        DenseArchitecture,
        DtreeArchitecture,
        SvmArchitecture,
        LogRegArchitecture,
        LinRegArchitecture,
        InitForm,
    },
    data() {
        return {
            currentStep: "INIT",
            initStepCompleted: false,
            dataStepCompleted: false,
            hyperparameterStepCompleted: false,
            architectureStepCompleted: false,
            trainingStepCompleted: false,
            modelState: "PENDING",
            model: {
                id: '',
                name: '',
                description: '',
                dataset: '',
                type: '',
                architectureType: '',
                layers: [],
                decisiontreeConfiguration: {},
                svmConfiguration: {},
                linRegConfiguration: {},
                logRegConfiguration: {},
            }
        }
    },
    mounted() {
        if (!this.modelId) {
            this.modelState = "COMPLETED"
            return
        }
        this.loadModel(this.modelId)
    },
    computed: {
        isFnnArchitecture() {
            return this.model.architectureType === 'FNN'
        },
        isRnnArchitecture() {
            return this.model.architectureType === 'RNN'
        },
        isDtreeArchitecture() {
            return this.model.architectureType === 'DTREE'
        },
        isSvmArchitecture() {
            return this.model.architectureType === 'SVM'
        },
        isLinRegArchitecture() {
            return this.model.architectureType === 'LinRegression'
        },
        isLogRegArchitecture() {
            return this.model.architectureType === 'LogRegression'
        },
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
                        this.model.dataset = data.model.dataset
                        this.model.type = data.model.type
                        this.model.architectureType = data.model.architectureType
                        this.model.layers = data.model.layers
                        this.model.decisiontreeConfiguration = data.model.decisiontreeConfiguration ?? {}
                        this.model.svmConfiguration = data.model.svmConfiguration ?? {}
                        this.model.linRegConfiguration = data.model.linRegConfiguration ?? {}
                        this.model.logRegConfiguration = data.model.logRegConfiguration ?? {}

                        this.initStepCompleted = data.model.validBaseData
                        this.architectureStepCompleted = data.model.validArchitecture
                        this.trainingStepCompleted = data.model.validTraining
                    }
                    this.modelState = "COMPLETED"
                })
                .catch(error => {
                    console.log(error)
                })
        },
        async saveModel(params) {

            let form = new FormData()
            form.set('name', params.name)
            form.set('description', params.description)
            form.set('dataset', params.dataset)
            form.set('modeltype', params.modeltype)

            if (params.id) {
                form.set('id', params.id)
                updateModel(this.updateUrl, form)
                    .then(response => {
                        console.log(response)
                        if (response.data.success) {
                            this.loadModel(this.model.id)
                        }
                    })
                    .catch(error => {
                        console.log(error)
                    })
            } else {
                initializeModel(this.initializeUrl, form)
                    .then(response => {
                        if (response.data.success) {
                            window.location.replace(response.data.redirectUrl)
                        }
                    })
                    .catch(error => {
                        console.log(error)
                    })
            }
        },
        async addLayer(params) {
            let form = new FormData()
            form.set('id', this.model.id)
            form.set('type', params.type)
            form.set('neurons', params.neurons)
            form.set('returnSequences', params.returnSequences)
            form.set('activationFunction', params.activationFunction)
            form.set('regularizationType', params.regularizationType)
            form.set('lambda', params.lambda)
            form.set('dropoutRate', params.dropoutRate)
            form.set('recurrentDropoutRate', params.recurrentDropoutRate)

            await ArchitectureService.addLayer(this.addLayerUrl, form)
            this.loadModel(this.model.id)
        },
        async removeLayer(layerId) {


            let form = new FormData()
            form.set('modelId', this.model.id)
            form.set('layerId', layerId)

            await ArchitectureService.removeLayer(this.removeLayerUrl, form)
            this.loadModel(this.model.id)
        },
        async saveDtreeConfiguration(params) {
            let form = new FormData()
            form.set('id', this.model.id)
            form.set('maxDepth', params.maxDepth)
            form.set('minSampleSplit', params.minSampleSplit)
            form.set('maxFeatures', params.maxFeatures)
            form.set('minSamplesLeaf', params.minSamplesLeaf)
            form.set('missingValueHandling', params.missingValueHandling)
            form.set('qualityMeasure', params.qualityMeasure)

            await ArchitectureService.setConfiguration(this.saveDtreeConfigUrl, form)
            this.loadModel(this.model.id)
        },
        async saveSvmConfiguration(params) {
            let form = new FormData()
            form.set('id', this.model.id)
            form.set('kernel', params.kernel)
            form.set('c', params.c)
            form.set('degree', params.degree)

            await ArchitectureService.setConfiguration(this.saveSvmConfigUrl, form)
            this.loadModel(this.model.id)
        },
        async saveLogRegConfiguration(params) {
            let form = new FormData()
            form.set('id', this.model.id)
            form.set('regularizerType', params.regularizerType)
            form.set('solver', params.solver)
            form.set('lambda', params.lambda)

            await ArchitectureService.setConfiguration(this.saveLogRegConfigUrl, form)
            this.loadModel(this.model.id)
        },
        async saveLinRegConfiguration(params) {
            let form = new FormData()
            form.set('id', this.model.id)
            form.set('regularizationType', params.regularizationType)
            form.set('alpha', params.alpha)

            await ArchitectureService.setConfiguration(this.saveLinRegConfigUrl, form)
            this.loadModel(this.model.id)
        }
    }
};
</script>
