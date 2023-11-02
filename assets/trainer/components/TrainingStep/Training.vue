<template>
    <div>
        <div class="row">
            <div class="col-12">
                <div class="h1">Training</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <button class="btn btn-primary" @click="submitTrainingTask">Ausführen</button>
            </div>
        </div>
    </div>
</template>
<script>

export default {
    name: "Training",
    props: {

    },
    data() {
        return {

        }
    },
    mounted() {

    },
    methods: {
        submitTrainingTask() {
            this.$emit('submit-training-task')
        }
    }
}

</script>