<script lang="ts" setup>
import HeadingSmall from '@/components/HeadingSmall.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { SCORE_STYLES } from '@/stores/NavigationItemsStore'
import { SharedData } from '@/types'
import { router, useForm, usePage } from '@inertiajs/vue3'

const page = usePage<SharedData>()

const settings = useForm(page.props.auth.user.ai_settings)

const handleSaveAISettings = () => {
    settings.put(route('users.update-instructions'))
    router.reload({ only: ['user'] })
}
</script>

<template>
    <div>
        <HeadingSmall
            title="Compatibility thresholds"
            description="These values define the compatibility score threshold, this is mostly a visual feedback but the 'Top Choice' option will define wether it generates a top choice text or not."
        />

        <div class="my-2 grid grid-cols-1 space-x-2 xl:grid-cols-3">
            <div class="gap-2">
                <Label :class="SCORE_STYLES.HIGH">High</Label>
                <Input type="number" class="" :class="SCORE_STYLES.HIGH" v-model="settings.compatibilityScoreLevels.high" name="High" />
            </div>
            <div class="gap-2">
                <Label :class="SCORE_STYLES.MEDIUM">Regular</Label>
                <Input type="number" class="" :class="SCORE_STYLES.MEDIUM" v-model="settings.compatibilityScoreLevels.medium" name="Medium" />
            </div>
            <div class="gap-2">
                <Label :class="SCORE_STYLES.LOW">Low</Label>
                <Input type="number" class="" :class="SCORE_STYLES.LOW" v-model="settings.compatibilityScoreLevels.low" name="Low" />
            </div>
        </div>

        <div class="my-8">
            <div class="grid gap-2">
                <Label>Top Choice</Label>
                <Input type="number" class="w-full" v-model="settings.compatibilityScoreLevels.top" name="Top" />
            </div>
        </div>
        <HeadingSmall title="AI Instructions" description="Add custom instructions for optimizations." />

        <Textarea
            v-model="settings.instructions"
            auto-grow
            placeholder="i.e. Lower the compatibility score by 20 points if the company does not offer free hugs!"
        />

        <div class="mt-4 flex items-center gap-4">
            <Button type="button" @click="handleSaveAISettings">Save</Button>
            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
                <p v-show="settings.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
            </Transition>
        </div>
    </div>
</template>
