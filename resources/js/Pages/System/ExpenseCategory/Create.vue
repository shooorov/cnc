<template>
    <Head title="Create Expense Category" />

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
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0"> Expense Category Create</p>

                        <div class="flex-shrink-0 flex space-x-3">
                        </div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="space-y-4 sm:space-y-6 px-5 py-6">

                            <!-- <div class="grid sm:grid-cols-3 sm:gap-2 sm:px-6">
                                <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8">
                                    Category
                                </dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-1">
                                    <Combobox class="mt-1" v-model="form.expense_category_id" :items="expense_categories" />
                                </dd>
                            </div> -->

                            <div class="grid sm:grid-cols-3 sm:gap-2 sm:px-6">
                                <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8"> Type <span class="text-red-500">*</span> </dt>
                                <dd class="flex mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 gap-3">
                                    <label class="mb-5 inline-flex items-center">
                                        <input v-model="form.type" value="receive" type="radio" class="form-radio w-5 h-5 border-gray-300 text-indigo-500 cursor-pointer focus:ring-indigo-500">
                                        <span class="ml-2 tracking-wide cursor-pointer"> Receive </span>
                                    </label><br>
                                    <label class="mb-5 inline-flex items-center">
                                        <input v-model="form.type" value="payment" type="radio" class="form-radio w-5 h-5 border-gray-300 text-indigo-500 cursor-pointer focus:ring-indigo-500">
                                        <span class="ml-2 tracking-wide cursor-pointer"> Payment </span>
                                    </label><br>
                                    <InputError :message="$page.props.errors.type" />
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

                            <hr class="my-4">

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
        Combobox,
        Head,
        InputError,
        Link,

        PlusIcon,
        PencilSquareIcon,
    },

    props:{
        string_change: Object,

        expense_categories: Array,
    },

    setup (props) {
        const breadcrumbs = [
            { name: 'Expense Categories', href: route('expense_category.index'), current: false },
            { name: 'Create Page', href: '#', current: false },
        ]

        const form = reactive({
            expense_category_id: null,
            name: null,
            type: 'payment',
        })

        function submit() {
            router.post(route('expense_category.store'), form);
        }

        return {
            breadcrumbs,
            form,
            submit
        }
    }
}
</script>
