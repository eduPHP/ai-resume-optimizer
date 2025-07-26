<script setup lang="ts">
    import { UploadIcon } from 'lucide-vue-next';
    import { useOptimizationWizardStore, Resume } from '@/stores/OptimizationWizardStore';
    import { Buttons } from '@/components/ui/steps';
    import { onBeforeMount, ref } from 'vue';
    import { Axios, uploadResume, updateResume } from '@/lib/axios';
    import InputError from '@/components/InputError.vue';
    import Heading from '@/components/Heading.vue';

    const state = useOptimizationWizardStore()

    const resumes = ref<Resume[]>([])
    const flashSuccess = ref(false)

    onBeforeMount(() => {
        Axios().get(route('resumes.index')).then(response => {
            resumes.value = response.data as Resume[]
            state.form.resume.id = response.data[0]?.id
        })
    })

    const submit = () => {
        updateResume(state)
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

        uploadResume(state, uploadedResume).then(response => {
            resumes.value.unshift(response.data as Resume);
            state.form.resume.id = response.data.id;
            flashSuccess.value = true
            setTimeout(() => {
                flashSuccess.value = false
            }, 3000)
        });

    }
</script>

<template>
    <div class="bg-gray-300/10 dark:bg-[#202020] px-8 py-6 min-w-80">
        <Heading title="Your Resume" description="Select a previously uploaded resume or upload a new one" />
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

            <label class="mt-6 px-12 text-center flex flex-col gap-4 items-center justify-center py-12 dark:bg-[#363636] rounded-md border border-dashed cursor-pointer" :class="{
                ' border-white/30 text-gray-400': ! state.form.errors.upload,
                ' border-red-400 text-red-400': !! state.form.errors.upload,
            }">
                <UploadIcon class="transition-colors duration-1000" :class="{'motion-safe:animate-bounce': state.loading, 'text-green-400': flashSuccess}" size="50" />
                <span>Drag or click here to upload a new resume.</span>
                <input type="file" :disabled="state.loading" class="sr-only" @input="handleResumeSubmit">
            </label>
            <InputError :message="state.form.errors.upload" />
        </div>

    </div>

    <Buttons :action="submit" />
</template>
