<template>
    <div class="row">
        <div class="col-12">
            <Navigation :trainer-url="trainerUrl" :configuratorUrl="configuratorUrl" />

            <div class="tab-content" id="pills-tabContent" v-if="modelState === 'COMPLETED'">

                <div class="row">
                    <div class="col-12">
                        <h2>Modellparameter</h2>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-9 col-lg-6">
                        <div class="row gy-3">
                            <div class="col-12">
                                <h2>Eingabe der Features</h2>
                            </div>
                            <div class="col-12">
                                <div class="form-group mt-3">
                                    <textarea
                                        class="form-control"
                                        id="inputFeatures"
                                        rows="5"
                                        v-model="input"
                                        required
                                    ></textarea>
                                </div>
                            </div>
                            <div class="col-9">
                                {{message}}
                            </div>
                            <div class="col-2">
                                <button
                                    @click.prevent="apply"
                                    :class="{'disabled': input === '' || execState !== 'INIT'}"
                                    class="btn btn-primary"
                                >
                                    Ausführen
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-3 col-lg-2">
                        <div class="row ms-3 gy-3" v-if="result.length > 0">
                            <div class="col-12">
                                <h2>Ergebnis</h2>
                            </div>
                            <div class="col-12" style="max-height: 60vh; overflow-y: scroll;">
                                <p style="padding: 0; margin: 0;" v-for="(value, index) in result" :key="index">{{value}}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-3 offset-lg-1">
                        <div class="row">
                            <div class="col-12 fw-bold mb-3">
                                Benötigte Spalten
                            </div>
                            <div class="col-12" v-for="field in model.fieldConfigurations" :key="field.id">
                                <p v-if="!field.isIgnored && !field.isTarget">{{field.name}} - {{field.type}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import Navigation from "./Navigation.vue";
import {loadModel} from "../../configurator/services/ModelService.js";
import ApplicationService from "../services/ApplicationService.js";

export default {
    name: 'Application',
    components: {Navigation},
    props: {
        modelId: String,
        loadModelUrl: String,
        trainerUrl: String,
        configuratorUrl: String,
        executeUrl: String,
    },
    data() {
        return {
            modelState: "PENDING",
            execState: "INIT",
            input: "",
            model: {
                id: '',
                name: '',
                description: '',
                dataset: '',
                type: '',
                architectureType: '',
                layers: [],
                fieldConfigurations: [],
                decisiontreeConfiguration: {},
                svmConfiguration: {},
                linRegConfiguration: {},
                logRegConfiguration: {},
            },
            result: [],
            message: "",
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
                        this.model.fieldConfigurations = data.model.fieldConfigurations
                        this.model.decisiontreeConfiguration = data.model.decisiontreeConfiguration ?? {}
                        this.model.svmConfiguration = data.model.svmConfiguration ?? {}
                        this.model.linRegConfiguration = data.model.linRegConfiguration ?? {}
                        this.model.logRegConfiguration = data.model.logRegConfiguration ?? {}

                        let features = []
                        for (let field of this.model.fieldConfigurations) {
                            if (field.isIgnored) {
                                continue
                            }
                            if (field.isTarget) {
                                continue
                            }
                            features.push(field.name)
                        }
                        this.input = features.join(";")
                    }
                    this.modelState = "COMPLETED"
                })
                .catch(error => {
                    console.log(error)
                })
        },
        async apply() {
            this.execState = "PENDING"
            this.result = []
            this.message = ""
            let form = new FormData()
            form.set('id', this.model.id)
            form.set('input', this.input)
            let response = await ApplicationService.execute(this.executeUrl, form)
            if (response.data.success) {
                this.result = response.data.result
            } else {
                this.message = "Fehler bei der Verarbeitung"
            }
            this.execState = "INIT"
        }
    }
}

</script>