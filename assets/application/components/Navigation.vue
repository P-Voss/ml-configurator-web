<template>
    <div>
        <ul class="nav nav-pills mb-3 justify-content-center nav-fill d-none d-lg-flex" id="pills-tab-lg" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link bg-success text-light"
                   id="pills-configurator-tab" :href="configuratorUrl" role="tab">
                    Konfigurator
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link bg-success text-light"
                   id="pills-architektur-tab" :href="trainerUrl" role="tab">
                    Training
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link active"
                   id="pills-hyperparameter-tab" role="tab" aria-controls="pills-hyperparameter">
                    Anwendung
                </a>
            </li>
        </ul>


        <ul class="nav nav-pills mb-3 justify-content-center flex-column nav-fill d-flex d-lg-none gap-2" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link bg-success text-light"
                   id="pills-configurator-tab" :href="configuratorUrl" role="tab">
                    Konfigurator
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link bg-success text-light"
                   id="pills-architektur-tab" :href="trainerUrl" role="tab">
                    Training
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link active"
                   id="pills-hyperparameter-tab" role="tab" aria-controls="pills-hyperparameter">
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
        trainerUrl: String,
        configuratorUrl: String,
    }
}
</script>