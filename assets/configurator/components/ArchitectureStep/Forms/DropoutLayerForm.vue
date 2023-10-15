<template>
    <form @submit.prevent="handleSubmit">

        <div class="mb-3">
            <label for="dropout" class="form-label">Dropout-Rate:</label>
            <div class="input-group">
                <input type="number" class="form-control" id="dropout" v-model="layer.dropoutRate" placeholder="0.0 - 1.0" min="0" max="1" step="0.01"/>
                <button type="button" class="btn btn-outline-secondary" @click="toggleInfo('dropout')">?</button>
            </div>
            <div v-if="showingInfo === 'dropout'" class="form-text">
                Ein Mechanismus gegen Overfitting. Ein bestimmter Prozentsatz der Eingabe-Einheiten wird zufällig in jedem Trainingsschritt "ausgeschaltet".
            </div>
            <p class="mt-3">
                Manchmal wird Dropout als Parameter in anderen Layern verwendet und manchmal als eigenständiger Layer. Der Unterschied liegt hauptsächlich in der Implementierung und Flexibilität. Durch das Hinzufügen eines eigenen Dropout-Layers können Sie den Dropout gezielt auf bestimmte Schichten anwenden und die Architektur des Netzwerks anpassen. Ein Dropout-Parameter innerhalb eines anderen Layers bietet eine kompaktere Möglichkeit, Dropout für diesen speziellen Layer hinzuzufügen.
            </p>
        </div>

        <button type="submit" class="btn btn-primary">Dropout-Schicht hinzufügen</button>

    </form>
</template>

<script>
export default {
    name: 'DropoutLayerForm',
    data() {
        return {
            layer: {
                type: 'LAYER_TYPE_DROPOUT',
                dropoutRate: 0.5,
            },
            showingInfo: null
        };
    },
    methods: {
        handleSubmit() {
            let params = {
                type: this.layer.type,
                neurons: 0,
                returnSequences: false,
                activationFunction: "",
                regularizationType: "",
                lambda: 0,
                dropoutRate: this.layer.dropoutRate,
                recurrentDropoutRate: 0,
            }

            this.$emit("add-layer", params);
        },
        toggleInfo(parameter) {
            this.showingInfo = this.showingInfo === parameter ? null : parameter;
        }
    }
}
</script>