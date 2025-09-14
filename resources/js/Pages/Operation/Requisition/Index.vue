<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { onMounted, reactive } from 'vue'

import Alert from '@/Components/Alert.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import { PlusIcon } from '@heroicons/vue/24/solid'

import { ArrowPathIcon, ArrowTopRightOnSquareIcon, MagnifyingGlassIcon, PencilSquareIcon, PrinterIcon, TrashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    filter: Object,
    string_change: Object,
    requisitions: Array
})

onMounted(() => {
    $('#table').DataTable({
        lengthMenu: [
            [10, 25, 50, 100, 200],
            [10, 25, 50, 100, 200]
        ],
        length: 10,
        dom: "<'flex justify-center sm:justify-end mb-3'B><'flex flex-col sm:flex-row justify-between'lf><'block overflow-auto'rt><'flex flex-col sm:flex-row justify-between'ip>",
        buttons: ['copy', 'excel']
    })
})

const destroy = (route, message = 'Are you sure you want to delete?') => {
    if (confirm(message)) {
        router.delete(route)
    }
}

const breadcrumbs = [
    { name: 'Requisitions', href: route('requisition.index'), current: false },
    { name: 'List Page', href: '#', current: false }
]

const form = reactive({
    end_date: props.filter.end_date,
    start_date: props.filter.start_date
})

const clearFilter = () => {
    for (const [key, value] of Object.entries(form)) {
        form[key] = ''
    }
}

const submit = () => {
    router.visit(route('requisition.index'), {
        data: form
    })
}
</script>
<template>
    <Head title="Requisitions" />

    <AuthenticatedLayout>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>
                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                        <Link
                            :href="route('requisition.create')"
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
                        <div class="flex-1">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Requisitions</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{
                                    (props.filter.start_date ? props.filter.start_date.split('-').reverse().join('/') : '') +
                                    ' - ' +
                                    (props.filter.end_date ? props.filter.end_date.split('-').reverse().join('/') : '')
                                }}
                            </p>
                        </div>
                        <div class="flex-shrink-0 flex space-x-3"></div>
                    </div>

                    <form @submit.prevent="submit">
                        <dl class="px-5 py-5 mx-auto max-w-5xl">
                            <div class="py-2 sm:grid sm:grid-cols-8 sm:gap-4">
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1"></dd>

                                <dd v-if="form.start_date" class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                    <input
                                        v-model="form.start_date"
                                        type="date"
                                        class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                </dd>

                                <dd v-if="form.end_date" class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">End Date</label>
                                    <input
                                        v-model="form.end_date"
                                        type="date"
                                        class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                </dd>

                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Action</label>
                                    <div class="inline-flex mt-1 rounded" role="group">
                                        <button
                                            type="submit"
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-l shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                            <MagnifyingGlassIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                            Search
                                        </button>

                                        <button
                                            @click="clearFilter"
                                            type="reset"
                                            class="inline-flex items-center px-4 py-1 border border-primary-600 rounded-r shadow-sm text-sm font-medium text-primary-700 bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                            <ArrowPathIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                            Clear
                                        </button>
                                    </div>
                                </dd>
                            </div>
                        </dl>
                    </form>

                    <Alert />

                    <table class="table-auto sm:table-fixed min-w-full w-full" id="table">
                        <thead>
                            <tr>
                                <th class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">S.N.</th>
                                <th class="w-40 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Date</th>
                                <th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Total</th>
                                <th class="w-40 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr v-for="(requisition, index) in requisitions" :key="requisition.id" :class="[index % 2 == 0 ? 'bg-white' : 'bg-gray-50', 'border-b']">
                                <td class="p-2 text-center">{{ Number(index) + 1 }}</td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
                                    {{ requisition.datetime_format }}
                                </td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
                                    {{ requisition.total ?? 0 }}
                                </td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
                                    <div class="flex justify-end">
                                        <a :href="route('report.requisition', requisition.id)" target="_blank" class="text-gray-600 hover:text-gray-800 ml-3" title="print">
                                            <PrinterIcon class="w-6 h-6" aria-hidden="true" />
                                        </a>
                                        <Link :href="route('requisition.show', requisition.id)" class="text-primary-600 hover:text-primary-800 ml-3" title="detail">
                                            <ArrowTopRightOnSquareIcon class="w-6 h-6" aria-hidden="true" />
                                        </Link>
                                        <Link :href="route('requisition.edit', requisition.id)" class="text-indigo-600 hover:text-indigo-800 ml-3" title="edit">
                                            <PencilSquareIcon class="w-6 h-6" aria-hidden="true" />
                                        </Link>
                                        <button @click="destroy(route('requisition.destroy', requisition.id))" class="text-red-600 hover:text-red-800 ml-3" title="delete">
                                            <TrashIcon class="w-6 h-6" aria-hidden="true" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
