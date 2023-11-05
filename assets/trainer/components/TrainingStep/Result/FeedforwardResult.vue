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
            <div class="col-12 col-lg-4">
                <span class="fw-bold">Trainingsdauer </span>
                <span>{{roundedDuration}} Sekunden</span>
            </div>
            <div class="col-12 col-lg-4">
                <span class="fw-bold">Verlustfunktion </span>
                <span>{{roundedLoss}}</span><span v-if="bestTrainingId === data.id"> - Best Result</span>
            </div>
            <div class="col-12 col-lg-4">
                <span class="fw-bold">Bestimmtheitsmaß </span>
                <span>{{roundedR2}}</span>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12 col-lg-4">
                <span class="fw-bold">Absolviere Epochen </span>
                <span>{{data.result.epochs_completed}}</span>
            </div>
            <div class="col-12 col-lg-4">
                <span class="fw-bold">Early-Stop Epoche </span>
                <span v-if="data.result.stopped_epoch > 0">{{data.result.stopped_epoch}}</span><span v-if="data.result.stopped_epoch === 0">kein early stop</span>
            </div>
            <div class="col-12 col-lg-4">
                <span class="fw-bold">Niedrigster Verlustwert </span>
                <span>{{roundedBestLoss}}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-6 imgcontainer" v-if="data.result.scatterplot !== ''">
                <img :src="displayScatterplot" />
            </div>
            <div class="col-12 col-lg-6 imgcontainer" v-if="data.result.learning_curves !== ''">
                <img :src="displayLearningcurves" />
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: "FeedforwardResult",
    props: {
        data: Object,
        bestTrainingId: Number
    },
    computed: {
        displayLearningcurves() {
            return "data:image/png;base64," + this.data.result.learning_curves
        },
        displayScatterplot() {
            return "data:image/png;base64," + this.data.result.scatterplot
        },
        roundedDuration() {
            return Math.trunc(Math.round(this.data.result.duration * 100)) / 100
        },
        roundedLoss() {
            return Math.trunc(Math.round(this.data.result.loss * 10000)) / 10000
        },
        roundedR2() {
            return Math.trunc(Math.round(this.data.result.r2_score * 10000)) / 10000
        },
        roundedBestLoss() {
            return Math.trunc(Math.round(this.data.result.best_val_loss * 10000)) / 10000
        },
    }
}

</script>