<template>
    <Head title="Orders" />

    <!-- Header / Breadcrumb -->
    <div class="bg-white shadow">
        <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
            <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                <div class="flex-1 min-w-0">
                    <Breadcrumb :breadcrumbs="breadcrumbs" />
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="py-5">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
                    <div class="flex-1">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Orders</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{
                                (filter.start_date ? filter.start_date.split('-').reverse().join('/') : '') +
                                ' - ' +
                                (filter.end_date ? filter.end_date.split('-').reverse().join('/') : '')
                            }}
                        </p>
                    </div>
                </div>

                <Alert />

                <form @submit.prevent="submit">
                    <dl class="px-5 py-5 mx-auto max-w-5xl">
                        <div class="py-2 sm:grid sm:grid-cols-8 sm:gap-4">
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Manager</label>
                                <Combobox class="mt-1" v-model="filter.manager_id" :items="managers" />
                            </dd>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Waiter</label>
                                <Combobox class="mt-1" v-model="filter.waiter_id" :items="waiters" />
                            </dd>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Customer</label>
                                <Combobox class="mt-1" v-model="filter.customer_id" :items="customers" />
                            </dd>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                                <Combobox class="mt-1" v-model="filter.payment_method_id" :items="payment_methods" />
                            </dd>

                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input v-model="filter.start_date" type="date" class="mt-1 block w-full px-4 border-gray-300 rounded sm:text-sm" />
                            </dd>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">End Date</label>
                                <input v-model="filter.end_date" type="date" class="mt-1 block w-full px-4 border-gray-300 rounded sm:text-sm" />
                            </dd>

                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <Combobox class="mt-1" v-model="filter.status" :items="statuses" />
                            </dd>

                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Action</label>
                                <div class="flex w-full mt-1 rounded shadow-sm" role="group">
                                    <button
                                        type="submit"
                                        class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-l text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                                        <MagnifyingGlassIcon class="-ml-1 mr-2 h-5 w-5" />
                                        Search
                                    </button>
                                    <button
                                        @click="clearFilter"
                                        class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-primary-600 rounded-r text-sm font-medium text-primary-700 bg-white hover:bg-primary-50">
                                        <ArrowPathIcon class="-ml-1 mr-2 h-5 w-5" />
                                        Clear
                                    </button>
                                </div>
                            </dd>
                        </div>
                    </dl>
                </form>

                <!-- DataTable -->
                <table class="table-auto sm:table-fixed min-w-full w-full" id="ajax_table" :data-url="route('order.load')">
                    <thead>
                        <tr>
                            <th class="w-3 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">S.N.</th>
                            <th class="w-10 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Date <br> dmY</th>
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

    <!-- Product Popup -->
    <OrderDetailsModal :show="showProducts" :products="productList" @close="showProducts = false" />
</template>

<script setup>
import Alert from '@/Components/Alert.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import Combobox from '@/Components/Combobox.vue'
import OrderDetailsModal from '@/Components/OrderDetailsModal.vue'
import AppLayout from '@/Layouts/AuthenticatedLayout.vue'

import { ArrowPathIcon, ArrowTopRightOnSquareIcon, MagnifyingGlassIcon, PencilSquareIcon, PrinterIcon, TrashIcon } from '@heroicons/vue/24/outline'
import { Head, router } from '@inertiajs/vue3'
import { createApp, h, onMounted, reactive, ref } from 'vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    filter: Object,
    managers: Array,
    waiters: Array,
    customers: Array,
    statuses: Array,
    payment_methods: Array
})

const filter = reactive(props.filter)
const breadcrumbs = [
    { name: 'Orders', href: route('order.index'), current: false },
    { name: 'List Page', href: '#', current: false }
]

const showProducts = ref(false)
const productList = ref([])

// Function to open the modal with products
window.showProductPopup = (products) => {
    if (!products || !products.length) return
    productList.value = products // assign array directly
    showProducts.value = true
}

const clearFilter = () => Object.keys(filter).forEach((k) => (filter[k] = ''))
const submit = () => router.visit(route('order.index'), { data: filter })

const loadAjaxData = () => {
    const table = $('#ajax_table').DataTable({
        responsive: true,
        serverSide: true,
        processing: true,
        destroy: true,
        lengthMenu: [
            [10, 25, 50, 100, 200],
            [10, 25, 50, 100, 200]
        ],
        ajax: {
            url: $('#ajax_table').data('url'),
            type: 'GET',
            data: filter
        },
        createdRow: (row, data) => {
            $(row).toggleClass('text-gray-700', data.is_complete)
            $(row).toggleClass('text-red-700', !data.is_complete)
        },
        order: [[1, 'desc']],
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1 },
            { data: 'datetime_format' },
            { data: 'waiter_name' },
            { data: 'branch_invoice' },
            {
                data: 'detail',
                sortable: false,
                render: (data, type, row) => {
                    // Pass products array safely to the modal
                    return `<span onclick='showProductPopup(${JSON.stringify(
                        row.products || []
                    )})' class="cursor-pointer text-primary-600 underline decoration-dotted">${data}</span>`
                }
            },
            { data: 'discount_amount', className: 'text-right' },
            { data: 'discount_type', className: 'text-right' },
            { data: 'vat_amount', className: 'text-right' },
            { data: 'total', className: 'text-right' },
            {
                data: 'actions',
                orderable: false,
                searchable: false,
                render: (data) => `<div class="vue-actions" data-actions='${JSON.stringify(data).replace(/'/g, '&apos;')}'></div>`
            }
        ]
    })

    table.on('draw', () => {
        document.querySelectorAll('.vue-actions').forEach((el) => {
            if (el.dataset.vueMounted) return
            const actions = JSON.parse(el.dataset.actions)
            const ActionButtons = {
                props: ['actions'],
                components: { ArrowTopRightOnSquareIcon, PrinterIcon, PencilSquareIcon, TrashIcon },
                setup(props) {
                    return () =>
                        h(
                            'div',
                            { class: 'flex justify-end space-x-2' },
                            [
                                props.actions.print_url &&
                                    h('a', { href: props.actions.print_url, target: '_blank', class: 'text-green-600 hover:text-green-800' }, [
                                        h(PrinterIcon, { class: 'w-6 h-6' })
                                    ]),
                                props.actions.detail_url &&
                                    h('a', { href: props.actions.detail_url, target: '_blank', class: 'text-primary-600 hover:text-primary-800' }, [
                                        h(ArrowTopRightOnSquareIcon, { class: 'w-6 h-6' })
                                    ]),
                                props.actions.edit_url &&
                                    h('a', { href: props.actions.edit_url, class: 'text-indigo-600 hover:text-indigo-800' }, [h(PencilSquareIcon, { class: 'w-6 h-6' })]),
                                props.actions.destroy_url &&
                                    h('button', { class: 'text-red-600 hover:text-red-800', onClick: () => window.deleteRecord(props.actions.destroy_url) }, [
                                        h(TrashIcon, { class: 'w-6 h-6' })
                                    ])
                            ].filter(Boolean)
                        )
                }
            }
            createApp(ActionButtons, { actions }).mount(el)
            el.dataset.vueMounted = 'true'
        })
    })
}

onMounted(() => {
    loadAjaxData()
})
</script>
