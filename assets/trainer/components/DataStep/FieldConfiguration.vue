<template>
    <div>
        <div class="mb-3">
            <label for="dataType" class="form-label">Datentyp auswählen:</label>
            <select class="form-select" id="dataType" v-model="selectedType" >
                <option value="number">Nummer</option>
                <option value="text">Text</option>
            </select>
        </div>

        <div v-if="selectedType === 'number'">
            <div class="mb-3">
                <label for="scalingMethod" class="form-label">Skalierungsmethode:</label>
                <div class="input-group">
                    <select class="form-select" id="scalingMethod" v-model="config.scalingMethod">
                        <option value="standardization">Standardisierung</option>
                        <option value="min_max_scaling">Min-Max-Scaling</option>
                        <option value="robust_scaling">Robuste Skalierung</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="showHelp('scalingMethod')">?</button>
                </div>
                <div v-if="showingHelp === 'scalingMethod'" class="form-text">
                    Hier können Sie die Methode zur Skalierung der Daten auswählen.
                    - Standardisierung transformiert die Daten auf eine Standardnormalverteilung.
                    - Min-Max-Scaling skaliert die Daten auf einen bestimmten Bereich.
                    - Robuste Skalierung ist eine skalierungsbasierte Methode, die robust gegenüber Ausreißern ist.
                </div>
            </div>

            <div class="mb-3">
                <label for="outlierTreatment" class="form-label">Ausreißer-Behandlung:</label>
                <div class="input-group">
                    <select class="form-select" id="outlierTreatment" v-model="config.outlierTreatment">
                        <option value="remove">Entfernen</option>
                        <option value="mark">Markieren</option>
                        <option value="rescale">Neu skalieren</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="showHelp('outlierTreatment')">?</button>
                </div>
                <div v-if="showingHelp === 'outlierTreatment'" class="form-text">
                    Wählen Sie hier aus, wie mit Ausreißern in den Daten umgegangen werden soll.
                    - Entfernen: Entfernt Ausreißer aus den Daten.
                    - Markieren: Markiert Ausreißer für eine spätere Behandlung.
                    - Neu skalieren: Skaliert Ausreißer, um ihre Auswirkungen zu minimieren.
                </div>
            </div>

            <div class="mb-3">
                <label for="normalizationMethod" class="form-label">Normalisierungsmethode:</label>
                <div class="input-group">
                    <select class="form-select" id="normalizationMethod" v-model="config.normalizationMethod">
                        <option value="l1">L1-Normalisierung</option>
                        <option value="l2">L2-Normalisierung</option>
                        <option value="max">Max-Normalisierung</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="showHelp('normalizationMethod')">?</button>
                </div>
                <div v-if="showingHelp === 'normalizationMethod'" class="form-text">
                    Hier können Sie die Methode zur Normalisierung der Daten auswählen.
                    - L1-Normalisierung normalisiert die Daten basierend auf der L1-Norm.
                    - L2-Normalisierung normalisiert die Daten basierend auf der L2-Norm.
                    - Max-Normalisierung normalisiert die Daten basierend auf dem Maximum.
                </div>
            </div>
        </div>

        <div v-if="selectedType === 'text'">
            <div class="mb-3">
                <label for="tokenizationMethod" class="form-label">Tokenisierungsmethode:</label>
                <div class="input-group">
                    <select class="form-select" id="tokenizationMethod" v-model="config.tokenizationMethod">
                        <option value="word">Wort</option>
                        <option value="character">Zeichen</option>
                        <option value="ngram">N-Gramm</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="showHelp('tokenizationMethod')">?</button>
                </div>
                <div v-if="showingHelp === 'tokenizationMethod'" class="form-text">
                    Hier können Sie die Methode zur Zerlegung des Textes in einzelne Tokens auswählen.
                    - Wort: Teilt den Text in einzelne Wörter auf.
                    - Zeichen: Teilt den Text in einzelne Zeichen auf.
                    - N-Gramm: Teilt den Text in N-Gruppen aufeinanderfolgender Zeichen oder Wörter auf.
                </div>
            </div>

            <div class="mb-3">
                <label for="specialCharacterHandling" class="form-label">Behandlung von Sonderzeichen:</label>
                <div class="input-group">
                    <select class="form-select" id="specialCharacterHandling" v-model="config.specialCharacterHandling">
                        <option value="remove">Entfernen</option>
                        <option value="replace">Ersetzen</option>
                        <option value="keep">Behalten</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="showHelp('specialCharacterHandling')">?</button>
                </div>
                <div v-if="showingHelp === 'specialCharacterHandling'" class="form-text">
                    Wählen Sie hier aus, wie mit Sonderzeichen im Text umgegangen werden soll.
                    - Entfernen: Entfernt alle Sonderzeichen aus dem Text.
                    - Ersetzen: Ersetzt Sonderzeichen durch geeignete Zeichen oder Platzhalter.
                    - Behalten: Behält Sonderzeichen im Text bei.
                </div>
            </div>

            <div class="mb-3">
                <label for="vectorizationMethod" class="form-label">Vektorisierungsmethode:</label>
                <div class="input-group">
                    <select class="form-select" id="vectorizationMethod" v-model="config.vectorizationMethod">
                        <option value="tfidf">TF-IDF</option>
                        <option value="count">Zähler</option>
                        <option value="binary">Binär</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="showHelp('vectorizationMethod')">?</button>
                </div>
                <div v-if="showingHelp === 'vectorizationMethod'" class="form-text">
                    Hier können Sie die Methode zur Umwandlung des Textes in Vektoren auswählen.
                    - TF-IDF: Berechnet die Termfrequenz-Inversdokumentfrequenz für die Vektorisierung.
                    - Zähler: Zählt die Anzahl der Vorkommen jedes Worts im Text.
                    - Binär: Markiert das Vorhandensein oder Fehlen von Wörtern im Text.
                </div>
            </div>

            <div class="mb-3">
                <label for="padding" class="form-label">Padding:</label>
                <div class="input-group">
                    <select class="form-select" id="padding" v-model="config.padding">
                        <option value="true">Ja</option>
                        <option value="false">Nein</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" @click="showHelp('padding')">?</button>
                </div>
                <div v-if="showingHelp === 'padding'" class="form-text">
                    Wählen Sie hier aus, ob Padding für den Text angewendet werden soll.
                    - Ja: Fügt Padding zu den Textdaten hinzu, um eine einheitliche Länge zu erreichen.
                    - Nein: Verwendet keine Padding-Technik für den Text.
                </div>
            </div>

            <div class="mb-3">
                <label for="paddingSize" class="form-label">Padding-Size:</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="paddingSize" v-model="config.paddingSize" />
                    <button type="button" class="btn btn-outline-secondary" @click="showHelp('paddingSize')">?</button>
                </div>
                <div v-if="showingHelp === 'paddingSize'" class="form-text">
                    Geben Sie hier die maximale Länge an, die für das Padding verwendet werden soll.
                    Dies bestimmt die maximale Länge aller Textdaten, um die einheitliche Länge zu gewährleisten.
                </div>
            </div>
        </div>

        <div>
            <button class="btn btn-primary" @click.prevent="saveConfiguration">Speichern</button>
        </div>
    </div>
