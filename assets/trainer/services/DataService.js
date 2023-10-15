
import axios from "axios"

export default {
    async uploadFile(url, form) {
        const headers = { 'Content-Type': 'multipart/form-data' }
        return axios.post(url, form, {headers})
    }
}