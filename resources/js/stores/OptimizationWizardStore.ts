import { defineStore } from 'pinia'
import { AdditionalInformation, Completed, RoleInformation, YourResume } from '@/components/ui/steps';
import { Component } from 'vue';

type Step = {
    name: string;
    stepComponent: Component;
}

export type State = {
    step: number;
    latestStep: number;
    loading: boolean;
    optimizing: boolean;
    windowWidth: number;
    steps: Step[];
    form: Form;
}

export type RoleForm = {
    name: string;
    company: string;
    description: string;
    url: string;
    location: string;
}

export type Resume = {
    id: number,
    name: string,
    created: string,
}

type Alignment = {
    title: string;
    description: string;
}

type AIResponse = {
    resume: string;
    compatibility_score: number;
    professional_summary: string;
    cover_letter: string[];
    strong_alignments: Alignment[];
    moderate_gaps: Alignment[];
    missing_requirements: Alignment[];
    reasoning: string;
    top_choice?: string;
}

export type OptimizationType = {
    id?: string;
    role_name: string;
    role_description: string;
    role_company: string;
    role_url: string;
    resume_id?: number;
    role_location?: string;
    make_grammatical_corrections: boolean;
    change_professional_summary: boolean;
    generate_cover_letter: boolean;
    change_target_role: boolean;
    mention_relocation_availability: boolean;
    current_step: number;
    status: string;
    ai_response: AIResponse;
}

export type AdditionalInformationForm = {
    makeGrammaticalCorrections: boolean;
    changeProfessionalSummary: boolean;
    generateCoverLetter: boolean;
    changeTargetRole: boolean;
    mentionRelocationAvailability: boolean;
    targetCountry: string;
}

export type Form = {
    optimizationId: string | undefined;
    status: string;
    role: RoleForm;
    resume: {
        id: number | undefined;
    };
    additional: AdditionalInformationForm;
    errors: {
        [key: string]: string | undefined;
    },
    response: AIResponse;
}

export const useOptimizationWizardStore = defineStore('resume-wizard', {
    state: (): State => ({
        step: 0,
        latestStep: 0,
        loading: false,
        optimizing: false,
        windowWidth: 4000,
        steps: [
            {
                name: 'Role Information',
                stepComponent: RoleInformation,
            },
            {
                name: 'Your Resume',
                stepComponent: YourResume,
            },
            {
                name: 'Additional Information',
                stepComponent: AdditionalInformation,
            },
            {
                name: 'Complete',
                stepComponent: Completed,
            },
        ],

        form: {
            status: 'pending',
            optimizationId: undefined,
            role: {
                company: '',
                description: '',
                name: '',
                url: '',
                location: '',
            },
            resume: { id: undefined },
            additional: {
                changeProfessionalSummary: true,
                changeTargetRole: true,
                makeGrammaticalCorrections: true,
                generateCoverLetter: true,
                mentionRelocationAvailability: true,
                targetCountry: ''
            },
            errors: {},
            response: {} as AIResponse,
        },
    }),

    getters: {
        currentStep: (state: State): Step => {
            return state.steps[state.step]
        },
        pageTitle: (state: State) => {
            if (state.form.role.company && state.form.role.name) {
                return `${state.form.role.company} - ${state.form.role.name} - Resume Optimization`
            }

            return 'New Optimization'
        },
        compatibilityStyle: (state: State): string => {
            if (state.form.status === 'complete') {
                const score = state.form.response.compatibility_score;
                switch (true) {
                    case score < 90 && score > 84:
                        return 'text-yellow-400'
                    case score < 85:
                        return 'text-red-400'
                }
            }

            return 'text-green-400 text-xl'
        },
    },

    actions: {
        clearErrors(errors: string[]|string = []) {
            if (errors.length === 0) {
                this.form.errors = {};
            }

            if (typeof errors === 'string') {
                this.form.errors[errors] = undefined;
                return;
            }

            for (const error in errors) {
                delete this.form.errors[error];
            }
        },
        setError(key: string, message: string) {
            this.form.errors[key] = message;
        },
        setOptimization(optimization: OptimizationType) {
            this.step = optimization.current_step || 0;
            this.latestStep = optimization.current_step || 0;
            this.form.optimizationId = optimization.id;
            this.form.role.url = optimization.role_url;
            this.form.role.name = optimization.role_name;
            this.form.role.description = optimization.role_description;
            this.form.role.company = optimization.role_company;
            this.form.role.location = optimization.role_location ?? '';
            this.form.resume.id = optimization.resume_id;
            this.form.additional.targetCountry = optimization.role_location ?? '';
            this.form.additional.makeGrammaticalCorrections = optimization.make_grammatical_corrections ?? true;
            this.form.additional.changeProfessionalSummary = optimization.change_professional_summary ?? true;
            this.form.additional.generateCoverLetter = optimization.generate_cover_letter ?? true;
            this.form.additional.changeTargetRole = optimization.change_target_role ?? true;
            this.form.additional.mentionRelocationAvailability = optimization.mention_relocation_availability ?? true;
            if (this.form.status !== 'editing') {
                this.form.status = optimization.status ?? 'draft';
            }
            this.form.response = optimization.ai_response ?? {} as AIResponse;
        },
        nextStep() {
            this.step++
            this.latestStep = this.step;
        },
        prevStep() {
            this.step--
        },
        setStep(step: number) {
            if (step <= this.latestStep) {
                this.step = step
            }
        },
        finish() {
            alert('done')
        },
    },
})

export type OptimizationWizardStore = ReturnType<typeof useOptimizationWizardStore>
