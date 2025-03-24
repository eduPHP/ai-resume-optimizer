<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { DocumentIcon, InboxArrowDownIcon } from '@heroicons/vue/24/solid';
import { ref } from 'vue';
import axios, { AxiosResponse } from 'axios';
import { Switch } from '@headlessui/vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

interface Resume {
    id: number;
    name: string;
    content: string;
    optimized_content: string;
    role_details: string;
    reasoning: string;
}

interface FileUpload extends File {
    humanReadableSize: string;
}

const file = ref<FileUpload>({} as FileUpload);
const resume = ref<Resume>({
    content: '',
    optimized_content: '',
    role_details: '',
    reasoning: '',
} as Resume);
const loading = ref<boolean>(false);
const addRoleInfo = ref<boolean>(false);
const feedback = ref<string>('');

const upload = () => {
    // loading indicator
    loading.value = true;
    feedback.value = 'Uploading...';
    // add "uploading" feedback
    axios
        .post(
            `/api/resume`,
            { resume: file.value },
            {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    Authorization: `Bearer ${window.Laravel.api_token}`,
                },
            },
        )
        .then((response: AxiosResponse) => {
            feedback.value = 'Uploaded, parsing file...';
            resume.value.id = response.data.resume.id;
            parseUploadedFile();
        });
};

const parseUploadedFile = () => {
    axios
        .post(
            `/api/parser/${resume.value.id}`,
            {},
            {
                headers: { Authorization: `Bearer ${window.Laravel.api_token}` },
            },
        )
        .then((response: AxiosResponse) => {
            feedback.value = 'File parsed successfully';
            resume.value.content = response.data.content as string;
            loading.value = false;
        });
};

const humanReadableSize = (size: number): string => {
    if (size <= 1024) {
        return size + 'b';
    } else if (size <= 1024 * 1024) {
        return (size / 1024).toFixed(1) + 'kb';
    }

    return (size / 1024 / 1024).toFixed(1) + 'mb';
};

const handleFileSelected = (e: any) => {
    file.value = e.target.files[0];
    file.value.humanReadableSize = humanReadableSize(e.target.files[0].size);
};

const resetForm = () => {
    file.value = {} as FileUpload;
    resume.value.content = '';
};

const optimize = () => {
    loading.value = true;
    axios
        .post(
            `/api/optimize/${resume.value.id}`,
            {
                role_details: resume.value.role_details,
            },
            {
                headers: { Authorization: `Bearer ${window.Laravel.api_token}` },
            },
        )
        .then((response: AxiosResponse) => {
            feedback.value = 'Completed!';
            resume.value.optimized_content = response.data.response as string;
            resume.value.reasoning = response.data.reasoning as string;
            loading.value = false;
        });
};

/* Credits: https://stackoverflow.com/a/75039478/29766047 */
const downloadPDF: (id: number) => Promise<void> = (id: number) => {
    return axios({
        method: 'post',
        url: `/api/download-optimized/${id}`,
        headers: {
            Authorization: `Bearer ${window.Laravel.api_token}`,
            'Content-Type': 'application/json',
        },
        responseType: 'blob',
    }).then(function (response) {
        const a = document.createElement('a');
        a.href = window.URL.createObjectURL(response.data);
        a.download = `resume.pdf`;
        document.body.appendChild(a);
        a.click();
        a.remove();
    });
};