</template>

<script>
export default {
    name: 'FieldConfiguration',
    props: {
        field: Object,
    },
    data() {
        return {
            selectedType: 'number',
            showingHelp: null,
            config: {
                scalingMethod: 'standardization',
                outlierTreatment: 'remove',
                normalizationMethod: 'l1',
                tokenizationMethod: 'word',
                specialCharacterHandling: 'remove',
                vectorizationMethod: 'tfidf',
                padding: false,
                paddingType: 'none',
                paddingSize: 10
            }
        };
    },
    methods: {
        saveConfiguration() {
            let form = new FormData()
            form.set('fieldname', this.field.name)
            form.set('scalingMethod', this.config.scalingMethod)
            form.set('outlierTreatment', this.config.outlierTreatment)
            form.set('normalizationMethod', this.config.normalizationMethod)
            form.set('tokenizationMethod', this.config.tokenizationMethod)
            form.set('specialCharacterHandling', this.config.specialCharacterHandling)
            form.set('vectorizationMethod', this.config.vectorizationMethod)
            form.set('padding', this.config.padding)
            form.set('paddingType', this.config.paddingType)
            form.set('paddingSize', this.config.paddingSize)

            this.$emit('save-configuration', form)
        },
        showHelp(parameter) {
            this.showingHelp = this.showingHelp === parameter ? null : parameter;
        }
    }
};
</script>
