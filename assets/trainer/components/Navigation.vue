<template>
    <div>
        <ul class="nav nav-pills mb-3 justify-content-center nav-fill d-none d-lg-flex" id="trainer-tab-lg" role="tablist" data-bs-theme="dark">
            <li class="nav-item" role="presentation">
                <a class="nav-link d-flex align-items-center bg-success text-light"
                   :href="configuratorUrl" role="tab"
                >
                    <span class="material-symbols-outlined">settings</span>Konfigurator
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   :class="{'active': currentStep === 'DATA_PREP', 'bg-success': initStepCompleted, 'text-light': initStepCompleted}"
                   @click="() => {
                       this.$emit('set-step', 'DATA_PREP')
                       currentStep = 'DATA_PREP'
                   }"
                   id="trainer-data-prep-tab-lg" data-bs-toggle="pill" href="#trainer-data-prep" role="tab" aria-controls="trainer-data-prep" aria-selected="true">
                    Datenvorbereitung
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   :class="{'active': currentStep === 'HYPERPARAMETER', 'bg-success': hyperparameterStepCompleted, 'text-light': hyperparameterStepCompleted, 'disabled' : !initStepCompleted}"
                   @click="() => {
                       this.$emit('set-step', 'HYPERPARAMETER')
                       currentStep = 'HYPERPARAMETER'
                   }"
                   id="trainer-hyperparameter-tab-lg"
                   data-bs-toggle="pill"
                   href="#trainer-hyperparameter"
                   role="tab"
                   aria-controls="trainer-hyperparameter"
                   aria-selected="false"
                >
                    Hyperparameter
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   :class="{'active': currentStep === 'TRAINING', 'disabled' : !initStepCompleted|| !hyperparameterStepCompleted, 'bg-success text-light': trainingStepCompleted}"
                   @click="() => {
                       this.$emit('set-step', 'TRAINING')
                       currentStep = 'TRAINING'
                   }"
                   id="trainer-training-tab-lg" data-bs-toggle="pill" href="#trainer-training" role="tab" aria-controls="trainer-training" aria-selected="false">
                    Training
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link d-flex align-items-center"
                   :class="{'disabled' : !initStepCompleted || !hyperparameterStepCompleted || !trainingStepCompleted}"
                   id="trainer-execute-tab-lg" :href="executorUrl" role="tab" aria-controls="trainer-execute" aria-selected="false">
                    <span class="material-symbols-outlined">keyboard_double_arrow_right</span>Anwendung
                </a>
            </li>
        </ul>


        <ul class="nav nav-pills mb-3 justify-content-center flex-column nav-fill d-flex d-lg-none gap-2" id="trainer-tab" role="tablist" data-bs-theme="dark">
            <li class="nav-item" role="presentation">
                <a class="nav-link d-flex align-items-center bg-success text-light"
                   id="trainer-data-prep-tab" :href="configuratorUrl" role="tab" aria-controls="trainer-data-prep"
                >
                    <span class="material-symbols-outlined">settings</span>Konfigurator
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
                   :class="{'active': currentStep === 'HYPERPARAMETER', 'bg-success': hyperparameterStepCompleted, 'text-light': hyperparameterStepCompleted, 'disabled' : !initStepCompleted}"
                   @click="() => {
                       this.$emit('set-step', 'HYPERPARAMETER')
                       currentStep = 'HYPERPARAMETER'
                   }"
                   id="trainer-hyperparameter-tab"
                   data-bs-toggle="pill"
                   href="#trainer-hyperparameter"
                   role="tab"
                   aria-controls="trainer-hyperparameter"
                   aria-selected="false"
                >
                    Hyperparameter
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   :class="{'active': currentStep === 'TRAINING', 'disabled' : !initStepCompleted || !hyperparameterStepCompleted, 'bg-success text-light': trainingStepCompleted}"
                   @click="() => {
                       this.$emit('set-step', 'TRAINING')
                       currentStep = 'TRAINING'
                   }"
                   id="trainer-training-tab"
                   data-bs-toggle="pill"
                   href="#trainer-training"
                   role="tab"
                   aria-controls="trainer-training"
                   aria-selected="false"
                >
                    Training
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link d-flex align-items-center"
                   :class="{'disabled' : !initStepCompleted || !hyperparameterStepCompleted || !trainingStepCompleted}"
                   id="trainer-execute-tab"
                   :href="executorUrl"
                   role="tab"
                   aria-controls="trainer-execute"
                   aria-selected="false"
                >
                    <span class="material-symbols-outlined">keyboard_double_arrow_right</span>Anwendung
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
