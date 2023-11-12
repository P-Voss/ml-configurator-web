<template>
    <div class="row">
        <div class="col-12">
            <Navigation :trainer-url="trainerUrl" :configuratorUrl="configuratorUrl" />

            <div class="tab-content" id="pills-tabContent" v-if="modelState === 'COMPLETED'">

                <div class="row">
                    <div class="col-md-6">
                        <h2>Modellparameter</h2>

                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12 col-lg-9">
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
                                        :placeholder="featureString"
                                        v-model="input"
                                        required
                                    ></textarea>
                                </div>
                            </div>
                            <div class="col-2 offset-10">
                                <button
                                    @click.prevent="apply"
                                    :class="{'disabled': input === ''}"
                                    class="btn btn-primary"
                                >
                                    Ausführen
                                </button>
                            </div>
                        </div>

                    </div>


                    <div class="col-12 col-lg-3">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="idColumn" v-model="includeIdColumn" />
                                    <label class="form-check-label" for="idColumn">Die erste Spalte enthält ID</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 fw-bold mb-3">
                                Benötigte Spalten
                            </div>
                            <div class="col-12">
                                <p v-if="includeIdColumn">ID</p>
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
            input: "",
            includeIdColumn: true,
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
                        if (this.includeIdColumn) {
                            features.push("ID")
                        }
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
            let form = new FormData()
            form.set('id', this.model.id)
            form.set('input', this.input)
            let response = await ApplicationService.execute(this.executeUrl, form)
            console.log(response)
        }
    }
}

</script>