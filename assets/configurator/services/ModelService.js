
import axios from "axios"

export const initializeModel = async (url, formdata) => {
    return axios.post(url, formdata)
}

export const updateModel = async (url, formdata) => {
    return axios.post(url, formdata)
}

export const loadModel = async (url, form) => {
    return axios.post(url, form)
}

export const rollback = async (url, form) => {
    return axios.post(url, form)
}
