<script setup>
import { reactive, onMounted, onUpdated } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import Alert from '@/Components/Alert.vue'
import Combobox from '@/Components/Combobox.vue'

import { PlusIcon, ChevronUpDownIcon } from '@heroicons/vue/24/solid'

import {
    ArrowTopRightOnSquareIcon,
    FunnelIcon,
    PencilSquareIcon,
    PrinterIcon,
    TrashIcon,
    MagnifyingGlassIcon,
    ShoppingCartIcon,
    ArrowPathIcon,
    TableCellsIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    filter: Object,
	accounts: Array,
	categories: Array,
    expense_load: Boolean,
})

const form = reactive(props.filter)

const breadcrumbs = [
    { name: 'Expenses', href: route('expense.index'), current: false },
    { name: 'List Page', href: '#', current: false }
]

onUpdated(() => {
    console.log('updated')
    loadAjaxData()
})

onMounted(() => {
    console.log('mounted')
    loadAjaxData()
})

const loadAjaxData = () => {
    $('#ajax_table').DataTable({
        responsive: true,
        serverSide: true,
        processing: true,
		destroy: true,
        lengthMenu: [
            [10, 25, 50, 100, 200],
            [10, 25, 50, 100, 200]
        ],
        length: 10,
        dom: "<'flex flex-col sm:flex-row justify-between'lf><'block overflow-auto 'rt><'flex flex-col sm:flex-row justify-between items-center'ip>",
        ajax: {
            url: $('#ajax_table').data('url'),
            type: 'GET',
            data: form
        },
        order: [[1, 'desc']],
        columns: [
            {
                data: 'SrNo',
                class: 'px-2 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5 sorting_1',
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1
                }
            },
            {
                class: 'px-2 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5',
                data: 'datetime_format'
            },
            {
                class: 'px-2 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5',
                data: 'branch_name'
            },
            {
                class: 'px-2 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5',
                data: 'account_name'
            },
            {
                class: 'px-2 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5',
                data: 'category_name'
            },
            {
                class: 'px-2 py-1 sm:py-2 whitespace-nowrap border-b border-gray-200 text-sm leading-5',
                data: 'amount'
            },
            {
                class: 'px-2 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5',
                data: 'created_at_format'
            },
            {
                class: 'px-2 py-1 sm:py-2 whitespace-nowrap border-b border-gray-200 text-sm leading-5',
                data: 'status'
            },
            {
                class: 'px-2 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5',
                data: 'action',
                sortable: false
            }
        ]
    })
}

const clearFilter = () => {
    for (const [key, value] of Object.entries(form)) {
        form[key] = ''
    }
}

const submit = () => {
    router.visit(route('expense.index'), {
        data: form
    })
}
</script>
<template>
    <Head title="Expenses" />

    <AuthenticatedLayout>
        <div>
            <div class="bg-white shadow">
                <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                    <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                        <div class="flex-1 min-w-0">
                            <Breadcrumb :breadcrumbs="breadcrumbs" />
                        </div>
                        <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
							<Link :href="route('expense.create')" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400" >
								<PlusIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
								Create
							</Link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-5" >
                <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white shadow sm:rounded-lg">
						<div
							class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
							<div class="flex-1">
								<h3 class="text-base font-semibold leading-6 text-gray-900">Expenses</h3>
								<p class="mt-1 text-sm text-gray-500">
									{{ (props.filter.start_date ? props.filter.start_date.split("-").reverse().join("/") : '') + " - " +
										(props.filter.end_date ? props.filter.end_date.split("-").reverse().join("/") : '') }}
								</p>
							</div>
							<div class="flex-shrink-0 flex space-x-3">
							</div>
						</div>

						<Alert />

                        <form @submit.prevent="submit" v-if="props.expense_load">
                            <dl class="px-5 py-5 mx-auto max-w-5xl">
                                <div class="py-2 sm:grid sm:grid-cols-8 sm:gap-4">
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Account</label>
                                        <Combobox class="mt-1" v-model="form.account_id" :items="accounts" />
                                    </dd>

                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Category</label>
                                        <Combobox class="mt-1" v-model="form.category_id" :items="categories" />
                                    </dd>

                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                        <input
                                            v-model="form.start_date"
                                            type="date"
                                            class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                    </dd>

                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
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
                                                class="inline-flex items-center px-4 py-1 border border-primary-600 rounded-r shadow-sm text-sm font-medium text-primary-700 bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                                <ArrowPathIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                                Clear
                                            </button>
                                        </div>
                                    </dd>
                                </div>
                            </dl>
                        </form>

                        <table class="table-auto sm:table-fixed min-w-full w-full" id="ajax_table" v-if="props.expense_load" :data-url="route('expense.load')">
                            <thead>
                                <tr>
									<th class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">S.N.</th>
									<th class="w-20 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Date</th>
									<th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Branch</th>
									<th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Account</th>
									<th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Category</th>
									<th class="w-24 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Amount</th>
									<th class="w-20 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Created&nbsp;At</th>
									<th class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">Status</th>
									<th class="w-20 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>