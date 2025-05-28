import { Page } from '@inertiajs/core';
import axios, { AxiosInstance, AxiosRequestConfig, AxiosPromise, AxiosResponse } from 'axios';
import { Optimization, State, Resume } from '@/stores/ResumeWizardStore';

const Axios = (page: Page): AxiosInstance => {
    const token = page.props.auth?.user.api_token

    const instance: AxiosInstance = axios.create()

    instance.defaults.headers.common['Authorization'] = token ? `Bearer ${token}` : ''
    instance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

    return instance;
}

const createOrUpdateOptimization = (page: Page, state: State): AxiosPromise<AxiosResponse<{optimization: Optimization, created: boolean}>> => {
    state.loading = true

    const axios = Axios(page)
    const options: AxiosRequestConfig = {
        headers: {
            'X-CurrentStep': state.step
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

    return request
}

const uploadResume = (page: Page, state: State, uploadedResume: File): AxiosPromise<Resume> => {
    state.loading = true

    if (!uploadedResume) {
        state.setError('upload', 'Please select a resume, allowed formats are .pdf and .docx')
        return Promise.reject(new Error('No resume provided'))
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

    return axios.post<Resume>(route('resumes.store'), formData, options)
        .catch((error: any) => {
            Object.keys(error.response.data.errors).forEach(key => {
                state.form.errors[key] = error.response.data.errors[key][0];
            });
            throw error;
        })
        .finally(() => {
            state.loading = false;
        })
}

const updateAdditionalInformation = (page: Page, state: State) => {
    state.loading = true

    const axios = Axios(page)
    const options: AxiosRequestConfig = {
        headers: {
            'X-CurrentStep': 2
        }
    }

    axios.put(route('optimizations.update', state.form.optimizationId), state.form.additional, options).then(() => {
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

const updateResume = (page: Page, state: State) => {
    state.loading = true

    const axios = Axios(page)
    const options: AxiosRequestConfig = {
        headers: {
            'X-CurrentStep': 1
        }
    }

    axios.put(route('optimizations.update', state.form.optimizationId), state.form.resume, options).then(() => {
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

const completeWizard = (page: Page, state: State) => {
    state.loading = true

    const axios = Axios(page)
    const options: AxiosRequestConfig = {
        headers: {
            'X-CurrentStep': 3
        }
    }

    return axios.put(route('optimizations.update', state.form.optimizationId), {}, options)
        .catch((error: any) => {
            Object.keys(error.response.data.errors).forEach(key => {
                state.form.errors[key] = error.response.data.errors[key][0]
            })
        })
        .finally(() => {
            state.loading = false
        })
}


export {
    Axios,
    createOrUpdateOptimization,
    updateResume,
    uploadResume,
    updateAdditionalInformation,
    completeWizard,
}
