<script setup>
import { ref, watch } from 'vue'
import { CheckCircleIcon, XCircleIcon, XMarkIcon } from '@heroicons/vue/24/solid'
import { CheckIcon } from '@heroicons/vue/24/outline'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const showSuccess = ref(page.props.alertMessage?.success ? true : false)
const showFail = ref(page.props.alertMessage?.fail ? true : false)
const showValidationError = ref(Object.keys(page.props.errors).length ? true : false)

function close() {
    showSuccess.value = false
    showFail.value = false
    showValidationError.value = false
}

watch(
    () => page.props.alertMessage,
    (newVal) => {
        showSuccess.value = newVal.success ? true : false
        showFail.value = newVal.fail ? true : false

        if (showSuccess.value) {
            // setTimeout(() => close(), 10000);
        }
    }
)

watch(
    () => page.props.errors,
    (newVal) => {
        showValidationError.value = Object.keys(newVal).length ? true : false
    }
)
</script>
<template>
    <div v-if="showSuccess" class="rounded bg-green-50 p-4">
        <div class="flex">
            <div class="shrink-0">
                <CheckCircleIcon class="h-5 w-5 text-green-400" aria-hidden="true" />
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                    {{ page.props.alertMessage?.success }}
                </p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button
                        @click="close"
                        type="button"
                        class="inline-flex bg-green-50 rounded p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600">
                        <span class="sr-only">Dismiss</span>
                        <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div v-if="showFail" class="rounded bg-red-50 p-4">
        <div class="flex">
            <div class="shrink-0">
                <XCircleIcon class="h-5 w-5 text-red-400" aria-hidden="true" />
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">
                    {{ page.props.alertMessage?.fail }}
                </p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button
                        type="button"
                        @click="close"
                        class="inline-flex bg-red-50 rounded p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600">
                        <span class="sr-only">Dismiss</span>
                        <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div v-if="showValidationError" class="rounded bg-red-50 p-4">
        <div class="flex">
            <div class="shrink-0">
                <XCircleIcon class="h-5 w-5 text-red-400" aria-hidden="true" />
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">There were {{ Object.keys(page.props.errors).length }} errors with your submission</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul role="list" class="list-disc pl-5 space-y-1">
                        <li v-for="error in page.props.errors" :key="error">
                            {{ error }}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button
                        type="button"
                        @click="close"
                        class="inline-flex bg-red-50 rounded p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600">
                        <span class="sr-only">Dismiss</span>
                        <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
