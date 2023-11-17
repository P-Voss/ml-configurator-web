<template>
    <div>
        <div class="row mt-3 mb-3">
            <div class="col-12 col-lg-6">
                <div class="row gy-4">
                    <div class="col-12">
                        Initialisiert am: {{$d(data.creationDatetime, 'long')}}
                    </div>
                    <div class="col-12">
                        Ausgeführt: {{$d(data.startDatetime, 'long')}} - {{$d(data.endDatetime, 'long')}}
                    </div>
                    <div class="col-12">
                        <span class="fw-bold">Trainingsdauer </span>
                        <span>{{roundedDuration}} Sekunden</span>
                    </div>
                    <div class="col-12">
                        <span class="fw-bold">Absolvierte Epochen </span>
                        <span>{{data.result.epochs_completed}}</span>
                    </div>
                    <div class="col-12">
                        <span class="fw-bold">Early-Stop Epoche </span>
                        <span v-if="data.result.stopped_epoch > 0">{{data.result.stopped_epoch}}</span><span v-if="data.result.stopped_epoch === 0">kein early stop</span>
                    </div>
                    <div class="col-12">
                        <span class="fw-bold">Niedrigster Verlustwert </span>
                        <span>{{roundLoss(data.result.best_val_loss)}}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 imgcontainer" v-if="data.result.learning_curves !== ''">
                <img :src="plot(data.result.learning_curves)" />
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <div class="h2">
                    Modellperformance gegen Validierungsdaten
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="row gy-4">
                    <div class="col-12">
                        <span class="fw-bold">Verlustfunktion </span>
                        <span>{{roundLoss(data.result.loss)}}</span><span v-if="bestTrainingId === data.id"> - Best Result</span>
                    </div>
                    <div class="col-12">
                        <span class="fw-bold">Bestimmtheitsmaß </span>
                        <span>{{roundR2(data.result.r2_score_val)}}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8 imgcontainer" v-if="data.result.scatterplot_val !== ''">
                <img :src="plot(data.result.scatterplot_val)" />
            </div>
        </div>


        <div class="row mb-3" v-if="data.result.r2_score_test > 0">
            <div class="col-12">
                <div class="h2">
                    Modellperformance gegen Testdaten
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="row gy-4">
                    <div class="col-12">
                        <span class="fw-bold">Bestimmtheitsmaß </span>
                        {{roundR2(data.result.r2_score_test)}}
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8 imgcontainer" v-if="data.result.scatterplot_test !== ''">
                <img :src="plot(data.result.scatterplot_test)" />
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
        displayScatterplotVal() {
            return "data:image/png;base64," + this.data.result.scatterplot_val
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
    },
    methods: {
        roundLoss(val) {
            return Math.trunc(Math.round(val * 10000)) / 10000
        },
        roundR2(val) {
            return Math.trunc(Math.round(val * 10000)) / 10000
        },
        plot(data) {
            return "data:image/png;base64," + data
        }
    }
}

</script>