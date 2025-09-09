<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import Alert from '@/Components/Alert.vue'

import { PlusIcon } from '@heroicons/vue/24/solid'

import { PencilSquareIcon } from '@heroicons/vue/24/outline'

const page = usePage()

const props = defineProps({
    string_change: Object,
    types: Array,
    product_categories: Array
})

const breadcrumbs = [
    { name: props.string_change.product_s + ' Categories', href: route('product_category.index'), current: false },
    { name: 'Specification Page', href: '#', current: false }
]

const form = useForm({
    product_categories: props.product_categories
})

const submit = () => {
	page.props.alertMessage = {};

	form.post(route('product_category.specification_update'), {})
}
</script>
<template>
    <Head title="Specification" />

    <AuthenticatedLayout>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>

                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                        <Link
                            :href="route('product_category.create')"
                            class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
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
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">{{ string_change.product }} Categories Specification</p>

                        <div class="flex-shrink-0 flex space-x-3">
                            <button
                                type="submit"
                                @click="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                <PencilSquareIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                Update
                            </button>
                        </div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="space-y-8 py-6 px-6">
                            <table class="table-auto sm:table-fixed min-w-full w-full">
                                <thead>
                                    <tr>
                                        <th class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">S.N.</th>
                                        <th class="p-1/3 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Name</th>
                                        <th
                                            v-for="(type, key) in types"
                                            :key="key"
                                            class="w-48 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">
                                            {{ type.name }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <tr
                                        v-for="(product_category, index) in form.product_categories"
                                        :key="product_category.id"
                                        :class="[index % 2 == 0 ? 'bg-white' : 'bg-gray-50', 'border-b']">
                                        <td class="px-3 py-1 whitespace-wrap break-words">
                                            <div class="text-sm leading-5 text-gray-700 text-center">{{ parseInt(index) + 1 }}</div>
                                        </td>

                                        <td class="px-3 py-1 whitespace-wrap break-words">
                                            <div class="text-sm leading-5 text-gray-700">{{ product_category.name }}</div>
                                        </td>

                                        <td v-for="(type, key) in types" :key="key" class="p-2 whitespace-nowrap text-center">
                                            <input
                                                type="radio"
                                                v-model="product_category.type"
                                                :value="type.id"
                                                class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded-full" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-gray-300" /></div>
                                <div class="relative flex justify-center ml-4"><span class="px-3 bg-white text-lg font-medium text-gray-900"></span></div>
                            </div>

                            <div class="max-w-full mx-auto">
                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                        <PencilSquareIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                        Update
                                    </button>
                                </div>
                            </div>
                        </dl>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
