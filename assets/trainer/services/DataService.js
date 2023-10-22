
import axios from "axios"

export default {
    async uploadFile(url, form) {
        const headers = { 'Content-Type': 'multipart/form-data' }
        return axios.post(url, form, {headers})
    },
    async toggleFieldState(url, form) {
        return axios.post(url, form)
    },
    async updateField(url, form) {
        return axios.post(url, form)
    },
    async saveHyperparameter(url, form) {
        return axios.post(url, form)
    }
}