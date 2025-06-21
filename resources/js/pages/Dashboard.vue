<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { useToastsStore } from '@/stores/ToastsStore';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-3xl mx-auto p-8">
            <h1 class="text-3xl font-bold mb-4 text-center xl:text-left">Why You Should Tailor Your Resume for Each Role</h1>
            <p class="text-lg mb-4 text-justify xl:text-left">
                A resume should be tailored for each role to significantly increase the chances of catching the attention of hiring managers and applicant tracking systems (ATS). Every job posting highlights a unique combination of skills, qualifications, and experiences that the employer is seeking. When you customize your resume to align closely with those specific requirements, you demonstrate that you’ve taken the time to understand the role and that you’re a strong, relevant candidate.
            </p>
            <div class="pb-4 w-full flex justify-center">
                <Button as="a" size="lg" :href="route('optimizations.create')">Generate a New Optimized Resume</Button>
            </div>
            <h2 class="text-2xl font-semibold mb-3 text-center xl:text-left">Benefits of Tailoring Your Resume</h2>
            <ul class="list-disc pl-5 space-y-2 mb-6">
                <li><strong>Highlight the most relevant experience:</strong> Align your accomplishments and responsibilities with the job description to show your suitability for the role.</li>
                <li><strong>Stand out from generic applications:</strong> Customized resumes show initiative and attention to detail, making you more memorable.</li>
                <li><strong>Improve keyword match for ATS:</strong> Tailoring increases your chances of passing software that screens for job-specific keywords.</li>
                <li><strong>Showcase your value:</strong> Emphasize how your past experiences contribute directly to the success of the company in this specific role.</li>
            </ul>
            <p class="text-lg text-justify xl:text-left">
                In short, tailoring your resume is a strategic move that not only increases your chances of getting an interview but also positions you as the best possible fit for the job.
            </p>
        </div>
    </AppLayout>
</template>
