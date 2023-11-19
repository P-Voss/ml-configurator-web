<template>
    <div>
        <div class="row mt-3 mb-3">
            <div class="col-8">
                <div class="col-12">
                    Initialisiert am: {{$d(data.creationDatetime, 'long')}}
                </div>
                <div class="col-12">
                    Ausgeführt von: {{$d(data.startDatetime, 'long')}} bis {{$d(data.endDatetime, 'long')}}
                </div>
            </div>
            <div class="col-4">

            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <div class="h2">
                    Ergebnis
                </div>
            </div>
        </div>
        <div class="row mb-3" v-if="!data.result.isText">
            <div class="col-12">
                <span class="fw-bold">Trainingsdauer </span>
                <span>{{roundedDuration}} Sekunden</span>
            </div>
            <div class="col-12">
                <span class="fw-bold">Mean Squared Error für Validierungsdaten </span>
                <span>{{roundMse(data.result.mse_val)}}</span><span v-if="bestTrainingId === data.id"> - Best Result</span>
            </div>
            <div class="col-12" v-if="data.result.mse_test">
                <span class="fw-bold">Mean Squared Error für Testdaten </span>
                <span>{{roundMse(data.result.mse_test)}}</span>
            </div>
        </div>
        <div class="row mb-3" v-if="data.result.isText">
            <div class="col-12">
                <span class="fw-bold">Trainingsdauer </span>
                <span>{{roundedDuration}} Sekunden</span>
            </div>
            <div class="col-12">
                <span class="fw-bold">Accuracy für Validierungsdaten </span>
                <span>{{data.result.accuracy_val}}</span><span v-if="bestTrainingId === data.id"> - Best Result</span>
            </div>
            <div class="col-12" v-if="data.result.accuracy_test">
                <span class="fw-bold">Accuracy für Testdaten </span>
                <span>{{data.result.accuracy_test}}</span>
            </div>
        </div>
        <div class="row mb-3 g-1" v-if="!data.result.isText">
            <div class="col-12">
                <h3>Feature Importance</h3>
            </div>
            <div class="col-auto" v-for="(val, index) in data.result.feature_importance" :key="index">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            {{index}}
                        </div>
                        <div class="card-text">
                            {{roundImportance(val)}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-if="!data.result.isText">
            <div class="col-12">
                <h3>Visualisierung gegen Validierungsdaten</h3>
            </div>
            <div class="col-12 col-lg-6 imgcontainer" v-if="data.result.scatterplot_val !== ''">
                <img :src="plot(data.result.scatterplot_val)" />
            </div>
            <div class="col-12 col-lg-6 imgcontainer" v-if="data.result.residuals_val !== ''">
                <img :src="plot(data.result.residuals_val)" />
            </div>
        </div>
        <div class="row" v-if="data.result.mse_test">
            <div class="col-12">
                <h3>Visualisierung gegen Testdaten</h3>
            </div>
            <div class="col-12 col-lg-6 imgcontainer" v-if="data.result.scatterplot_test !== ''">
                <img :src="plot(data.result.scatterplot_test)" />
            </div>
            <div class="col-12 col-lg-6 imgcontainer" v-if="data.result.residuals_test !== ''">
                <img :src="plot(data.result.residuals_test)" />
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: "SvmResult",
    props: {
        data: Object,
        bestTrainingId: Number
    },
    computed: {
        displayResidualplot() {
            return "data:image/png;base64," + this.data.result.residuals
        },
        displayScatterplot() {
            return "data:image/png;base64," + this.data.result.scatterplot
        },
        roundedDuration() {
            return Math.trunc(Math.round(this.data.result.duration * 100)) / 100
        },
        roundedMse() {
            return Math.trunc(Math.round(this.data.result.mse * 100)) / 100
        }
    },
    methods: {
        roundImportance(val) {
            return Math.trunc(Math.round(val * 1000)) / 1000
        },
        roundMse(val) {
            return Math.trunc(Math.round(val * 100)) / 100
        },
        plot(data) {
            return "data:image/png;base64," + data
        },
    }
}

</script>