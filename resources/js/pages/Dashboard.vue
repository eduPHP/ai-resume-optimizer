<script setup lang="ts">
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
]
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl p-8">
            <h1 class="mb-4 text-center text-3xl font-bold xl:text-left">Why You Should Tailor Your Resume for Each Role</h1>
            <p class="text-justify text-lg xl:text-left">
                A resume should be tailored for each role to significantly increase the chances of catching the attention of hiring managers and
                applicant tracking systems (ATS). Every job posting highlights a unique combination of skills, qualifications, and experiences that
                the employer is seeking. When you customize your resume to align closely with those specific requirements, you demonstrate that you’ve
                taken the time to understand the role and that you’re a strong, relevant candidate.
            </p>
            <div class="flex w-full justify-center py-4">
                <Button as="a" size="lg" :href="route('optimizations.create')">Generate a New Optimized Resume</Button>
            </div>
            <h2 class="my-3 text-center text-2xl font-semibold xl:text-left">Benefits of Tailoring Your Resume</h2>
            <ul class="mb-6 list-disc space-y-2 pl-5">
                <li>
                    <strong>Highlight the most relevant experience:</strong> Align your accomplishments and responsibilities with the job description
                    to show your suitability for the role.
                </li>
                <li>
                    <strong>Stand out from generic applications:</strong> Customized resumes show initiative and attention to detail, making you more
                    memorable.
                </li>
                <li>
                    <strong>Improve keyword match for ATS:</strong> Tailoring increases your chances of passing software that screens for job-specific
                    keywords.
                </li>
                <li>
                    <strong>Showcase your value:</strong> Emphasize how your past experiences contribute directly to the success of the company in
                    this specific role.
                </li>
            </ul>
            <p class="text-justify text-lg xl:text-left">
                In short, tailoring your resume is a strategic move that not only increases your chances of getting an interview but also positions
                you as the best possible fit for the job.
            </p>

            <div class="flex w-full justify-center py-4">
                <Button as="a" size="lg" :href="route('optimizations.create')">Generate a New Optimized Resume</Button>
            </div>

            <div class="mt-8 flex w-full items-center justify-center gap-2">
                <img class="size-10 dark:invert" src="data:image/svg+xml,%3C?xml version='1.0' encoding='utf-8'?%3E%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'%3E%3Cg id='XMLID_273_'%3E%3Cg id='XMLID_78_'%3E%3Cpath id='XMLID_83_' class='st0' d='M304.8,0H95.2C42.6,0,0,42.6,0,95.2v209.6C0,357.4,42.6,400,95.2,400h209.6 c52.6,0,95.2-42.6,95.2-95.2V95.2C400,42.6,357.4,0,304.8,0z M106.3,375C61.4,375,25,338.6,25,293.8c0-44.9,36.4-81.3,81.3-81.3 c44.9,0,81.3,36.4,81.3,81.3C187.5,338.6,151.1,375,106.3,375z M293.8,375c-44.9,0-81.3-36.4-81.3-81.3 c0-44.9,36.4-81.3,81.3-81.3c44.9,0,81.3,36.4,81.3,81.3C375,338.6,338.6,375,293.8,375z'/%3E%3C/g%3E%3Cg id='XMLID_67_' class='st2'%3E%3Cpath id='XMLID_74_' class='st3' d='M304.8,0H95.2C42.6,0,0,42.6,0,95.2v209.6C0,357.4,42.6,400,95.2,400h209.6 c52.6,0,95.2-42.6,95.2-95.2V95.2C400,42.6,357.4,0,304.8,0z M106.3,375C61.4,375,25,338.6,25,293.8c0-44.9,36.4-81.3,81.3-81.3 c44.9,0,81.3,36.4,81.3,81.3C187.5,338.6,151.1,375,106.3,375z M293.8,375c-44.9,0-81.3-36.4-81.3-81.3 c0-44.9,36.4-81.3,81.3-81.3c44.9,0,81.3,36.4,81.3,81.3C375,338.6,338.6,375,293.8,375z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E">
                <span class="font-bold text-2xl">User Scripts</span>
            </div>

            <p class="mt-2 text-muted-foreground">Use these scripts through the <a target="_blank" class="underline underline-offset-4 hover:text-blue-500 dark:hover:text-blue-400"
                href="https://www.tampermonkey.net">Tampermonkey extension</a> to quickly fetch job board information, just click "Optimize Resume" and paste the automatically copied content on the URL field.</p>

            <div class="flex w-full justify-center py-4 gap-4 flex-wrap">
                <a target="_blank" href="https://github.com/eduPHP/ai-resume-optimizer/raw/refs/heads/main/resources/userscripts/geek-hunter-job-extractor.user.js">GeekHunter</a>
                <a target="_blank" href="https://github.com/eduPHP/ai-resume-optimizer/raw/refs/heads/main/resources/userscripts/glassdoor-job-extractor.user.js">GlassDoor</a>
                <a target="_blank" href="https://github.com/eduPHP/ai-resume-optimizer/raw/refs/heads/main/resources/userscripts/indeed-job-extractor.user.js">Indeed</a>
                <a target="_blank" href="https://github.com/eduPHP/ai-resume-optimizer/raw/refs/heads/main/resources/userscripts/greenhouse-job-extractor.user.js">GreenHouse</a>
                <a target="_blank" href="https://github.com/eduPHP/ai-resume-optimizer/raw/refs/heads/main/resources/userscripts/jobleads-job-extractor.user.js">JobLeads</a>
                <a target="_blank" href="https://github.com/eduPHP/ai-resume-optimizer/raw/refs/heads/main/resources/userscripts/seek-job-extractor.user.js">Seek</a>
            </div>


        </div>
    </AppLayout>
</template>