const printOptimizedResume = async () => {
    loading.value = true;

    downloadPDF(resume.value.id).then(() => {
        loading.value = false;
    });
};

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex h-full w-[595px] flex-1 flex-col gap-4 rounded-xl p-4">
            <div v-if="resume.optimized_content.length" class="col-span-full w-full md:w-[595px]">
                <span class="mt-2 block text-sm/6 font-medium dark:text-white">Reasoning:</span>
                <p class="max-h-72 w-full max-w-full overflow-y-auto text-wrap py-2 text-sm leading-6">
                    {{ resume.reasoning }}
                </p>

                <div class="flex items-start justify-between">
                    <span class="mx-auto mt-2 block flex-1 overflow-y-auto text-sm/6 font-medium dark:text-white">Result:</span>
                    <button
                        @click="printOptimizedResume"
                        class="flex items-center justify-center gap-2 rounded px-3 py-1 text-sm hover:text-indigo-500 hover:underline"
                        type="button"
                    >
                        Download
                        <InboxArrowDownIcon class="size-4" aria-hidden="true" />
                    </button>
                </div>
                <div class="mx-auto max-h-[842px] overflow-y-auto border">
                    <div
                        class="prose prose-stone
    prose-ul:my-1 prose-li:my-0
    prose-h1:mt-4 prose-h1:mb-4 prose-h2:mt-6 prose-h2:mb-2
    prose-lead:leading-normal text-sm
    prose-hr:border-gray-400 text-wrap rounded px-4 py-2 dark:bg-gray-300 dark:p-4"
                        v-html="resume.optimized_content"
                    ></div>
                </div>
            </div>
            <div v-else class="col-span-full w-full md:w-[595px]">
                <label for="upload-resume" class="block text-sm/6 font-medium dark:text-white">Your Resume</label>
                <input ref="uploadFile" @change="handleFileSelected" type="file" id="upload-resume" class="hidden" />
                <div class="mt-2 flex items-center gap-x-3">
                    <DocumentIcon class="size-12 text-gray-500" aria-hidden="true" />
                    <div v-if="!file.name">
                        <label
                            for="upload-resume"
                            class="cursor-pointer rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
                            >Upload Resume</label
                        >
                    </div>
                    <div v-else class="flex flex-col items-start justify-start">
                        <span>Name: <span v-text="file.name"></span></span>
                        <span>Size: <span v-text="file.humanReadableSize"></span></span>
                    </div>
                </div>
                <span class="mt-2 block text-sm/6 font-medium text-white" v-if="resume.content.length">Parsed Content:</span>
                <pre class="max-h-72 w-full overflow-auto py-2 text-sm" v-if="resume.content.length">{{ resume.content }}</pre>
                <div class="my-2" v-if="resume.content.length">
                    <label class="flex items-center gap-2 dark:text-white">
                        <Switch
                            v-model="addRoleInfo"
                            :class="[
                                addRoleInfo ? 'bg-indigo-300' : 'bg-gray-300 dark:bg-gray-700',
                                'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                            ]"
                        >
                            <span class="sr-only">Add role information</span>
                            <span
                                aria-hidden="true"
                                :class="[
                                    addRoleInfo ? 'translate-x-5' : 'translate-x-0',
                                    'pointer-events-none inline-block size-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                ]"
                            />
                        </Switch>
                        <span>Add role information</span>
                    </label>
                </div>
                <div v-if="addRoleInfo" class="col-span-full">
                    <label for="about" class="block text-sm/6 font-medium dark:text-white">Role Information</label>
                    <div class="mt-2">
                        <textarea
                            v-model="resume.role_details"
                            id="about"
                            rows="3"
                            class=":dark:placeholder:text-gray-500 block w-full rounded-md bg-white/5 px-3 py-1.5 text-base outline outline-1 -outline-offset-1 outline-gray-400 placeholder:text-gray-700 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 dark:text-white dark:outline-white/10 sm:text-sm/6"
                        />
                    </div>
                    <p class="mt-3 text-sm/6 text-gray-400">
                        Please paste here the role information so the resume can be tailored to better match the role you are applying for
                    </p>
                </div>

<!--                <pre class="max-h-72 w-full overflow-auto py-2 text-sm" v-if="resume.optimized_content.length" v-text="resume.optimized_content"></pre>-->

                <div class="mt-6 flex items-center justify-center gap-x-6">
                    <button type="reset" @click="resetForm" class="text-sm/6 font-semibold dark:text-white">Cancel</button>
                    <button
                        v-if="!resume.content.length"
                        type="button"
                        @click="upload"
                        class="rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
                        :class="{ 'disabled bg-indigo-300': loading, 'bg-indigo-500': !loading }"
                    >
                        Upload
                    </button>
                    <button
                        v-if="resume.content.length"
                        type="button"
                        @click="optimize"
                        class="rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
                        :class="{ 'disabled bg-indigo-300': loading, 'bg-indigo-500': !loading }"
                    >
                        Optimize
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
