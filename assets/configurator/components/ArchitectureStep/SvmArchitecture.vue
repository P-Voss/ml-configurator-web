<template>
    <form @submit.prevent="handleSubmit" class="gx-4">
        <div class="row mb-3">

            <div class="col-12 col-lg-6">
                <label for="kernel" class="form-label">Kernel:</label>
                <div class="input-group">
                    <select class="form-select" id="kernel" v-model="params.kernel">
                        <option value="linear">Linear</option>
                        <option value="poly">Polynomial</option>
                        <option value="rbf">RBF (Radial Basis Function)</option>
                        <option value="sigmoid">Sigmoid</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('kernel')">?</button>
                </div>
                <div v-if="showingInfo === 'kernel'" class="form-text">
                    Der Kernel bestimmt den Typ der Hyperebene, die zur Trennung der Daten verwendet wird.
                    Unterschiedliche Kernel können verschiedene Formen von Trennungen erzeugen.
                    <p>
                        <span class="fw-bold">Linear: </span>Dieser Kernel erstellt eine lineare Trennlinie.
                        Er ist ideal für Daten, bei denen die Klassen durch eine gerade Linie oder Ebene getrennt werden können.
                    </p>
                    <p>
                        <span class="fw-bold">Polynomial: </span>Dieser Kernel erstellt eine polynomiale Trennlinie,
                        was nützlich ist, wenn die Daten in einer polynomialen Form getrennt werden müssen.
                    </p>
                    <p>
                        <span class="fw-bold">RBF (Radial Basis Function): </span>Dies ist ein häufig verwendeter Kernel,
                        der es ermöglicht, eine nichtlineare Trennung in der Form einer Glockenkurve zu erstellen.
                        Er ist besonders nützlich, wenn die Trennung der Daten nicht einfach ist.
                    </p>
                    <p>
                        <span class="fw-bold">Sigmoid: </span>Dieser Kernel erzeugt eine trennende Hyperplane in der Form einer Sigmoid-Funktion.
                    </p>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <label for="c" class="form-label">C (Regularisierung):</label>
                <div class="input-group">
                    <input type="range" class="form-range me-2" style="max-width: 90%;" min="0" step="0.01" max="1000" id="c" v-model="params.c" />
                    <button type="button" class="btn btn-outline-secondary rounded-circle" @click="toggleInfo('c')">?</button>
                    <span class="ms-3" style="margin-top: -5px">{{ params.c }}</span>
                </div>
                <div v-if="showingInfo === 'c'" class="form-text">
                    Der Regularisierungsparameter C ist ein Trade-off zwischen der Erzielung eines möglichst
                    großen Abstands zwischen der trennenden Hyperplane und den Datenpunkten und der Minimierung
                    der Klassifikationsfehler. Ein kleiner Wert von C versucht, die Margin zwischen den Datenpunkten
                    und der trennenden Linie so groß wie möglich zu halten, was zu einer glatteren Entscheidungsgrenze
                    führen kann, während es einige Misclassifikationen zulässt. Ein hoher Wert von C legt mehr Wert
                    darauf, alle Datenpunkte korrekt zu klassifizieren, kann jedoch zu Overfitting führen, da
                    die Entscheidungsgrenze zu stark an die Trainingsdaten angepasst wird.
                </div>
            </div>
        </div>

        <div class="row mb-3">

            <div class="col-12 col-lg-6">
                <label for="degree" class="form-label">Degree (für Polynomial Kernel):</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="degree" v-model="params.degree" :disabled="params.kernel !== 'poly'" />
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('degree')">?</button>
                </div>
                <div v-if="showingInfo === 'degree'" class="form-text">
                    Der Grad des Polynomial-Kernels bestimmt die Art der polynomialen Trennung zwischen den Daten.
                    Ein höherer Grad führt zu komplexeren Trennungen. Es ist wichtig zu beachten, dass ein zu hoher
                    Grad zu Overfitting führen kann, da die Entscheidungsgrenze zu genau an die Trainingsdaten
                    angepasst wird. Der Degree-Parameter wird nur verwendet, wenn der Kernel auf "Polynomial" gesetzt ist.
                </div>
                <small v-if="params.kernel !== 'poly'" class="form-text text-muted">
                    Degree wird nur beim Polynomial-Kernel verwendet.
                </small>
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
    name: "SvmArchitecture",
    props: {
        architecture: Object,
    },
    data() {
        return {
            params: {
                kernel: 'linear',
                c: 1,
                degree: 3
            },
            showingInfo: null
        }
    },
    mounted() {
        if (this.architecture.kernel) {
            this.params.kernel = this.architecture.kernel
        }
        if (this.architecture.c) {
            this.params.c = this.architecture.c
        }
        if (this.architecture.degree) {
            this.params.degree = this.architecture.degree
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
