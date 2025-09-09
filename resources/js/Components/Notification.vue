<script setup>
import { ref, watch } from 'vue'
import { XCircleIcon, XMarkIcon } from '@heroicons/vue/24/solid'
import { CheckIcon } from '@heroicons/vue/24/outline'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const props = defineProps({
    position: String
})

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
    <div v-if="showSuccess" aria-live="assertive" class="fixed inset-0 flex items-end px-4 py-6 pointer-events-none sm:p-6 z-10">
        <div class="w-full flex flex-col items-start space-y-4">
            <!-- Notification panel, dynamically insert this into the live region when it needs to be displayed -->
            <transition
                enter-active-class="transform ease-out duration-300 transition"
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0">
                <div class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="mx-auto flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0">
                                <CheckIcon class="h-6 w-6 text-green-600" aria-hidden="true" />
                            </div>
                            <div class="ml-5 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-green-600">Successful!</p>
                                <p class="mt-1 text-sm text-gray-600" v-html="page.props.alertMessage?.success"></p>
                            </div>
                            <div class="ml-4 shrink-0 flex">
                                <button
                                    @click="close"
                                    class="bg-transparent rounded-md inline-flex text-green-800 hover:scale-125 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <span class="sr-only">Close</span>
                                    <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>

    <div v-if="showFail" aria-live="assertive" class="fixed inset-0 flex items-end px-4 py-6 pointer-events-none sm:p-6 z-10">
        <div class="w-full flex flex-col items-start space-y-4">
            <transition
                enter-active-class="transform ease-out duration-300 transition"
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0">
                <div class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="mx-auto flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0">
                                <XMarkIcon class="h-6 w-6 text-red-600" aria-hidden="true" />
                            </div>
                            <div class="ml-5 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-red-600">Failed!</p>
                                <p class="mt-1 text-sm text-gray-600" v-html="page.props.alertMessage?.fail"></p>
                            </div>
                            <div class="ml-4 shrink-0 flex">
                                <button
                                    @click="close"
                                    class="bg-transparent rounded-md inline-flex text-red-400 hover:text-red-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <span class="sr-only">Close</span>
                                    <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>

    <div v-if="showValidationError" aria-live="assertive" class="fixed inset-0 flex items-end px-4 py-6 pointer-events-none sm:p-6 z-10">
        <div class="w-full flex flex-col items-start space-y-4">
            <transition
                enter-active-class="transform ease-out duration-300 transition"
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0">
                <div class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="mx-auto flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0">
                                <XMarkIcon class="h-6 w-6 text-red-600" aria-hidden="true" />
                            </div>
                            <div class="ml-5 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-red-600">Validation Error!</p>
                                <p v-for="error in page.props.errors" :key="error" class="mt-1 text-sm text-red-500">
                                    {{ error }}
                                </p>
                            </div>
                            <div class="ml-4 shrink-0 flex">
                                <button
                                    @click="close"
                                    class="bg-transparent rounded-md inline-flex text-red-400 hover:text-red-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <span class="sr-only">Close</span>
                                    <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>
