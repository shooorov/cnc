<template>
    <Head title="Create Product" />

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
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">{{ string_change.product }} Create</p>

                        <div class="flex-shrink-0 flex space-x-3">
                        </div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="space-y-4 sm:space-y-6 px-5 py-6">

                            <div class="max-w-xl mx-auto">
                                <div class="grid grid-cols-4 gap-4">

                                    <div class="col-span-0 sm:col-span-1"></div>
                                    <div class="col-span-4 sm:col-span-2">
                                        <label class="mb-1 text-sm font-medium text-gray-700 flex justify-between">
                                            <span>Categories <span class="text-red-500">*</span></span>
                                            <Link :href="route('product_category.create')">
                                                <PlusIcon class="-ml-1 mr-2 h-5 w-5 text-gray-800" aria-hidden="true" />
                                            </Link>
                                        </label>
                                        <Listbox class="mt-1" v-model="form.product_category_id" :items="categories" />
                                        <InputError :message="$page.props.errors.product_category_id" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700"> Name <span class="text-red-500">*</span> </label>
                                        <input v-model="form.name" placeholder="Name" type="text" class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                        <InputError :message="$page.props.errors.name" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700"> English Name </label>
                                        <input v-model="form.english_name" placeholder="English Name" type="text" class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                        <InputError :message="$page.props.errors.english_name" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Code <span class="text-red-500">*</span> </label>
                                        <input v-model="form.code" placeholder="Code" type="text" class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                        <InputError :message="$page.props.errors.code" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Total </label>
                                        <div class="relative">
                                            <input v-model="form.total" placeholder="Total" type="text" readonly class="mt-1 block w-full px-4 pr-12 focus:outline-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded">
                                            <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                        </div>
                                        <InputError :message="$page.props.errors.total" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Rate / Sale Price </label>
                                        <div class="relative">
                                            <input v-model="form.rate" @change="total" @keyup="total" placeholder="Rate / Sale Price" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off" class="mt-1 block w-full px-4 pr-12 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                            <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                        </div>
                                        <InputError :message="$page.props.errors.rate" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Discount </label>
                                        <div class="relative">
                                            <input v-model="form.discount" @change="total" @keyup="total" placeholder="Discount" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off" class="mt-1 block w-full px-4 pr-12 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                            <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                        </div>
                                        <InputError :message="$page.props.errors.discount" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700"> Description </label>
                                        <textarea v-model="form.description" placeholder="Description" rows="3" class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded"></textarea>
                                        <InputError :message="$page.props.errors.description" />
                                    </div>
                                </div>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"> <div class="w-full border-t border-gray-300" /> </div>
                                <div class="relative flex justify-center ml-4"> <span class="px-3 bg-white text-lg font-medium text-gray-900"></span> </div>
                            </div>

                            <div class="max-w-xl mx-auto">
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
        Head,
        InputError,
        Listbox,
        Link,

        PlusIcon,
        PencilSquareIcon,
    },

    props:{

        string_change: Object,
        categories: Object,
    },

    methods: {
        total() {
            let total = this.form.rate - this.form.discount;
            this.form.total = total.toFixed(2);
        }
    },

    setup (props) {
        const breadcrumbs = [
            { name: props.string_change.product_s, href: route('product.index'), current: false },
            { name: 'Create Page', href: '#', current: false },
        ]

        const form = reactive({
            code: '',
            name: '',
            english_name: '',
            rate: '',
            discount: '',
            description: '',
            product_category_id: '',
        })

        function submit() {
            router.post(route('product.store'), form);
        }

        return {
            breadcrumbs,
            form,
            submit
        }
    }
}
</script>
