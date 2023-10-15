<template>
    <form @submit.prevent="handleSubmit">
        <div class="mb-3">
            <label for="neurons" class="form-label">Neuronenzahl:</label>
            <div class="input-group">
                <input type="number" class="form-control" id="neurons" v-model="layer.neurons" />
                <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('neurons')">?</button>
            </div>
            <div v-if="showingInfo === 'neurons'" class="form-text">
                Bestimmt die Anzahl der Neuronen in dieser Schicht. Mehr Neuronen erlauben komplexere Modelle, können aber auch zu Overfitting führen.
            </div>
        </div>

        <div class="mb-3">
            <label for="activation" class="form-label">Aktivierungsfunktion:</label>
            <div class="input-group">
                <select class="form-select" id="activation" v-model="layer.activationFunction">
                    <option value="relu">ReLu</option>
                    <option value="sigmoid">Sigmoid</option>
                    <option value="tanh">Tanh</option>
                </select>
                <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('activation')">?</button>
            </div>
            <div v-if="showingInfo === 'activation'" class="form-text">
                Definiert, wie die Ausgabe eines Neurons berechnet wird.
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

        <div class="mb-3">
            <label for="dropout" class="form-label">Dropout-Rate:</label>
            <div class="input-group">
                <input type="number" class="form-control" id="dropout" v-model="layer.dropoutRate" placeholder="0.0 - 1.0" min="0" max="1" step="0.01"/>
                <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('dropout')">?</button>
            </div>
            <div v-if="showingInfo === 'dropout'" class="form-text">
                Ein Mechanismus gegen Overfitting. Ein bestimmter Prozentsatz der Neuronen wird zufällig in jedem Trainingsschritt "ausgeschaltet".
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Schicht hinzufügen</button>
    </form>
</template>

<script>

export default {
    name: 'DenseLayerForm',
    props: {

    },
    data() {
        return {
            showingInfo: '',
            layer: {
                type: 'LAYER_TYPE_DENSE',
                neurons: 0,
                activationFunction: "relu",
                regularizationType: "none",
                lambda: 0,
                dropoutRate: 0
            }
        };
    },
    methods: {
        handleSubmit() {
            let params = {
                type: this.layer.type,
                neurons: this.layer.neurons,
                returnSequences: false,
                activationFunction: this.layer.activationFunction,
                regularizationType: this.layer.regularizationType,
                lambda: this.layer.lambda,
                dropoutRate: this.layer.dropoutRate,
                recurrentDropoutRate: 0,
            }

            this.$emit("add-layer", params);
        },
        toggleInfo(param) {
            if (this.showingInfo === param) {
                this.showingInfo = '';  // Toggle-Verhalten
            } else {
                this.showingInfo = param;
            }
        },
        resetForm() {
            this.layer = {
                neurons: 0,
                activationFunction: "relu",
                initialization: "glorot",
                regularizationType: "none",
                lambda: 0,
                dropoutRate: 0
            };
        }
    }
}

</script>
