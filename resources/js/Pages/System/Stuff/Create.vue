<template>
    <Head title="Create Table" />

    <div>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>
                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                    </div>
                </div>
            </div>
        </div>

        <div class="py-5">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">Table Create</p>

                        <div class="flex-shrink-0 flex space-x-3">
                        </div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="space-y-4 py-6 px-5">
                            <div class="grid sm:grid-cols-3 sm:gap-2 sm:px-6">
                                <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8">
                                    <label>Branch</label> <span class="text-red-500">*</span>
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
									<Combobox class="mt-1" v-model="form.branch_id" :items="$page.props.branches" />
									<InputError :message="$page.props.errors.branch_id" />
                                </dd>
                            </div>

                            <div class="grid sm:grid-cols-3 sm:gap-2 sm:px-6">
                                <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8">
                                    <label>Name</label> <span class="text-red-500">*</span>
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                    <input v-model="form.name" type="text" placeholder="Name" class="focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent block w-full px-4 sm:text-sm border-gray-300 rounded">
                                    <InputError :message="$page.props.errors.name" />
                                </dd>
                            </div>

                            <div class="grid sm:grid-cols-3 sm:gap-2 sm:px-6">
                                <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8">
                                    <label>Description</label>
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <textarea v-model="form.description" type="text" rows="3" placeholder="Description" class="focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent block w-full px-4 sm:text-sm border-gray-300 rounded" />
                                    <InputError :message="$page.props.errors.description" />
                                </dd>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"> <div class="w-full border-t border-gray-300" /> </div>
                                <div class="relative flex justify-center ml-4"> <span class="px-3 bg-white text-lg font-medium text-gray-900"></span> </div>
                            </div>

                            <div class="grid sm:grid-cols-3 sm:gap-2 sm:px-6">
                                <dt class="text-sm leading-5 font-semibold text-gray-500"></dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div class="mt-6 flex space-x-3 md:mt-0">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                            <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                            Create
                                        </button>
                                    </div>
                                </dd>
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

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Alert from '@/Components/Alert.vue';
import InputError from '@/Components/InputError.vue';
import Combobox from '@/Components/Combobox.vue';

import {
    PlusIcon,
} from '@heroicons/vue/24/solid';

import {
    PencilSquareIcon,
} from '@heroicons/vue/24/outline';

export default {
    layout: AuthenticatedLayout,

    components: {
        Alert,
        Breadcrumb,
        Head,
        InputError,
        Link,
		Combobox,

        PlusIcon,
        PencilSquareIcon,
    },

    setup () {
        const breadcrumbs = [
            { name: 'Tables', href: route('stuff.index'), current: false },
            { name: 'Create Page', href: '#', current: false },
        ]

        const form = reactive({
            name: null,
            description: null,
            branch_id: null,
        })

        function submit() {
            router.post(route('stuff.store'), form);
        }

        return {
            breadcrumbs,
            form,
            submit
        }
    }
}
</script>
