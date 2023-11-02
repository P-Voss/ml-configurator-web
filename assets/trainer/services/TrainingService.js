
import axios from "axios"

export default {

    submitTrainingTask(url, form) {
        return axios.post(url, form)
    }

}