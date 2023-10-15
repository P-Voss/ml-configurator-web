<template>
    <div>
        <ul class="nav nav-pills mb-3 justify-content-center nav-fill d-none d-lg-flex" id="pills-tab-lg" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   :class="{'active': currentStep === 'INIT', 'bg-success': initStepCompleted, 'text-light': initStepCompleted}"
                   @click="() => {
                       this.$emit('set-step', 'INIT')
                       currentStep = 'INIT'
                   }"
                   id="pills-basis-tab" data-bs-toggle="pill" href="#pills-basis" role="tab" aria-controls="pills-basis" aria-selected="true">
                    Modellauswahl
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   :class="{'active': currentStep === 'ARCHITECTURE', 'disabled' : !initStepCompleted, 'bg-success': architectureStepCompleted, 'text-light': architectureStepCompleted}"
                   @click="() => {
                       this.$emit('set-step', 'ARCHITECTURE')
                       currentStep = 'ARCHITECTURE'
                   }"
                   id="pills-architektur-tab" data-bs-toggle="pill" href="#pills-architektur" role="tab" aria-controls="pills-architektur" aria-selected="false">
                    Architektur
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   :class="{'disabled' : !initStepCompleted || !architectureStepCompleted}"
                   id="pills-hyperparameter-tab" :href="trainerUrl" role="tab" aria-controls="pills-hyperparameter">
                    Trainingskonfiguration
                </a>
            </li>
        </ul>


        <ul class="nav nav-pills mb-3 justify-content-center flex-column nav-fill d-flex d-lg-none gap-2" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   :class="{'active': currentStep === 'INIT', 'bg-success': initStepCompleted, 'text-light': initStepCompleted}"
                   @click="currentStep = 'INIT'"
                   id="pills-basis-tab" data-bs-toggle="pill" href="#pills-basis" role="tab" aria-controls="pills-basis" aria-selected="true">
                    Modellauswahl
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   :class="{'active': currentStep === 'ARCHITECTURE', 'disabled' : !initStepCompleted, 'bg-success': architectureStepCompleted, 'text-light': architectureStepCompleted}"
                   @click="currentStep = 'ARCHITECTURE'"
                   id="pills-architektur-tab" data-bs-toggle="pill" href="#pills-architektur" role="tab" aria-controls="pills-architektur" aria-selected="false">
                    Architektur
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   :class="{'disabled' : !initStepCompleted || !architectureStepCompleted}"
                   id="pills-hyperparameter-tab" :href="trainerUrl" role="tab" aria-controls="pills-hyperparameter">
                    Trainingskonfiguration
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
        initStepCompleted: Boolean,
        architectureStepCompleted: Boolean,
        trainerUrl: String,
    }
}
</script>