<template>
    <div>
        <div class="row justify-content-around">
            <div class="col-2">
                <div class="h1">Training</div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="accordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button
                                class="accordion-button"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#codeData"
                                aria-expanded="true"
                                aria-controls="codeData"
                                @click="loadExampleCode"
                            >
                                Beispielcode - Aktuelle Modellkonfiguration
                            </button>
                        </h2>
                        <div
                            id="codeData"
                            class="accordion-collapse collapse"
                        >
                        <pre>
                            <code class="python" id="source">{{code}}</code>
                        </pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gy-5">

            <div v-if="state === 'PENDING'" class="col-12 col-lg-8">
                <div class="row justify-content-around">
                    <div class="col-2">
                        <div class="loader"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center">
                        {{stateMessage}}
                    </div>
                </div>
            </div>

            <div v-if="state !== 'PENDING'" class="col-12 col-lg-8">
                <div class="row m-3">
                    <div class="col-12">
                        <button v-if="state === 'INIT'" class="btn btn-primary" @click="submitTrainingTask">Ausführen</button>
                    </div>
                </div>

                <div class="row" v-if="activeTask.state === 'COMPLETED'">
                    <div class="col-12">
                        <SvmResult
                            v-if="this.model.type === 'MODEL_TYPE_SVM'"
                            :data="activeTask.data"
                            :best-training-id="bestTrainingId"
                            :load-example-url="loadExampleUrl"
                        />
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4" v-if="completedTasks.length > 0">
                <div class="h3">Früheres Training</div>
                <div class="d-none d-lg-block" style="max-height: 60vh; overflow-x: hidden; overflow-y: scroll;">
                    <div class="row p-1" v-for="task in completedTasks" :key="task.id">
                        <div class="col-12">
                            <div
                                @click="() => setActive(task.id)"
                                class="card selectionCard"
                                :class="{'border-primary': task.id === bestTrainingId, 'fw-bold': task.id === bestTrainingId, 'activeCard': activeTask.id === task.id}"
                            >
                                <div class="card-body">
                                    <div class="card-title">
                                        Erstellt am: {{$d(task.creationDatetime, 'long')}}
                                    </div>
                                    <div class="card-subtitle text-muted">
                                        Training von {{$d(task.startDatetime, 'short')}} bis {{$d(task.endDatetime, 'short')}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-block d-lg-none pb-3 mb-3">
                    <div class="row p-1" v-for="task in completedTasks" :key="task.id">
                        <div class="col-12">
                            <div
                                @click="() => setActive(task.id)"
                                class="card selectionCard"
                                :class="{'border-primary': task.id === bestTrainingId, 'fw-bold': task.id === bestTrainingId, 'activeCard': activeTask.id === task.id}"
                            >
                                <div class="card-body">
                                    <div class="card-title">
                                        Erstellt am: {{$d(task.creationDatetime, 'long')}}
                                    </div>
                                    <div class="card-subtitle text-muted">
                                        Training von {{$d(task.startDatetime, 'short')}} bis {{$d(task.endDatetime, 'short')}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>

import TrainingService from "../../services/TrainingService.js";
import SvmResult from "./Result/SvmResult.vue";

export default {
    name: "Training",
    components: {SvmResult},
    props: {
        model: Object,
        submitTaskUrl: String,
        executeTaskUrl: String,
        loadTasksUrl: String,
        loadExampleUrl: String,
    },
    data() {
        return {
            state: "INIT",
            stateMessage: "",
            completedTasks: [],
            bestTrainingId: 0,
            activeTask: {
                state: "pending",
                id: null,
                data: {}
            },
            code: "",
        }
    },
    mounted() {
        if (this.state === "INIT") {
            this.loadTasks()
        }
    },
    methods: {
        setActive(taskId) {
            this.activeTask.state = "pending"
            for (let task of this.completedTasks) {
                if (task.id === taskId) {
                    this.activeTask.id = taskId
                    this.activeTask.state = "COMPLETED"
                    this.activeTask.data = {...task}
                }
            }
        },
        async submitTrainingTask() {
            this.state = "PENDING"
            this.stateMessage = "Trainingsauftrag wird angelegt...";
            const form = new FormData()
            form.set('id', this.model.id)
            let response = await TrainingService.submitTrainingTask(this.submitTaskUrl, form)
            if (response.data.success) {
                this.activeTask.id = response.data.taskId
                await this.attemptTaskExecution(response.data.taskId)
            } else {
                this.state = "INIT"
            }
        },
        async attemptTaskExecution(taskId) {
            this.stateMessage = "Führe Trainingsauftrag aus"
            const form = new FormData()
            form.set('taskId', taskId)
            let response = await TrainingService.submitTrainingTask(this.executeTaskUrl, form)
            if (response.data.success) {
                // this.activeTask.data = {...response.data.data}
                this.activeTask.state = "COMPLETED"
                this.state = "INIT"
                this.stateMessage = ""
                await this.loadTasks()
            }
        },
        async loadTasks() {
            this.state = "PENDING"
            this.stateMessage = "Lade Trainingsaufträge"
            const form = new FormData()
            form.set('id', this.model.id)
            let response = await TrainingService.loadTasks(this.loadTasksUrl, form)
            console.log(response)
            if (response.data.success) {
                this.completedTasks = response.data.completedTasks
                this.bestTrainingId = response.data.bestTrainingId
                for (let task of response.data.completedTasks) {
                    if (task.id === this.activeTask.id) {
                        this.activeTask.data = {...task}
                    }
                }
            }
            this.stateMessage = ""
            this.state = "INIT"
        },
        async loadExampleCode() {
            if (this.code) {
                return
            }
            let form = new FormData()
            form.set('id', this.model.id)
            let response = await TrainingService.loadExampleCode(this.loadExampleUrl, form)
            if (response.data.success) {
                this.code = response.data.code
                document.querySelectorAll('pre code').forEach((el) => {
                    this.$hljs.initHighlighting()
                });
            }
        }
    }
}

</script>
