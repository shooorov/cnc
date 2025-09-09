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
    managers: Array,
    waiters: Array,
    customers: Array,
    statuses: Array,
    payment_methods: Array
})

const form = reactive(props.filter)

const breadcrumbs = [
    { name: 'Orders', href: route('order.index'), current: false },
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
        createdRow: function (row, data) {
            if (data.is_complete) $(row).addClass('text-gray-700')
            else $(row).addClass('text-red-700')
        },
        order: [[1, 'desc']],
        columns: [
            {
                data: 'SrNo',
                class: 'px-2 sm:px-4 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5 sorting_1',
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1
                }
            },
            {
                class: 'px-2 sm:px-4 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5',
                data: 'datetime_format'
            },
            {
                class: 'px-2 sm:px-4 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5',
                data: 'waiter_name'
            },
            {
                class: 'px-2 sm:px-4 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5',
                data: 'branch_invoice'
            },
            {
                class: 'px-2 sm:px-4 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5',
                data: 'detail',
                sortable: false
            },
            {
                class: 'px-2 sm:px-4 py-1 sm:py-2 whitespace-nowrap border-b border-gray-200 text-sm leading-5',
                data: 'discount_amount'
            },
            {
                class: 'px-2 sm:px-4 py-1 sm:py-2 whitespace-nowrap border-b border-gray-200 text-sm leading-5',
                data: 'discount_type'
            },
            // {
            //     class: 'px-2 sm:px-4 py-1 sm:py-2 whitespace-nowrap border-b border-gray-200 text-sm leading-5',
            //     data: 'member_code'
            // },
            // {
            //     class: 'px-2 sm:px-4 py-1 sm:py-2 whitespace-nowrap border-b border-gray-200 text-sm leading-5',
            //     data: 'member_discount'
            // },
          
            {
                class: 'px-2 sm:px-4 py-1 sm:py-2 whitespace-nowrap border-b border-gray-200 text-sm leading-5',
                data: 'vat_amount'
            },
            {
                class: 'px-2 sm:px-4 py-1 sm:py-2 whitespace-nowrap border-b border-gray-200 text-sm leading-5',
                data: 'total'
            },
            {
                class: 'px-2 sm:px-4 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5',
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
    router.visit(route('order.index'), {
        data: form
    })
}
</script>
<template>
    <Head title="Orders" />

    <AuthenticatedLayout>
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
						<div
							class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
							<div class="flex-1">
								<h3 class="text-base font-semibold leading-6 text-gray-900">Orders</h3>
								<p class="mt-1 text-sm text-gray-500">
									{{ (props.filter.start_date ? props.filter.start_date.split("-").reverse().join("/") : '') + " - " +
										(props.filter.end_date ? props.filter.end_date.split("-").reverse().join("/") : '') }}
								</p>
							</div>
							<div class="flex-shrink-0 flex space-x-3">
							</div>
						</div>

						<Alert />

                        <form @submit.prevent="submit">
                            <dl class="px-5 py-5 mx-auto max-w-5xl">
                                <div class="py-2 sm:grid sm:grid-cols-8 sm:gap-4">
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Manager</label>
                                        <Combobox class="mt-1" v-model="form.manager_id" :items="managers" />
                                    </dd>

                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Waiter</label>
                                        <Combobox class="mt-1" v-model="form.waiter_id" :items="waiters" />
                                    </dd>

                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Customer</label>
                                        <Combobox class="mt-1" v-model="form.customer_id" :items="customers" />
                                    </dd>

                                    <!-- <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                                        <Combobox class="mt-1" v-model="form.payment_method_id" :items="payment_methods" />
                                    </dd> -->

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
										<label class="block text-sm font-medium text-gray-700">Status</label>
										<Combobox class="mt-1" v-model="form.status" :items="statuses" />
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

                        <table class="table-auto sm:table-fixed min-w-full w-full" id="ajax_table" :data-url="route('order.load')">
                            <thead>
                                <tr>
                                    <th class="w-3 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">S.N.</th>
                                    <th class="w-10 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Date</th>
                                    <th class="w-10 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Waiter</th>
                                    <th class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Number</th>
                                    <th class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Des</th>
                                    <th class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Discount</th>
                                    <th class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Type</th>
                                    <!-- hide for cnc only -->
                                    <!-- <th class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">M. Code</th>
                                    <th class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">M. Discount</th> -->

                                    <th class="w-8 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">VAT</th>
                                    <th class="w-8 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Total</th>
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
