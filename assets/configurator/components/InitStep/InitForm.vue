<template>
    <div class="row g-3">
        <div class="col-12 col-lg-3">
            <label class="form-label" for="modelname">Name *</label>
            <input type="text" class="form-control" id="modelname" v-model="name" required>
        </div>

        <div class="col-12 col-lg-9">
            <label class="form-label" for="description">Beschreibung</label>
            <input type="text" class="form-control" id="description" v-model="description" required>
        </div>

        <div class="col-12">
            <div class="row">
                <div class="h2">Aufgabentyp</div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-6 cardContainer">
                    <div
                        class="card selectionCard"
                        @click="setTaskType(tasktypes.TASK_TYPE_CLASSIFICATION)"
                        :class="{'activeCard' : taskType === tasktypes.TASK_TYPE_CLASSIFICATION}"
                    >
                        <div class="card-body">
                            <h5 class="card-title">Klassifizierung</h5>
                            <p class="card-text">
                                Die Klassifizierung ist ein Überwachtes Lernen, bei dem das Ziel darin besteht,
                                eine Eingabe (wie ein Bild, Text oder ein anderer Datentyp) einer der vordefinierten Kategorien zuzuordnen.
                                Beispielsweise könnte man versuchen, Bilder von Tieren als "Hund", "Katze" oder "Vogel" zu klassifizieren.
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
                            <h5 class="card-title">Regressionsanalyse</h5>
                            <p class="card-text">
                                Regressionsanalysen versuchen, den Zusammenhang zwischen Eingabevariablen (Merkmale) und
                                einer kontinuierlichen Ausgabevariable (Ziel) zu modellieren. Zum Beispiel könnte man den Preis
                                eines Hauses basierend auf Merkmalen wie Größe, Lage und Alter vorhersagen.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-12" v-if="taskType === tasktypes.TASK_TYPE_CLASSIFICATION">
            <div class="row">
                <div class="h2">Modelltyp</div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-4 cardContainer">
                    <div
                        class="card selectionCard"
                        @click="setModelType(modelTypes.MODEL_TYPE_DTREE)"
                        :class="{'activeCard' : modelType === modelTypes.MODEL_TYPE_DTREE}"
                    >
                        <div class="card-body">
                            <h5 class="card-title">Decision Tree</h5>
                            <h6 class="card-subtitle mb-2 text-muted">(Entscheidungsbaum)</h6>
                            <p class="card-text">
                                Entscheidungsbäume teilen Daten rekursiv in Untergruppen, um Entscheidungen auf der Grundlage von Eingabemerkmalen zu treffen.
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
                            <h5 class="card-title">Logistische Regression</h5>
                            <p class="card-text">
                                Dieses Modell schätzt die Wahrscheinlichkeit, dass eine gegebene Eingabe zu einer bestimmten Klasse gehört.
                                Es ist besonders nützlich für binäre Klassifizierungsaufgaben, kann aber auch für mehrklassige Aufgaben verwendet werden.
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
                            <h5 class="card-title">SVM</h5>
                            <h6 class="card-subtitle mb-2 text-muted">(Support Vector Machines)</h6>
                            <p class="card-text">
                                Ein Modell, das versucht, eine optimale Trennlinie oder -fläche zwischen Datenklassen zu finden.
                                Es ist besonders nützlich für komplexe Klassifikationsprobleme, kann aber auch für Regression verwendet
                                werden.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-12" v-if="taskType === tasktypes.TASK_TYPE_REGRESSION">
            <div class="row">
                <div class="h2">Modelltyp</div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-4 cardContainer">
                    <div
                        class="card selectionCard disabled"
                        @click="setModelType(modelTypes.MODEL_TYPE_LIN_REGR)"
                        :class="{'activeCard' : modelType === modelTypes.MODEL_TYPE_LIN_REGR}"
                    >
                        <div class="card-body">
                            <h5 class="card-title">Lineare Regression</h5>
                            <p class="card-text">
                                Dieses Modell versucht, einen linearen Zusammenhang zwischen den Eingabemerkmalen und dem Zielwert zu finden.
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
                            <h5 class="card-title">Neuronales Netz</h5>
                            <p class="card-text">
                                Ein komplexes Modell, das aus vielen miteinander verbundenen "Neuronen" besteht.
                                Es kann nicht-lineare Zusammenhänge modellieren und ist für eine Vielzahl von Aufgaben geeignet, einschließlich Regressionsproblemen.
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
                            <h5 class="card-title">RNN</h5>
                            <h6 class="card-subtitle mb-2 text-muted">(Recurrent Neural Network)</h6>
                            <p class="card-text">
                                Eine spezielle Art von neuronalem Netzwerk, das für sequentielle Daten konzipiert ist.
                                Es ist besonders nützlich für Zeitreihen- oder Textdaten.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 justify-content-end" v-if="formCompleted">
            <button class="btn btn-primary" @click="initializeModel">Speichern</button>
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
        console.log(this.existingId)
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
