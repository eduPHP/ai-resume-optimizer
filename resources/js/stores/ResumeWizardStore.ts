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
    steps: Step[];
    form: Form;
}

export type RoleForm = {
    name: string;
    company: string;
    description: string;
}

export type Resume = {
    id: number,
    name: string,
    created: string,
}

export type Optimization = {
    id?: string;
    role_name: string;
    role_description: string;
    role_company: string;
    resume_id?: number;
}

export type AdditionalInformationForm = {
    makeGrammaticalCorrections: boolean;
    changeProfessionalSummary: boolean;
    changeTargetRole: boolean;
    mentionRelocationAvailability: boolean;
    targetCountry: string;
}

export type Form = {
    optimizationId: string | undefined;
    role: RoleForm;
    resume: {
        id: number | undefined;
    };
    additional: AdditionalInformationForm;
    errors: {
        [key: string]: string | undefined;
    }
}

export const useResumeWizardStore = defineStore('resume-wizard', {
    state: (): State => ({
        step: 2,
        latestStep: 2,
        loading: false,
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
            optimizationId: undefined,
            role: {
                company: '',
                description: '',
                name: '',
            },
            resume: { id: undefined },
            additional: {
                changeProfessionalSummary: true,
                changeTargetRole: true,
                makeGrammaticalCorrections: true,
                mentionRelocationAvailability: false,
                targetCountry: ''
            },
            errors: {}
        },
    }),


    getters: {
        currentStep: (state: State): Step => {
            return state.steps[state.step]
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
        setOptimization(optimization: Optimization) {
            this.form.optimizationId = optimization.id;
            this.form.role.name = optimization.role_name;
            this.form.role.description = optimization.role_description;
            this.form.role.company = optimization.role_company;
            this.form.resume.id = optimization.resume_id;
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
