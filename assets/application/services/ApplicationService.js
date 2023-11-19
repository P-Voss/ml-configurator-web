
import axios from "axios"

export default {
    async execute(url, formdata) {
        return axios.post(url, formdata)
    },
    async loadExample(url) {
        return axios.get(url)
    }
}