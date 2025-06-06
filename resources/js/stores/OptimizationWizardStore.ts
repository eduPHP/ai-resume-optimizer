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
    pageTitle: string;
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
}

export type OptimizationType = {
    id?: string;
    role_name: string;
    role_description: string;
    role_company: string;
    resume_id?: number;
    role_location?: string;
    make_grammatical_corrections: boolean;
    change_professional_summary: boolean;
    change_target_role: boolean;
    mention_relocation_availability: boolean;
    current_step: number;
    status: string;
    ai_response: AIResponse;
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
        pageTitle: 'New Optimization',
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
            },
            resume: { id: undefined },
            additional: {
                changeProfessionalSummary: true,
                changeTargetRole: true,
                makeGrammaticalCorrections: true,
                mentionRelocationAvailability: false,
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
            this.form.role.name = optimization.role_name;
            this.form.role.description = optimization.role_description;
            this.form.role.company = optimization.role_company;
            this.form.resume.id = optimization.resume_id;
            this.form.additional.targetCountry = optimization.role_location ? optimization.role_location : '';
            this.form.additional.makeGrammaticalCorrections = optimization.make_grammatical_corrections;
            this.form.additional.changeProfessionalSummary = optimization.change_professional_summary;
            this.form.additional.changeTargetRole = optimization.change_target_role;
            this.form.additional.mentionRelocationAvailability = optimization.mention_relocation_availability;
            this.form.status = optimization.status;
            this.form.response = optimization.ai_response;
            if (optimization.id) {
                this.pageTitle = optimization.role_company + ' - ' + optimization.role_name + ' - Resume Optimization'
            }
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
