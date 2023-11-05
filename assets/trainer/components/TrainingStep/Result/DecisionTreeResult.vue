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
                    <div class="row my-3">
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
                        data-bs-target="#result"
                        aria-expanded="true"
                        aria-controls="result"
                    >
                        Ergebnis
                    </button>
                </h2>
                <div
                    id="result"
                    class="accordion-collapse collapse show"
                >
                    <div class="row my-3">
                        <div class="col-12 col-lg-6">
                            <span class="fw-bold">Trainingsdauer </span>
                            <span>{{roundedDuration}} Sekunden</span>
                        </div>
                        <div class="col-12 col-lg-6">
                            <span class="fw-bold">Genauigkeit </span>
                            <span>{{data.result.accuracy}}</span><span v-if="bestTrainingId === data.id"> - Best Result</span>
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
                        data-bs-target="#report"
                        aria-expanded="true"
                        aria-controls="report"
                    >
                        Klassifizierungs-Report
                    </button>
                </h2>
                <div
                    id="report"
                    class="accordion-collapse collapse"
                >
                    <div class="row">
                        <div class="col-6" v-for="(entry, key) in data.result.classification_report" :key="key">
                            <div class="card" v-if="['accuracy', 'macro avg', 'weighted avg'].indexOf(key) === -1">
                                <div class="card-body pb-0">
                                    <div class="card-title">
                                        Klasse: {{key}}
                                    </div>
                                    <div class="card-text">
                                        <pre>{{entry}}</pre>
                                    </div>
                                </div>
                            </div>
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
                        data-bs-target="#visual"
                        aria-expanded="true"
                        aria-controls="visual"
                    >
                        Visualisierung des Decision Trees
                    </button>
                </h2>
                <div
                    id="visual"
                    class="accordion-collapse collapse"
                >
                    <div class="row">
                        <div class="col-12 imgcontainer" v-if="data.result.tree_plot !== ''">
                            <img :src="displayTreeplot" />
                            <p class="fw-bold">Erklärung des Tree Plots</p>
                            <p class="text-muted">Beispiel: Jahreszeit anhand der Monate kategorisieren</p>
                            <p>
                                Die oberste Box (Wurzel) enthält das Merkmal, das die Daten am besten teilt.
                                Jede Box (Knoten) unterteilt die Daten weiter basierend auf einem Merkmal, wie zum Beispiel "Monat &lt; 6", was bedeutet, dass es sich um die Monate vor Juni handelt.
                            </p>
                            <p>
                                Die Linien (Äste) zu den Boxen zeigen die Antworten (Ja oder Nein). Je weiter unten ein Knoten, desto spezifischer die Frage. Die letzte Ebene der Boxen (Blätter) zeigt die vorhergesagte Kategorie, etwa die Jahreszeit.
                            </p>
                            <p>
                                Die Farbe in den Boxen deutet auf die vorherrschende Kategorie hin, und die Schattierung zeigt die Reinheit – eine einheitliche Farbe bedeutet, dass alle Datenpunkte in dieser Gruppe zur gleichen Kategorie gehören.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: "DecisionTreeResult",
    props: {
        data: Object,
        bestTrainingId: Number
    },
    computed: {
        displayTreeplot() {
            return "data:image/png;base64," + this.data.result.tree_plot
        },
        roundedDuration() {
            return Math.trunc(Math.round(this.data.result.duration * 100)) / 100
        },
    }
}

</script>