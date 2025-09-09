<template>
    <Head title="Create Event" />

    <div>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200" >
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>
                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4"></div>
                </div>
            </div>
        </div>

        <div class="py-5">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8" >
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0" > Event Create </p>

                        <div class="flex-shrink-0 flex space-x-3"></div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="space-y-4 sm:space-y-6 px-5 py-6">
                            <div class="max-w-5xl mx-auto space-y-4 sm:space-y-6">
                                <div class="md:px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm leading-10 font-semibold text-gray-700 tracking-wider text-right pr-8" > Date <span class="text-red-500">*</span> </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1" >
                                        <input v-model="form.date" type="date" class="block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                        <InputError :message="$page.props.errors.date" />
                                    </dd>
                                </div>

                                <div class="md:px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm leading-10 font-semibold text-gray-700 tracking-wider text-right pr-8" > Name <span class="text-red-500">*</span> </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1" >
                                        <input v-model="form.name" placeholder="Name" type="text" class="block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                        <InputError :message="$page.props.errors.name" />
                                    </dd>
                                </div>

                                <div class="md:px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm leading-10 font-semibold text-gray-700 tracking-wider text-right pr-8" > Start Time <span class="text-red-500">*</span> </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1" >
                                        <input v-model="form.start_time" type="datetime-local" class="block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                        <InputError :message="$page.props.errors.start_time" />
                                    </dd>
                                </div>

                                <div class="md:px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm leading-10 font-semibold text-gray-700 tracking-wider text-right pr-8" > End Time <span class="text-red-500">*</span> </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1" >
                                        <input v-model="form.end_time" type="datetime-local" class="block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                        <InputError :message="$page.props.errors.end_time" />
                                    </dd>
                                </div>

                                <div class="md:px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm leading-10 font-semibold text-gray-700 tracking-wider text-right pr-8" > Description </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2" >
                                        <textarea v-model="form.description" type="text" placeholder="Description Here" rows="4" class="block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded"></textarea>
                                        <InputError :message="$page.props.errors.description" />
                                    </dd>
                                </div>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-gray-300" /></div>
                                <div class="relative flex justify-center"> <span class="px-3 bg-white text-lg font-medium text-gray-900"></span> </div>
                            </div>

                            <div class="max-w-5xl mx-auto space-y-4 sm:space-y-6">
                                <div class="flex justify-end">
                                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                        <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                        Create
                                    </button>
                                </div>
                            </div>
                        </dl>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { reactive } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';

import Alert from '@/Components/Alert.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InputError from '@/Components/InputError.vue';

import {
    PlusIcon
} from "@heroicons/vue/24/solid";

import {
    PencilSquareIcon
} from "@heroicons/vue/24/outline";

export default {
    layout: AuthenticatedLayout,

    components: {
        Alert,
        Breadcrumb,
        Head,
        InputError,
        Link,

        PlusIcon,
        PencilSquareIcon,
    },
    props: {

    },

    setup() {
        const breadcrumbs = [
            { name: "Events", href: route("event.index"), current: false },
            { name: "Create Page", href: "#", current: false },
        ];

        const form = reactive({
            date: null,
            start_time: null,
            end_time: null,
            name: null,
            description: null,
        });

        function submit() {
            router.post(route("event.store"), form);
        }

        return {
            breadcrumbs,
            form,
            submit,
        };
    },
};
</script>
