<template>
    <Head :title="'Create ' + string_change.product + ' Category'" />

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
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0"> {{ string_change.product }} Category Create</p>

                        <div class="flex-shrink-0 flex space-x-3">
                        </div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="space-y-4 sm:space-y-6 px-5 py-6">
                            <div class="max-w-xl mx-auto">
                                <div class="grid grid-cols-4 gap-4">
                                    <div class="col-span-4 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Cooker Type </label>
                                        <Listbox class="mt-1" v-model="form.type" :items="types" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-2">
                                        <div class="relative flex items-center mt-8 justify-end">
                                            <div class="flex items-center h-5">
                                                <input type="checkbox" id="is-platter" v-model="form.is_platter" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="is-platter" class="font-medium text-gray-700 cursor-pointer">Is This Platter?</label>
                                                <InputError :message="$page.props.errors.is_platter" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-span-4 sm:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700"> Name <span class="text-red-500">*</span> </label>
                                        <input v-model="form.name" placeholder="Name" type="text" class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                        <InputError :message="$page.props.errors.name" />
                                    </div>
                                </div>
                            </div>
							
                            <hr class="my-4">

                            <div class="max-w-xl mx-auto">
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div class="mt-6 flex space-x-3 md:mt-0">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                            <PencilSquareIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                            Update
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
import Listbox from '@/Components/Listbox.vue';

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
        Listbox,
        Head,
        InputError,
        Link,

        PlusIcon,
        PencilSquareIcon,
    },

    props:{
        string_change: Object,

        product_categories: Array,
        types: Array,
    },

    setup (props) {
        const breadcrumbs = [
            { name: props.string_change.product_s + ' Categories', href: route('product_category.index'), current: false },
            { name: 'Create Page', href: '#', current: false },
        ]

        const form = reactive({
            product_category_id: null,
            name: null,
            is_platter: false,
            type: 'chef',
        })

        function submit() {
            router.post(route('product_category.store'), form);
        }

        return {
            breadcrumbs,
            form,
            submit
        }
    }
}
</script>
