<template>
    <Head :title="'Edit ' + string_change.product + ' Category'" />

    <div>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>
                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                        <Link :href="route('product_category.create')" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
                            <PlusIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                            Create
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-5">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0"> {{ string_change.product }} Category Edit</p>

                        <div class="flex-shrink-0 flex space-x-3">
                        </div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="space-y-4 sm:space-y-6 px-5 py-6">

                            <div class="max-w-xl mx-auto">
                                <div class="grid grid-cols-4 gap-4">
                                    <div class="col-span-4 sm:col-span-4">
                                        <label class="mb-1 block text-sm font-medium text-gray-700"> Image </label>
                                        <div class="relative h-32 w-64">
                                            <div class="flex text-sm text-gray-700 border border-gray-400 rounded-md bg-gray-50">
                                                <img class="object-cover h-32 w-64" :src="form.image" title="Click to upload image">
                                                <div class="absolute bottom-0 bg-gray-50 w-full text-center opacity-60 cursor-pointer" @click="$refs.image.click()"><span class="text-xs text-black">Click to upload</span></div>
                                                <input ref="image" type="file" class="hidden" @input="form.image_file = $event.target.files[0]" @change="form.image = onFileInput($event.target.files[0]);" accept=".png, .jpg, .jpeg" />
                                            </div>
                                            <div v-show="form.image != form.image_default" class="absolute top-0 right-0 px-0 py-0 bg-gray-50 opacity-60" title="Remove Image">
                                                <XMarkIcon class="w-5 h-5 text-red-500 hover:text-red-700 cursor-pointer" aria-hidden="true" @click="form.image = form.image_default; form.image_file = null; form.image_removed = true" />
                                            </div>
                                            <div v-show="form.image != product_category.image" class="absolute top-0 left-0 px-0 py-0 bg-gray-50 opacity-60" title="Refresh Image">
                                                <ArrowPathIcon class="w-5 h-5 text-primary-500 hover:text-primary-700 cursor-pointer" aria-hidden="true" @click="form.image = product_category.image; form.image_file = null; form.image_removed = false" />
                                            </div>
                                        </div>
                                        <InputError :message="$page.props.errors.image" />
                                    </div>

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
    XMarkIcon,
    ArrowPathIcon,
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
        XMarkIcon,
        ArrowPathIcon,
    },

    props:{

        string_change: Object,

        types: Array,
        product_category: Object,
        product_categories: Array,
    },

    methods: {
        onFileInput(file) {
            return URL.createObjectURL(file);
        },
    },

    setup (props) {
        const breadcrumbs = [
            { name: props.string_change.product_s + ' Categories', href: route('product_category.index'), current: false },
            { name: 'Edit Page', href: '#', current: false },
        ]

        const form = reactive({
            product_category_id: props.product_category.product_category_id,
            name: props.product_category.name,
            type: 'chef',
            is_platter: props.product_category.is_platter? true : false,

            image: props.product_category.image,
            image_default: props.product_category.image_default,
            image_removed: false,
        })

        function submit() {
            router.post(route('product_category.update', props.product_category.id), form)
        }

        return {
            breadcrumbs,
            form,
            submit
        }
    },
}
</script>
