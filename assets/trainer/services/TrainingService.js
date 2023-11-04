
import axios from "axios"

export default {

    async submitTrainingTask(url, form) {
        return axios.post(url, form)
    },

    async loadTasks(url, form) {
        return axios.post(url, form)
    },

    async loadExampleCode(url, form) {
        return axios.post(url, form)
    }

}