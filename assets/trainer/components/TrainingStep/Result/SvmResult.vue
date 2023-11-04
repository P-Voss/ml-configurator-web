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