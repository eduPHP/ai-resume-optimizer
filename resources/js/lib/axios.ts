import { Page, PageProps } from '@inertiajs/core';
import axios, { AxiosInstance, AxiosRequestConfig, AxiosPromise, AxiosResponse } from 'axios';
import {
    OptimizationType,
    OptimizationWizardStore,
    Resume,
    useOptimizationWizardStore
} from '@/stores/OptimizationWizardStore';
import { useToastsStore } from '@/stores/ToastsStore';
import { usePage } from '@inertiajs/vue3';
import { AISettings } from '@/types';

interface User {
    id: number;
    name: string;
    email: string;
    api_token: string;
}


interface JobInformation {
    supported: boolean;
    company: string;
    position: string;
    location: string;
    url: string;
    description: string;
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

const handleError = async (error: any): Promise<any> => {

    if (error.response.data?.errors) {
        const state = useOptimizationWizardStore();

        Object.keys(error.response.data?.errors).forEach(key => {
            state.form.errors[key] = error.response.data.errors[key][0]
        })
    } else {
        const toast = useToastsStore();
        toast.error(error.response.data.message ?? 'An error has occurred!', 'Please try again later.')
    }

    return Promise.reject(error);
}

const Axios = (): AxiosInstance => {
    const page = usePage() as AppPage;
    const token = getAuthToken(page);

    const instance: AxiosInstance = axios.create()

    instance.defaults.headers.common['Authorization'] = token ? `Bearer ${token}` : ''
    instance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
    instance.defaults.headers.common['X-Timezone-Offset'] = new Date().getTimezoneOffset() * -1
    instance.interceptors.response.use((response) => response, (error: any) => {
        return handleError(error)
    });

    return instance;
}

const deleteOptimization = async (): Promise<boolean> => {
    const state = useOptimizationWizardStore();

    state.loading = true

    const axios = Axios()

    await axios.delete(route('optimizations.destroy', state.form.optimizationId)).then(() => {
        state.clearErrors()
    })

    state.loading = false

    return new Promise(resolve => resolve(true))
}

const createOrUpdateOptimization = (): AxiosPromise<{optimization: OptimizationType, created: boolean}> => {
    const state = useOptimizationWizardStore();

    state.loading = true

    const axios = Axios()
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
        .finally(() => {
            state.loading = false
        })

    return request
}

const uploadResume = (state: OptimizationWizardStore, uploadedResume: File): AxiosPromise<Resume> => {
    state.loading = true

    if (!uploadedResume) {
        state.setError('upload', 'Please select a resume, allowed formats are .pdf and .docx')
        return Promise.reject(new Error('No resume provided'))
    }
    const axios = Axios()
    const options: AxiosRequestConfig = {
        headers: {
            'X-CurrentStep': 1,
            'Content-Type': `multipart/form-data`,
        }
    }

    const formData = new FormData()
    formData.set('upload', uploadedResume)

    return axios.post<Resume>(route('resumes.store'), formData, options)
        .finally(() => {
            state.loading = false;
        })
}

const updateAdditionalInformation = (state: OptimizationWizardStore) => {
    state.loading = true

    const axios = Axios()
    const options: AxiosRequestConfig = {
        headers: {
            'X-CurrentStep': 2
        }
    }

    axios.put(route('optimizations.update', state.form.optimizationId), state.form.additional, options).then(() => {
        state.clearErrors()
        state.nextStep()
    }).finally(() => {
        state.loading = false
    })
}

const updateResume = (state: OptimizationWizardStore) => {
    state.loading = true

    const axios = Axios()
    const options: AxiosRequestConfig = {
        headers: {
            'X-CurrentStep': 1
        }
    }

    axios.put(route('optimizations.update', state.form.optimizationId), state.form.resume, options).then(() => {
            state.clearErrors()
            state.nextStep()
        }).finally(() => {
            state.loading = false
        })
}

const completeWizard = (state: OptimizationWizardStore): AxiosPromise<{optimization: OptimizationType, errors: {[key: string]: string}}> => {
    state.loading = true
    state.optimizing = true

    const axios = Axios()
    const options: AxiosRequestConfig = {
        headers: {
            'X-CurrentStep': 3
        },
        timeout: 360000, // 6min
    }

    return axios.put<{optimization: OptimizationType, errors: {[key: string]: string}}>(route('optimizations.update', state.form.optimizationId), {}, options)
        .finally(() => {
            state.loading = false
            state.optimizing = false
        })
}

const downloadOptimizedResume: (state: OptimizationWizardStore) => Promise<void> = (state: OptimizationWizardStore) => {
    const url: string = route('optimizations.download', state.form.optimizationId)

    return downloadFile(url)
}

const downloadCoverLetter: (state: OptimizationWizardStore) => Promise<void> = (state: OptimizationWizardStore) => {
    const url: string = route('optimizations.download-cover', state.form.optimizationId)

    return downloadFile(url)
}

const cancelOptimizationEdit = (state: OptimizationWizardStore) => {
    state.loading = true

    const axios = Axios()

    return axios.put(route('optimizations.cancel', state.form.optimizationId))
        .then(response => {
            state.clearErrors()
            state.setOptimization(response.data.optimization)
            state.form.status = 'complete'
        })
        .finally(() => {
            state.loading = false
        })
}

/*
 * Credits: https://stackoverflow.com/a/75039478/29766047,
 *          https://gist.github.com/javilobo8/097c30a233786be52070986d8cdb1743?permalink_comment_id=3788793#gistcomment-3788793
 */
const downloadFile: (url: string) => Promise<void> = (url: string) => {
    const axios = Axios()

    const options: AxiosRequestConfig = {
        headers: {
            'Content-Type': 'application/json',
        },
        responseType: 'blob',
    }
    return axios.post(url, {}, options).then(function (response) {
        const a = document.createElement('a');
        const contentDisposition = response.headers['content-disposition'];
        let fileName = 'file.pdf';
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

const updateUserAISettings: (settings: AISettings) => void = async (settings: AISettings): Promise<void> => {
    const axios = Axios()
    const toast = useToastsStore()

    const response = await axios.put<{message: string}>(route('users.update-instructions'), settings)

    toast.success('Saved!', response.data.message)
}

const getJobInformation: (url: string) => Promise<AxiosResponse<JobInformation>> = async (url: string) => {
    const axios = Axios()
    const toast = useToastsStore()

    return axios.post<JobInformation>(route('jobs.crawl'), {url}).then(response => {
        if (response.data.supported) {
            toast.success('Saved!', 'Job information has been successfully retrieved.')
        }

        return response
    })
}



export {
    Axios,
    createOrUpdateOptimization,
    updateResume,
    uploadResume,
    updateAdditionalInformation,
    completeWizard,
    downloadOptimizedResume,
    downloadCoverLetter,
    deleteOptimization,
    cancelOptimizationEdit,
    updateUserAISettings,
    getJobInformation,
}
