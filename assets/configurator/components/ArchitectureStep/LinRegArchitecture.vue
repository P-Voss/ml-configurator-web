<template>
    <form @submit.prevent="handleSubmit" class="gx-4">
        <div class="row mb-3">
            <div class="col-12 col-lg-6">
                <label for="regularizationType" class="form-label">Regularisierungstyp:</label>
                <div class="input-group">
                    <select class="form-select" id="regularizationType" v-model="params.regularizationType">
                        <option value="none">Keine Regularisierung</option>
                        <option value="l1">L1-Regularisierung (Lasso)</option>
                        <option value="l2">L2-Regularisierung (Ridge)</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('regularizationType')">?</button>
                </div>
                <div v-if="showingInfo === 'regularizationType'" class="form-text">
                    Der Regularisierungstyp steuert die Art der Strafe, die auf das Modell angewendet wird, um Overfitting zu vermeiden.
                    <p>
                        <span class="fw-bold">Keine Regularisierung: </span>Das Modell wird ohne Regularisierung trainiert.
                    </p>
                    <p>
                        <span class="fw-bold">L1-Regularisierung (Lasso): </span> Fördert das Modell, dünn besetzte Gewichte zu lernen,
                        wodurch irrelevante oder schwach wirkende Features eliminiert werden können.
                    </p>
                    <p>
                        <span class="fw-bold">L2-Regularisierung (Ridge): </span> Fügt der Fehlerfunktion eine Strafterm hinzu,
                        die proportional zum Quadrat der Summe der Gewichte ist. Dies führt dazu, dass die Gewichte kleiner werden,
                        aber nicht auf Null reduziert werden.
                    </p>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <label for="alpha" class="form-label">Alpha:</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="alpha" v-model="params.alpha" />
                    <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('alpha')">?</button>
                </div>
                <div v-if="showingInfo === 'alpha'" class="form-text">
                    Der Alpha-Parameter kontrolliert die Stärke der Regularisierung. Ein höherer Alpha-Wert führt zu einer stärkeren
                    Regularisierung. Die optimale Wahl von Alpha ist datenabhängig und sollte durch Cross-Validation bestimmt werden.
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
    name: "LinRegArchitecture",
    props: {
        configuration: Object,
    },
    data() {
        return {
            params: {
                regularizationType: 'none',
                alpha: 1,
            },
            showingInfo: null,
        };
    },
    mounted() {
        if (this.configuration.regularizationType) {
            this.params.regularizationType = this.configuration.regularizationType;
        }
        if (this.configuration.alpha) {
            this.params.alpha = this.configuration.alpha;
        }
    },
    methods: {
        handleSubmit() {
            this.$emit("save-configuration", this.params);
        },
        toggleInfo(parameter) {
            this.showingInfo = this.showingInfo === parameter ? null : parameter;
        },
    },
};
</script>