import { Page, PageProps } from '@inertiajs/core';
import axios, { AxiosInstance, AxiosRequestConfig, AxiosPromise } from 'axios';
import { OptimizationType, OptimizationWizardStore, Resume } from '@/stores/OptimizationWizardStore';
interface User {
    id: number;
    name: string;
    email: string;
    api_token: string;
}

interface AuthProps {
    user?: User | undefined;
}
interface AppPageProps extends PageProps {
    auth?: AuthProps | undefined;
}

export type AppPage = Page<AppPageProps>;

const getAuthToken = (page: AppPage): string => {
    if (!page.props.auth?.user) {
        return '';
    }

    const token = page.props.auth.user.api_token;
    return token ? `Bearer ${token}` : '';
};

const Axios = (page: AppPage): AxiosInstance => {
    const token = getAuthToken(page);

    const instance: AxiosInstance = axios.create()

    instance.defaults.headers.common['Authorization'] = token ? `Bearer ${token}` : ''
    instance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

    return instance;
}

const createOrUpdateOptimization = (page: Page, state: OptimizationWizardStore): AxiosPromise<{optimization: OptimizationType, created: boolean}> => {
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

const uploadResume = (page: Page, state: OptimizationWizardStore, uploadedResume: File): AxiosPromise<Resume> => {
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

const updateAdditionalInformation = (page: Page, state: OptimizationWizardStore) => {
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

const updateResume = (page: Page, state: OptimizationWizardStore) => {
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

const completeWizard = (page: Page, state: OptimizationWizardStore) => {
    state.loading = true
    state.optimizing = true

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
            state.optimizing = false
        })
}

/* Credits: https://stackoverflow.com/a/75039478/29766047 */
const downloadPDF: (page: Page, state: OptimizationWizardStore) => Promise<void> = (page: Page, state: OptimizationWizardStore) => {
    const axios = Axios(page)

    const options: AxiosRequestConfig = {
        headers: {
            'Content-Type': 'application/json',
        },
        responseType: 'blob',
    }
    return axios.post(route('optimizations.download', state.form.optimizationId), {}, options).then(function (response) {
        const a = document.createElement('a');
        const contentDisposition = response.headers['content-disposition'];
        let fileName = 'unknown';
        if (contentDisposition) {
            const fileNameMatch = contentDisposition.match(/filename="(.+)"/);
            if (fileNameMatch.length === 2)
                fileName = fileNameMatch[1];
        }
        a.href = window.URL.createObjectURL(response.data);
        a.download = fileName;
        document.body.appendChild(a);
        a.click();
        a.remove();
    });
};


export {
    Axios,
    createOrUpdateOptimization,
    updateResume,
    uploadResume,
    updateAdditionalInformation,
    completeWizard,
    downloadPDF,
}
