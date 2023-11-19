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
                    {{$t("navigation.modelselection")}}
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
                    {{$t("navigation.architecture")}}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link d-flex align-items-center"
                   :class="{'disabled' : !initStepCompleted, 'bg-success text-light': trainingStepCompleted}"
                   id="pills-hyperparameter-tab" :href="trainerUrl" role="tab" aria-controls="pills-hyperparameter">
                    <span class="material-symbols-outlined">model_training</span>{{$t("navigation.training")}}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link d-flex align-items-center"
                   :class="{'disabled' : !trainingStepCompleted}"
                   id="pills-hyperparameter-tab" :href="executorUrl" role="tab" aria-controls="pills-hyperparameter">
                    <span class="material-symbols-outlined">keyboard_double_arrow_right</span>{{$t("navigation.execute")}}
                </a>
            </li>
        </ul>


        <ul class="nav nav-pills mb-3 justify-content-center flex-column nav-fill d-flex d-lg-none gap-2" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   :class="{'active': currentStep === 'INIT', 'bg-success': initStepCompleted, 'text-light': initStepCompleted}"
                   @click="currentStep = 'INIT'"
                   id="pills-basis-tab" data-bs-toggle="pill" href="#pills-basis" role="tab" aria-controls="pills-basis" aria-selected="true">
                    {{$t("navigation.modelselection")}}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   :class="{'active': currentStep === 'ARCHITECTURE', 'disabled' : !initStepCompleted, 'bg-success': architectureStepCompleted, 'text-light': architectureStepCompleted}"
                   @click="currentStep = 'ARCHITECTURE'"
                   id="pills-architektur-tab" data-bs-toggle="pill" href="#pills-architektur" role="tab" aria-controls="pills-architektur" aria-selected="false">
                    {{$t("navigation.architecture")}}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-linkd-flex align-items-center"
                   :class="{'disabled' : !initStepCompleted, 'bg-success text-light': trainingStepCompleted}"
                   id="pills-hyperparameter-tab" :href="trainerUrl" role="tab" aria-controls="pills-hyperparameter">
                    <span class="material-symbols-outlined">model_training</span>{{$t("navigation.training")}}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link d-flex align-items-center"
                   :class="{'disabled' : !trainingStepCompleted}"
                   id="pills-hyperparameter-tab" :href="executorUrl" role="tab" aria-controls="pills-hyperparameter">
                    <span class="material-symbols-outlined">keyboard_double_arrow_right</span>{{$t("navigation.execute")}}
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
        trainingStepCompleted: Boolean,
        trainerUrl: String,
        executorUrl: String,
    }
}
</script>