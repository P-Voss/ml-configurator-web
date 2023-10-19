<template>
    <form @submit.prevent="handleSubmit" class="gx-4">
        <div class="row mb-3">

            <div class="col-12 col-lg-6">
                <label for="regularizerType" class="form-label">Regularizer Type:</label>
                <div class="input-group">
                    <select class="form-select" id="regularizerType" v-model="params.regularizerType">
                        <option value="l1">L1</option>
                        <option value="l2">L2</option>
                        <option value="none">None</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('regularizerType')">?</button>
                </div>
                <div v-if="showingInfo === 'regularizerType'" class="form-text">
                    Der Regularizer Typ bestimmt die Art der Regularisierung, die auf die Kostenfunktion angewendet wird.
                    L1-Regularisierung fügt einen Strafterm hinzu, der proportional zur absoluten Größe der Gewichte ist.
                    L2-Regularisierung fügt einen Strafterm hinzu, der proportional zum Quadrat der Gewichte ist.
                    "None" bedeutet, dass keine Regularisierung angewendet wird.
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <label for="solver" class="form-label">Solver:</label>
                <div class="input-group">
                    <select class="form-select" id="solver" v-model="params.solver">
                        <option value="newton-cg">Newton-CG</option>
                        <option value="lbfgs">L-BFGS</option>
                        <option value="liblinear">Liblinear</option>
                        <option value="sag">SAG</option>
                        <option value="saga">SAGA</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('solver')">?</button>
                </div>
                <div v-if="showingInfo === 'solver'" class="form-text">
                    Der Solver bestimmt die Optimierungsalgorithmen, die zur Lösung des Optimierungsproblems verwendet werden.
                    Die Auswahl des Solvers kann sich auf die Konvergenzgeschwindigkeit und die Leistung der logistischen Regression auswirken.
                    <p v-if="params.solver === 'newton-cg'">
                        <span class="fw-bold">Newton-CG: </span>Ein numerischer Optimierungsalgorithmus, der auf der Berechnung der exakten
                        Hesse-Matrix basiert. Er ist für kleine Datensätze geeignet, aber möglicherweise nicht für große Datensätze
                        aufgrund der Berechnungskomplexität der Hesse-Matrix.
                    </p>
                    <p v-if="params.solver === 'lbfgs'">
                        <span class="fw-bold">L-BFGS: </span>Ein quasi-newtonsches Optimierungsverfahren, das auf einer
                        begrenzten Speicherimplementierung des BFGS-Verfahrens basiert. Es ist in der Regel effizienter als der
                        Newton-CG-Solver für große Datensätze.
                    </p>
                    <p v-if="params.solver === 'liblinear'">
                        <span class="fw-bold">Liblinear: </span>Eine der populärsten Bibliotheken für lineare Modelle, die lineare
                        SVMs und logistische Regression für große Datensätze effizient unterstützt. Es verwendet eine verbesserte
                        Version des Coordinate Descent-Verfahrens.
                    </p>
                    <p v-if="params.solver === 'sag'">
                        <span class="fw-bold">SAG: </span>Stochastic Average Gradient Descent ist eine Variante des Gradientenabstiegs,
                        die für große Datensätze geeignet ist. Sie arbeitet schneller als normale Gradientenabstiegsverfahren, indem
                        sie den Durchschnitt der Gradienten über mehrere zufällige Batches berechnet.
                    </p>
                    <p v-if="params.solver === 'saga'">
                        <span class="fw-bold">SAGA: </span>Eine Verbesserung des SAG-Verfahrens, das eine zusätzliche Korrektur für die
                        Richtung des Gradienten einführt, um die Konvergenzgeschwindigkeit zu erhöhen. Es ist gut für konvexe Probleme
                        und hat eine schnelle Konvergenz.
                    </p>
                </div>
            </div>

        </div>

        <div class="row mb-3">

            <div class="col-12 col-lg-6">
                <label for="lambda" class="form-label">Lambda:</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="lambda" v-model="params.lambda" />
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('lambda')">?</button>
                </div>
                <div v-if="showingInfo === 'lambda'" class="form-text">
                    Lambda ist ein Hyperparameter, der nur für Modelle mit Regularisierung relevant ist. Er kontrolliert die Stärke der Regularisierung.
                    Ein höherer Wert von Lambda kann dazu führen, dass die Koeffizienten näher oder gleich Null werden, was zu einer stärkeren Regularisierung führt.
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
    name: "LogisticRegressionConfiguration",
    props: {
        configuration: Object,
    },
    data() {
        return {
            params: {
                regularizerType: 'none',
                solver: 'liblinear',
                lambda: 1.0
            },
            showingInfo: null
        }
    },
    mounted() {
        if (this.configuration.regularizerType) {
            this.params.regularizerType = this.configuration.regularizerType
        }
        if (this.configuration.solver) {
            this.params.solver = this.configuration.solver
        }
        if (this.configuration.lambda) {
            this.params.lambda = this.configuration.lambda
        }
    },
    methods: {
        handleSubmit() {
            this.$emit("save-configuration", this.params);
        },
        toggleInfo(parameter) {
            this.showingInfo = this.showingInfo === parameter ? null : parameter
        }
    }
}
</script>