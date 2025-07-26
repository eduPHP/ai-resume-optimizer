<script lang="ts" setup>

import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { reactive } from 'vue';
import { updateUserAISettings } from '@/lib/axios';
import { usePage } from '@inertiajs/vue3'
import { AISettings, SharedData } from '@/types';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import HeadingSmall from '@/components/HeadingSmall.vue';

const page = usePage<SharedData>();

const settings = reactive<AISettings>(page.props.auth.user.ai_settings)

const handleSaveAISettings = () => {
    updateUserAISettings(settings)
}

</script>

<template>
    <div>
        <HeadingSmall title="Compatibility thresholds" description="These values define the compatibility score threshold, this is mostly a visual feedback but the 'Top Choice' option will define wether it generates a top choice text or not." />

        <div class="my-8">
            <div>
                <Label class="w-52 text-right">Top Choice</Label>
                <Input type="number" class="w-32" v-model="settings.compatibilityScoreLevels.top" name="Top" />
            </div>
            <div>
                <Label class="w-52 text-right">High Compatibility</Label>
                <Input type="number" class="w-32" v-model="settings.compatibilityScoreLevels.high" name="High" />
            </div>
            <div>
                <Label class="w-52 text-right">Regular Compatibility</Label>
                <Input type="number" class="w-32" v-model="settings.compatibilityScoreLevels.medium" name="Medium" />
            </div>
            <div>
                <Label class="w-52 text-right">Low Compatibility</Label>
                <Input type="number" class="w-32" v-model="settings.compatibilityScoreLevels.low" name="Low" />
            </div>
        </div>
        <HeadingSmall title="AI Instructions" description="Add custom instructions for optimizations." />

        <Textarea v-model="settings.instructions" auto-grow placeholder="i.e. Lower the compatibility score by 20 points if the company does not offer free hugs!" />

        <div class="mt-4 flex justify-end">
            <Button type="button" @click="handleSaveAISettings">Save</Button>
        </div>
    </div>
</template>
