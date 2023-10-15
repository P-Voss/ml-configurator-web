<template>
    <div class="row">
        <div class="col-12">
            <TrainerNavigation
                :init-step-completed="initTrainingStepCompleted"
                :current-step="currentTrainingStep"
                :configurator-url="configuratorUrl"
                @set-step="val => currentTrainingStep = val"
            />
            <!-- Hier können dann die Inhalte für die einzelnen Trainingsschritte eingefügt werden -->
        </div>
    </div>
</template>

<script>
import TrainerNavigation from "./Navigation.vue";

export default {
    name: 'Trainer',
    components: {
        TrainerNavigation,
    },
    props: {
        modelId: String,
        loadModelUrl: String,
        configuratorUrl: String,
    },
    data() {
        return {
            currentTrainingStep: "DATA_PREP",
            initTrainingStepCompleted: false,
        }
    },
    methods: {

    }
};
</script>
