import { Page } from '@inertiajs/core';
import axios, { AxiosInstance, AxiosRequestConfig, AxiosPromise } from 'axios';
import { State } from '@/stores/ResumeWizardStore';

const Axios = (page: Page): AxiosInstance => {
    const token = page.props.auth?.user.api_token

    const instance: AxiosInstance = axios.create()

    instance.defaults.headers.common['Authorization'] = token ? `Bearer ${token}` : ''
    instance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

    return instance;
}

const createOrUpdateOptimization = (page: Page, state: State) => {
    state.loading = true

    const axios = Axios(page)
    const options: AxiosRequestConfig = {
        headers: {
            'X-CurrentStep': 0
        }
    }

    const request = state.form.optimizationId ?
        axios.put(route('optimizations.update', state.form.optimizationId), state.form.role, options) :
        axios.post(route('optimizations.store'), state.form.role, options)

    request
        .then(() => {
            state.clearErrors()
            state.nextStep()
        })
        .catch((error: any) => {
            Object.keys(error.response.data.errors).forEach(key => {
                state.form.errors[key] = error.response.data.errors[key][0]
            })
        })
        .finally(() => {
            state.loading = false
        })
}

const uploadResume = (page: Page, state: State, uploadedResume: File): AxiosPromise => {
    state.loading = true

    if (!uploadedResume) {
        state.setError('upload', 'Please select a resume, allowed formats are .pdf and .docx')
        return
    }
    const axios = Axios(page)
    const options: AxiosRequestConfig = {
        headers: {
            'X-CurrentStep': 1,
            'Content-Type': `multipart/form-data`,
        }
    }

    const formData = new FormData()
    formData.set('upload', uploadedResume)

    return axios.post(route('resumes.store'), formData, options)
        .catch((error: any) => {
            Object.keys(error.response.data.errors).forEach(key => {
                state.form.errors[key] = error.response.data.errors[key][0];
            });
        })
        .finally(() => {
            state.loading = false;
        })
}
const updateResume = (page: Page, state: State) => {
    state.loading = true

    const axios = Axios(page)
    const options: AxiosRequestConfig = {
        headers: {
            'X-CurrentStep': 1
        }
    }

    axios.put(route('optimizations.update', state.form.optimizationId), options).then(() => {
            state.clearErrors()
            state.nextStep()
        })
        .catch((error: any) => {
            Object.keys(error.response.data.errors).forEach(key => {
                state.form.errors[key] = error.response.data.errors[key][0]
            })
        })
        .finally(() => {
            state.loading = false
        })
}


export { Axios, createOrUpdateOptimization, updateResume, uploadResume }
