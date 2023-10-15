<template>
    <form @submit.prevent="handleSubmit" class="gx-4">
        <div class="row mb-3">
            <div class="col-12 col-lg-6">
                <label for="maxDepth" class="form-label">Maximale Tiefe:</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="maxDepth" v-model="params.maxDepth" />
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('maxDepth')">?</button>
                </div>
                <div v-if="showingInfo === 'maxDepth'" class="form-text">
                    Die maximale Tiefe des Baumes. Ein tieferer Baum wird genauer sein, aber das Risiko des Overfittings steigt.
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <label for="maxFeatures" class="form-label">Maximale Merkmale:</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="maxFeatures" v-model="params.maxFeatures" />
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('maxFeatures')">?</button>
                </div>
                <div v-if="showingInfo === 'maxFeatures'" class="form-text">
                    Die maximale Anzahl von Merkmalen, die für das beste Teilen gesucht werden. Ein kleinerer Wert kann den Algorithmus schneller machen.
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12 col-lg-6">
                <label for="minSampleSplit" class="form-label">Minimale Proben zum Teilen:</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="minSampleSplit" v-model="params.minSampleSplit" />
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('minSampleSplit')">?</button>
                </div>
                <div v-if="showingInfo === 'minSampleSplit'" class="form-text">
                    Dieser Parameter gibt die minimale Anzahl von Datensätzen an, die benötigt wird, um
                    einen Knoten im Baum zu teilen. Wenn die Anzahl der Datensätze in einem Knoten unter diesem Wert liegt,
                    wird der Knoten nicht weiter geteilt. Dies hilft, kleine und feinverzweigte Bäume zu verhindern,
                    die zu Überanpassung führen können.
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <label for="minSamplesLeaf" class="form-label">Minimale Proben pro Blatt:</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="minSamplesLeaf" v-model="params.minSamplesLeaf" />
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('minSamplesLeaf')">?</button>
                </div>
                <div v-if="showingInfo === 'minSamplesLeaf'" class="form-text">
                    Die minimale Anzahl von Datensätzen, die in einem Blattknoten (Endknoten) des Baums enthalten sein müssen.
                    Das Erhöhen dieser Zahl kann das Modell stabiler und weniger anfällig für Rauschen in den Daten machen.
                    Allerdings kann ein zu hoher Wert die Fähigkeit des Modells einschränken, feinere Muster zu erkennen.
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12 col-lg-6">
                <label for="missingValueHandling" class="form-label">Behandlung von fehlenden Werten:</label>
                <div class="input-group">
                    <select class="form-select" id="missingValueHandling" v-model="params.missingValueHandling">
                        <option value="mean">Mittelwert</option>
                        <option value="median">Median</option>
                        <option value="drop">Verwerfen</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('missingValueHandling')">?</button>
                </div>
                <div v-if="showingInfo === 'missingValueHandling'" class="form-text">
                    Bestimmt, wie mit fehlenden Werten umgegangen wird. Sie können entweder durch den Mittelwert, den Median ersetzt oder komplett verworfen werden.
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <label for="qualityMeasure" class="form-label">Qualitätsmaß:</label>
                <div class="input-group">
                    <select class="form-select" id="qualityMeasure" v-model="params.qualityMeasure">
                        <option value="gini">Gini-Unreinheit</option>
                        <option value="entropy">Entropie</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('qualityMeasure')">?</button>
                </div>
                <div v-if="showingInfo === 'qualityMeasure'" class="form-text">
                    Das Maß, das verwendet wird, um die Qualität eines Splits zu bewerten. Gini-Unreinheit und Entropie sind die gängigsten Methoden.
                    <p>
                        <span class="fw-bold">Gini: </span>Die Gini-Unreinheit misst, wie
                        oft ein zufällig ausgewähltes Element fälschlicherweise klassifiziert werden würde,
                        wenn es zufällig nach der Verteilung der Labels in einem Knoten klassifiziert wird.
                        Eine Gini-Unreinheit von 0 bedeutet, dass alle Elemente im Knoten zu einer einzelnen Klasse gehören,
                        d.h. der Knoten ist "rein". Ein höherer Wert für die Gini-Unreinheit zeigt eine größere Vermischung
                        von Klassen in einem Knoten an. Bei der Entscheidungsbaum-Bildung versucht man, Attribute zu finden,
                        die die Gini-Unreinheit minimieren.
                    </p>
                    <p>
                        <span class="fw-bold">Entropie: </span>Entropie ist ein Maß für die Unordnung oder den Informationsgehalt.
                        In Bezug auf Entscheidungsbäume misst die Entropie, wie gemischt die Daten in Bezug auf das Label in
                        einem Knoten sind. Ein Knoten mit einer Entropie von 0 ist völlig "rein", d.h. alle Datenpunkte in
                        diesem Knoten gehören zu einer Klasse. Ein Knoten mit einer höheren Entropie enthält eine Mischung von Klassen.
                        Bei der Entscheidungsbaum-Bildung wird versucht, Attribute zu finden, die die Entropie minimieren,
                        wodurch der Informationsgewinn maximiert wird.
                    </p>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Speichern</button>
            </div>
        </div>
    </form>
</template>

<script>

export default {
    name: "DtreeArchitecture",
    props: {
        architecture: Object,
    },
    data() {
        return {
            params: {
                maxDepth: 1,
                minSampleSplit: 2,
                maxFeatures: 1,
                minSamplesLeaf: 1,
                missingValueHandling: 'mean',
                qualityMeasure: 'gini'
            },
            showingInfo: null
        };
    },
    mounted() {
        if (this.architecture.maxDepth) {
            this.params.maxDepth = this.architecture.maxDepth
        }
        if (this.architecture.minSampleSplit) {
            this.params.minSampleSplit = this.architecture.minSampleSplit
        }
        if (this.architecture.maxFeatures) {
            this.params.maxFeatures = this.architecture.maxFeatures
        }
        if (this.architecture.minSamplesLeaf) {
            this.params.minSamplesLeaf = this.architecture.minSamplesLeaf
        }
        if (this.architecture.missingValueHandling) {
            this.params.missingValueHandling = this.architecture.missingValueHandling
        }
        if (this.architecture.qualityMeasure) {
            this.params.qualityMeasure = this.architecture.qualityMeasure
        }
    },
    methods: {
        handleSubmit() {
            this.$emit("save-configuration", this.params);
        },
        toggleInfo(parameter) {
            this.showingInfo = this.showingInfo === parameter ? null : parameter;
        }
    }
}

</script>