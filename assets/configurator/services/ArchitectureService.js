
import axios from "axios"

export default {
    async addLayer(url, formdata) {
        return axios.post(url, formdata)
    },
    async removeLayer(url, formdata) {
        return axios.post(url, formdata)
    },
    async setConfiguration(url, formdata) {
        return axios.post(url, formdata)
    }
}