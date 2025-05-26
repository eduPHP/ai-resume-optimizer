<script setup lang="ts">
    import { UploadIcon } from 'lucide-vue-next';
    import { useResumeWizardStore } from '@/stores/ResumeWizardStore';
    import { Buttons } from '@/components/ui/steps';
    import { useForm, usePage } from '@inertiajs/vue3';
    import { onBeforeMount, ref } from 'vue';
    import { Axios, uploadResume } from '@/lib/axios';
    import InputError from '@/components/InputError.vue';

    const state = useResumeWizardStore()

    type Resume = {
        id: number,
        name: string,
        created: string,
    }

    const resumes = ref<Resume[]>([])
    const uploadedFile = ref<File | null>(null)

    onBeforeMount(() => {
        Axios(usePage()).get('/api/resumes').then(response => {
            resumes.value = response.data as Resume[]
            state.form.resume.id = response.data[0]?.id
        })
    })

    const submit = () => {
        state.loading = true
        // form.post(route('optimizations.store'), {
        //     headers: {
        //         'X-CurrentStep': state.step.toString(),
        //     },
        //     onSuccess: () => {
        //         state.form.resume = form.data()
        //         state.nextStep()
        //     },
        //     onFinish: () => {
        //         state.loading = false
        //     }
        // });
    }

    type FileUploadEvent = Event & {
        target: HTMLInputElement & {
            files: FileList
        }
    }

    const handleResumeSubmit = (e: FileUploadEvent) => {
        state.clearErrors()

        const uploadedResume: File | null = e.target.files.item(0)

        if (!uploadedResume) {
            state.setError('upload', 'Please select a resume, allowed formats are .pdf and .docx')
            return
        }


        uploadResume(usePage(), state, uploadedResume).then(response => {
            resumes.value.unshift(response.data as Resume);
            state.form.resume.id = response.data.id;
        });

    }
</script>

<template>
    <div class="bg-gray-300/10 dark:bg-[#202020] px-8 py-6">
        <h3 class="text-xl mb-6">Your Resume</h3>
        <p class="text-gray-400">Select a previously uploaded resume or upload a new one</p>
        <div class="py-6">

            <ul>
                <li v-for="resume in resumes" :key="resume.id" class="mb-4">
                    <label class="flex items-center gap-4 text-lg cursor-pointer">
                        <input @input="state.clearErrors('id')" accept="application/msword, application/pdf" type="radio" name="resume" :value="resume.id" v-model="state.form.resume.id" class="w-6 h-6">
                        <span class="flex flex-col leading-none">
                            <span>{{ resume.name }}</span>
                            <span class="text-xs text-gray-400">Sent on {{ resume.created }}</span>
                        </span>
                    </label>
                </li>
            </ul>
            <InputError :message="state.form.errors.id" />

            <label class="mt-6 flex flex-col gap-4 items-center justify-center py-12  dark:bg-[#363636] rounded-md border border-dashed cursor-pointer" :class="{
                ' border-white/30 text-gray-400': ! state.form.errors.upload,
                ' border-red-400 text-red-400': !! state.form.errors.upload,
            }">
                <UploadIcon size="50" />
                Drag or click here to upload a new resume.
                <input type="file" class="sr-only" @input="handleResumeSubmit">
            </label>
            <InputError :message="state.form.errors.upload" />
        </div>

    </div>

    <Buttons :action="submit" />
</template>
