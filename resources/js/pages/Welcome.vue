<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 flex flex-col items-center justify-center px-4 py-10">
        <Head title="Welcome" />

        <!-- Logo -->
        <div class="mb-8">
            <AppLogoIcon class="size-24 fill-current text-black dark:text-white" />
        </div>

        <!-- Header -->
        <h1 class="text-4xl font-bold mb-4 text-center">
            Welcome to <span class="text-indigo-600 dark:text-indigo-400">AI Resume Optimizer</span>
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-300 mb-10 text-center max-w-2xl">
            Optimize your resume with AI and stand out in every job application.
        </p>

        <!-- Content Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 w-full max-w-3xl space-y-6">
            <section>
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-2">
                    Tailored Resumes. Smarter Applications.
                </h2>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                    Our AI analyzes job descriptions and customizes your resume for each opportunity. Save time, increase your chances of getting noticed, and land interviews faster.
                </p>
            </section>

            <section>
                <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-2">
                    <li>Instantly adapt your resume to any job description.</li>
                    <li>Ensure keyword match for ATS systems.</li>
                    <li>Highlight relevant experience for maximum impact.</li>
                    <li>Boost confidence with AI-backed insights.</li>
                </ul>
            </section>

            <section class="text-center">
                <a
                    href="/dashboard"
                    class="inline-block mt-6 px-6 py-3 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition"
                >
                    Get Started
                </a>
            </section>
        </div>
    </div>
</template>
