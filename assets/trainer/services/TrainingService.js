
import axios from "axios"

export default {

    async deleteTrainingTask(url, form) {
        return axios.post(url, form)
    },

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