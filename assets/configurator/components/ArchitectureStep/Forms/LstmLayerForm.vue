<template>
    <form @submit.prevent="handleSubmit">

        <!-- Einheiten/Neuronen -->
        <div class="mb-3">
            <label for="units" class="form-label">Einheiten (Neuronen):</label>
            <div class="input-group">
                <input type="number" class="form-control" id="units" v-model="layer.neurons" />
                <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('units')">?</button>
            </div>
            <div v-if="showingInfo === 'units'" class="form-text">
                <p>
                    In LSTM-Layern bezieht sich der Begriff 'Einheiten' auf die Anzahl der LSTM-Zellen in der Schicht.
                    Jede Einheit enthält den Mechanismus des LSTM, einschließlich der verschiedenen Gates und des internen Zellzustands.
                    Sie können sich eine Einheit grob als ein 'erweitertes Neuron' vorstellen, das speziell für sequenzielle Daten entwickelt wurde.
                </p>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-check-label">
                <input type="checkbox" class="form-check-input" v-model="layer.returnSequences"> Rückgabesequenzen
            </label>
            <button type="button" class="btn btn-outline-secondary ms-2" @click="toggleInfo('returnSequences')">?</button>
            <div v-if="showingInfo === 'returnSequences'" class="form-text">
                Aktivieren Sie diese Option, wenn Sie die gesamte Sequenz der Ausgaben anstelle der letzten Ausgabe zurückgeben möchten.
                Dies ist besonders nützlich, wenn Sie eine zeitliche Abfolge an einen anderen LSTM- oder rekurrenten Layer weitergeben möchten.
                Wenn Sie beispielsweise ein Modell für die Satzgenerierung oder die Vorhersage von Zeitreihen mit mehreren zukünftigen Punkten erstellen, benötigen Sie die vollständige Sequenz.
            </div>
        </div>

        <div class="mb-3">
            <label for="activation" class="form-label">Aktivierungsfunktion:</label>
            <div class="input-group">
                <select class="form-select" id="activation" v-model="layer.activationFunction">
                    <option value="tanh">Tanh</option>
                    <option value="sigmoid">Sigmoid</option>
                    <option value="relu">ReLU</option>
                </select>
                <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('activation')">?</button>
            </div>
            <div v-if="showingInfo === 'activation'" class="form-text">
                Definiert, wie die Ausgabe eines Neurons berechnet wird. Typischerweise wird Tanh für LSTM-Aktivierungen verwendet.
                ReLU kann in manchen Fällen funktionieren, birgt aber das Risiko des Gradienten-Explodierens.
                <p>
                    <span class="fw-bold">ReLu: </span>Beliebt für Zwischenschichten, da es schnelles Training ermöglicht
                    und nicht saturiert. Es setzt alle negativen Werte auf null und behält positive Werte bei.
                    Aber Vorsicht vor "toten" Neuronen, die nie aktiviert werden.
                </p>
                <p>
                    <span class="fw-bold">Sigmoid: </span>Begrenzt Ausgaben zwischen 0 und 1. Sie eignet sich besonders
                    gut für Ausgabeschichten von binären Klassifikationsproblemen. Wegen ihrer sättigenden Natur und der
                    Gefahr des verschwindenden Gradienten nicht für tiefe Netzwerke empfohlen.
                </p>
                <p>
                    <span class="fw-bold">Tanh: </span>Wie Sigmoid, aber mit Ausgaben zwischen -1 und 1. Es zentriert
                    die Daten, was oft vorteilhaft ist, aber es kann auch saturieren.
                </p>
            </div>
        </div>

        <div class="mb-3">
            <label for="regularization" class="form-label">Regularisierung:</label>
            <div class="input-group">
                <select class="form-select" id="regularization" v-model="layer.regularizationType">
                    <option value="none">Keine</option>
                    <option value="l1">L1</option>
                    <option value="l2">L2</option>
                </select>
                <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('regularization')">?</button>
            </div>
            <div v-if="showingInfo === 'regularization'" class="form-text">
                Reduziert Overfitting durch Bestrafen großer Gewichtswerte.
                <p>
                    <span class="fw-bold">L1: </span>Bestraft die absolute Größe der Gewichte; kann zu spärlichen Gewichtsmatrizen führen.
                </p>
                <p>
                    <span class="fw-bold">L2: </span>Bestraft das Quadrat der Gewichtsgrößen; gebräuchlicher als L1.
                </p>
            </div>
        </div>

        <div class="mb-3" v-if="layer.regularizationType !== 'none'">
            <label for="lambda" class="form-label">Lambda:</label>
            <input type="number" class="form-control mt-2" id="lambda" v-model="layer.lambda" placeholder="Lambda-Wert" max="1" min="0" step="0.001" />

            <div v-if="showingInfo === 'regularization'" class="form-text">
                <p>
                    <span class="fw-bold">Lambda-Wert: </span>
                    Der Lambda-Wert steuert die Stärke der Regularisierung. Ein höherer Wert führt zu
                    stärkerer Regularisierung, wodurch das Modell vorsichtiger wird und große Gewichtswerte vermeidet.
                    Dies kann Overfitting reduzieren, aber ein zu hoher Wert kann zu Underfitting führen. Beginnen
                    Sie mit einem kleinen Wert (z.B. 0,001) und justieren Sie bei Bedarf.
                </p>
            </div>
        </div>

        <!-- Dropout -->
        <div class="mb-3">
            <label for="dropout" class="form-label">Dropout-Rate:</label>
            <div class="input-group">
                <input type="number" class="form-control" id="dropout" v-model="layer.dropoutRate" placeholder="0.0 - 1.0" min="0" max="1" step="0.01"/>
                <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('dropout')">?</button>
            </div>
            <div v-if="showingInfo === 'dropout'" class="form-text">
                Ein Mechanismus gegen Overfitting. Ein bestimmter Prozentsatz der Eingabe-Einheiten wird zufällig in jedem Trainingsschritt "ausgeschaltet".
            </div>
        </div>

        <!-- Rekurrenter Dropout -->
        <div class="mb-3">
            <label for="recurrentDropout" class="form-label">Rekurrenter Dropout:</label>
            <div class="input-group">
                <input type="number" class="form-control" id="recurrentDropout" v-model="layer.recurrentDropoutRate" placeholder="0.0 - 1.0" min="0" max="1" step="0.01"/>
                <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('recurrentDropout')">?</button>
            </div>
            <div v-if="showingInfo === 'recurrentDropout'" class="form-text">
                Während "Dropout" die Eingabeverbindungen zu den LSTM-Einheiten beeinflusst, betrifft "rekurrenter Dropout" die
                rekurrenten Verbindungen innerhalb der Einheiten. Dies kann dabei helfen, Overfitting in zeitlichen Sequenzen zu verhindern.
            </div>
        </div>

        <!-- Button zum Hinzufügen -->
        <button type="submit" class="btn btn-primary">LSTM-Schicht hinzufügen</button>

    </form>
</template>

<script>
export default {
    name: 'LstmLayerForm',
    data() {
        return {
            layer: {
                type: 'LAYER_TYPE_LSTM',
                neurons: 50,
                returnSequences: false,
                activationFunction: 'tanh',
                regularizationType: "none",
                lambda: 0,
                dropoutRate: 0,
                recurrentDropoutRate: 0
            },
            showingInfo: null
        };
    },
    methods: {
        handleSubmit() {
            let params = {
                type: this.layer.type,
                neurons: this.layer.neurons,
                returnSequences: this.layer.returnSequences,
                activationFunction: this.layer.activationFunction,
                regularizationType: this.layer.regularizationType,
                lambda: this.layer.lambda,
                dropoutRate: this.layer.dropoutRate,
                recurrentDropoutRate: this.layer.recurrentDropoutRate,
            }

            this.$emit("add-layer", params);
        },
        toggleInfo(parameter) {
            this.showingInfo = this.showingInfo === parameter ? null : parameter;
        }
    }
}
</script>