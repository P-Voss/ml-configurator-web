<template>
    <div class="row g-3">
        <div class="col-12 col-lg-3">
            <label class="form-label" for="modelname">{{$t("label.name")}} *</label>
            <input type="text" class="form-control" id="modelname" v-model="name" required>
        </div>

        <div class="col-12 col-lg-9">
            <label class="form-label" for="description">{{$t("label.description")}}</label>
            <input type="text" class="form-control" id="description" v-model="description" required>
        </div>

        <div class="col-12">
            <div class="row">
                <div class="h2">{{$t("headline.tasktype")}}</div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-6 cardContainer">
                    <div
                        class="card selectionCard"
                        @click="setTaskType(tasktypes.TASK_TYPE_CLASSIFICATION)"
                        :class="{'activeCard' : taskType === tasktypes.TASK_TYPE_CLASSIFICATION}"
                    >
                        <div class="card-body">
                            <h5 class="card-title">{{$t("card.classificationLabel")}}</h5>
                            <p class="card-text">
                                {{$t("card.classificationDescription")}}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 cardContainer">
                    <div
                        class="card selectionCard"
                        @click="setTaskType(tasktypes.TASK_TYPE_REGRESSION)"
                        :class="{'activeCard' : taskType === tasktypes.TASK_TYPE_REGRESSION}"
                    >
                        <div class="card-body">
                            <h5 class="card-title">{{$t("card.regressionLabel")}}</h5>
                            <p class="card-text">
                                {{$t("card.regressionDescription")}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-12" v-if="taskType === tasktypes.TASK_TYPE_CLASSIFICATION">
            <div class="row">
                <div class="h2">{{$t("headline.modeltype")}}</div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-4 cardContainer">
                    <div
                        class="card selectionCard"
                        @click="setModelType(modelTypes.MODEL_TYPE_DTREE)"
                        :class="{'activeCard' : modelType === modelTypes.MODEL_TYPE_DTREE}"
                    >
                        <div class="card-body">
                            <h5 class="card-title">{{$t("card.dtreeLabel")}}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">{{$t("card.dtreeSubLabel")}}</h6>
                            <p class="card-text">
                                {{$t("card.dtreeDescription")}}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 cardContainer">
                    <div
                        class="card selectionCard"
                        @click="setModelType(modelTypes.MODEL_TYPE_LOG_REGR)"
                        :class="{'activeCard' : modelType === modelTypes.MODEL_TYPE_LOG_REGR}"
                    >
                        <div class="card-body">
                            <h5 class="card-title">{{$t("card.logregLabel")}}</h5>
                            <p class="card-text">
                                {{$t("card.logregDescription")}}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 cardContainer">
                    <div
                        class="card selectionCard"
                        @click="setModelType(modelTypes.MODEL_TYPE_SVM)"
                        :class="{'activeCard' : modelType === modelTypes.MODEL_TYPE_SVM}"
                    >
                        <div class="card-body">
                            <h5 class="card-title">{{$t("card.svmLabel")}}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">{{$t("card.svmSubLabel")}}</h6>
                            <p class="card-text">
                                {{$t("card.svmDescription")}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-12" v-if="taskType === tasktypes.TASK_TYPE_REGRESSION">
            <div class="row">
                <div class="h2">{{$t("headline.modeltype")}}</div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-4 cardContainer">
                    <div
                        class="card selectionCard disabled"
                        @click="setModelType(modelTypes.MODEL_TYPE_LIN_REGR)"
                        :class="{'activeCard' : modelType === modelTypes.MODEL_TYPE_LIN_REGR}"
                    >
                        <div class="card-body">
                            <h5 class="card-title">{{$t("card.linregLabel")}}</h5>
                            <p class="card-text">
                                {{$t("card.linregDescription")}}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 cardContainer">
                    <div
                        class="card selectionCard"
                        @click="setModelType(modelTypes.MODEL_TYPE_NEUR)"
                        :class="{'activeCard' : modelType === modelTypes.MODEL_TYPE_NEUR}"
                    >
                        <div class="card-body">
                            <h5 class="card-title">{{$t("card.nnLabel")}}</h5>
                            <p class="card-text">
                                {{$t("card.nnDescription")}}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 cardContainer">
                    <div
                        class="card selectionCard"
                        @click="setModelType(modelTypes.MODEL_TYPE_RNN)"
                        :class="{'activeCard' : modelType === modelTypes.MODEL_TYPE_RNN}"
                    >
                        <div class="card-body">
                            <h5 class="card-title">{{$t("card.rnnLabel")}}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">{{$t("card.rnnSubLabel")}}</h6>
                            <p class="card-text">
                                {{$t("card.rnnDescription")}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-1" v-if="formCompleted">
            <button class="btn btn-primary" @click="initializeModel">{{$t("button.save")}}</button>
        </div>

        <div class="col-12 col-lg-6 text-muted" v-if="formCompleted">
            {{$t('helptext.initModelchangeHint')}}
        </div>
    </div>
</template>


<script>

export default {
    name: 'InitForm',
    props: {
        existingId: String,
        existingName: String,
        existingDescription: String,
        existingType: String
    },
    data() {
        return {
            name: "",
            description: "",
            taskType: null,
            modelType: null,
            tasktypes: {
                TASK_TYPE_CLASSIFICATION: "TASK_TYPE_CLASSIFICATION",
                TASK_TYPE_REGRESSION: "TASK_TYPE_REGRESSION",
            },
            modelTypes: {
                MODEL_TYPE_DTREE: "MODEL_TYPE_DTREE",
                MODEL_TYPE_LOG_REGR: "MODEL_TYPE_LOG_REGR",
                MODEL_TYPE_SVM: "MODEL_TYPE_SVM",
                MODEL_TYPE_LIN_REGR: "MODEL_TYPE_LIN_REGR",
                MODEL_TYPE_NEUR: "MODEL_TYPE_NEUR",
                MODEL_TYPE_RNN: "MODEL_TYPE_RNN",
            }
        };
    },
    mounted() {
        if (this.existingId) {
            this.name = this.existingName
            this.description = this.existingDescription
            this.modelType = this.existingType

            if ([this.modelTypes.MODEL_TYPE_DTREE, this.modelTypes.MODEL_TYPE_SVM, this.modelTypes.MODEL_TYPE_LOG_REGR].indexOf(this.existingType) < 0) {
                this.taskType = this.tasktypes.TASK_TYPE_REGRESSION
            } else {
                this.taskType = this.tasktypes.TASK_TYPE_CLASSIFICATION
            }
        }
    },
    computed: {
        formCompleted() {
            return this.taskType !== null && this.modelType !== null && this.name !== ""
        }
    },
    methods: {
        setTaskType(type) {
            this.taskType = type
            this.modelType = null
        },
        setModelType(type) {
            this.modelType = type
        },
        initializeModel() {
            this.$emit('init', {
                id: this.existingId,
                name: this.name,
                description: this.description,
                modeltype: this.modelType
            })
        }
    }
};
</script>
