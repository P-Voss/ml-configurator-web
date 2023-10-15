<template>
    <div>
        <ul class="nav nav-pills mb-3 justify-content-center nav-fill" id="trainer-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link bg-success text-light"
                   id="trainer-data-prep-tab" :href="configuratorUrl" role="tab" aria-controls="trainer-data-prep"
                >
                    Konfigurator
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   :class="{'active': currentStep === 'DATA_PREP', 'bg-success': initStepCompleted, 'text-light': initStepCompleted}"
                   @click="() => {
                       this.$emit('set-step', 'DATA_PREP')
                       currentStep = 'DATA_PREP'
                   }"
                   id="trainer-data-prep-tab" data-bs-toggle="pill" href="#trainer-data-prep" role="tab" aria-controls="trainer-data-prep" aria-selected="true">
                    Datenvorbereitung
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   :class="{'active': currentStep === 'HYPERPARAMETER', 'disabled' : !initStepCompleted}"
                   @click="() => {
                       this.$emit('set-step', 'HYPERPARAMETER')
                       currentStep = 'HYPERPARAMETER'
                   }"
                   id="trainer-hyperparameter-tab" data-bs-toggle="pill" href="#trainer-hyperparameter" role="tab" aria-controls="trainer-hyperparameter" aria-selected="false">
                    Hyperparameter
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   :class="{'active': currentStep === 'TRAINING', 'disabled' : !initStepCompleted}"
                   @click="() => {
                       this.$emit('set-step', 'TRAINING')
                       currentStep = 'TRAINING'
                   }"
                   id="trainer-training-tab" data-bs-toggle="pill" href="#trainer-training" role="tab" aria-controls="trainer-training" aria-selected="false">
                    Training
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   :class="{'disabled' : !initStepCompleted || !hyperparameterStepCompleted || !trainingStepCompleted}"
                   id="trainer-training-tab" :href="executorUrl" role="tab" aria-controls="trainer-training" aria-selected="false">
                    Anwendung
                </a>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: "Navigation",
    props: {
        currentStep: String,
        configuratorUrl: String,
        executorUrl: String,
        initStepCompleted: Boolean,
        hyperparameterStepCompleted: Boolean,
        trainingStepCompleted: Boolean,
    }
}
</script>
