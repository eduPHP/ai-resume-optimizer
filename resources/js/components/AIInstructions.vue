<script lang="ts" setup>

import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { ref } from 'vue';
import { updateUserInstructions } from '@/lib/axios';
import { usePage } from '@inertiajs/vue3'

const page = usePage();

const instructions = ref<string>(page.props.auth.user.ai_instructions)

const handleSaveInstructions = () => {
    updateUserInstructions(instructions.value)
}

</script>

<template>
    <div>
        <Textarea v-model="instructions" auto-grow placeholder="i.e. Lower the compatibility score by 20 points if the company does not offer free hugs!" />

        <div class="mt-4 flex justify-end">
            <Button type="button" @click="handleSaveInstructions">Save</Button>
        </div>
    </div>
</template>
