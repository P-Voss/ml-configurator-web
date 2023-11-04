<template>
    <div>
        <div class="accordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button
                        class="accordion-button"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#taskData"
                        aria-expanded="true"
                        aria-controls="taskData"
                    >
                        Trainingsdurchlauf
                    </button>
                </h2>
                <div
                    id="taskData"
                    class="accordion-collapse collapse show"
                >
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
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button
                        class="accordion-button"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#resultData"
                        aria-expanded="true"
                        aria-controls="resultData"
                    >
                        Ergebnis
                    </button>
                </h2>
                <div
                    id="resultData"
                    class="accordion-collapse collapse"
                >
                    <div class="row mb-3">
                        <div class="col-12 col-lg-6">
                            <span class="fw-bold">Trainingsdauer </span>
                            <span>{{roundedDuration}} Sekunden</span>
                        </div>
                        <div class="col-12 col-lg-6">
                            <span class="fw-bold">Mean Squared Error </span>
                            <span>{{roundedMse}}</span><span v-if="bestTrainingId === data.id"> - Best Result</span>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 imgcontainer" v-if="data.result.scatterplot !== ''">
                            <img :src="displayScatterplot" />
                        </div>
                        <div class="col-12 col-lg-6 imgcontainer" v-if="data.result.residuals !== ''">
                            <img :src="displayResidualplot" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button
                        class="accordion-button"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#featureImportance"
                        aria-expanded="true"
                        aria-controls="featureImportance"
                    >
                        Feature-Importance
                    </button>
                </h2>
                <div
                    id="featureImportance"
                    class="accordion-collapse collapse show"
                >
                    <div class="row">
                        <div class="col-6" v-for="feature in data.result.feature_importance" :key="feature">
                            <span class="fw-bold"></span>
                        </div>
                        <div class="col-8">
                            <div class="col-12">
                                Initialisiert am: {{$d(data.creationDatetime, 'long')}}
                            </div>
                            <div class="col-12">
                                Ausgeführt von: {{$d(data.startDatetime, 'long')}} bis {{$d(data.endDatetime, 'long')}}
                            </div>
                        </div>
                    </div>
                </div>
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
    }
}

</script>