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
                        <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4"></div>
                    </div>
                </div>
            </div>

            <div class="py-5">
                <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white shadow sm:rounded-lg">
                        <div class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
                            <div class="flex-1">
                                <h3 class="text-base font-semibold leading-6 text-gray-900">Orders</h3>
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

                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                                        <Combobox class="mt-1" v-model="form.payment_method_id" :items="payment_methods" />
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
                                        <label class="block text-sm font-medium text-gray-700">Status</label>
                                        <Combobox class="mt-1" v-model="form.status" :items="statuses" />
                                    </dd>

                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Action</label>
                                        <div class="flex w-full mt-1 rounded shadow-sm" role="group">
                                            <button
                                                type="submit"
                                                class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-l text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                                <MagnifyingGlassIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                                Search
                                            </button>

                                            <button
                                                @click="clearFilter"
                                                class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-primary-600 rounded-r text-sm font-medium text-primary-700 bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
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

        <!-- Popup Modal -->
        <div v-if="showProducts" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showProducts = false">
            <div class="bg-white rounded-xl shadow-2xl w-11/12 max-w-lg max-h-[80vh] p-6 relative overflow-hidden">
                <button @click="showProducts = false" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 transition">✕</button>
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Products List</h2>
                <div class="overflow-y-auto max-h-[60vh] pr-2 custom-scrollbar">
                    <ul class="list-disc list-inside space-y-1 text-gray-700 text-sm">
                        <li v-for="(item, index) in productList" :key="index">{{ item }}</li>
                    </ul>
                </div>
                <div class="mt-5 flex justify-end">
                    <button @click="showProducts = false" class="px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 transition">Close</button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import { createApp, h, onMounted, reactive, ref } from 'vue'

import Alert from '@/Components/Alert.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import Combobox from '@/Components/Combobox.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import { ArrowPathIcon, ArrowTopRightOnSquareIcon, MagnifyingGlassIcon, PencilSquareIcon, PrinterIcon, TrashIcon } from '@heroicons/vue/24/outline'

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

const showProducts = ref(false)
const productList = ref([])

window.showProductPopup = (titles) => {
    productList.value = titles.split(',').map((title) => title.trim())
    showProducts.value = true
}

const clearFilter = () => {
    Object.keys(form).forEach((key) => (form[key] = ''))
}

const submit = () => {
    router.visit(route('order.index'), { data: form })
}

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
            data: form
        },
        createdRow: (row, data) => {
            if (data.is_complete) $(row).addClass('text-gray-700')
            else $(row).addClass('text-red-700')
        },
        order: [[1, 'desc']],
        columns: [
            {
                data: null,
                render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
            },
            { data: 'datetime_format' },
            {
                data: 'waiter_name',
                orderable: false
            },
            { data: 'branch_invoice' },
            {
                data: 'detail',
                sortable: false,
                render: (data, type, row) =>
                    `<span onclick="showProductPopup('${row.product_titles || ''}')" class="cursor-pointer text-primary-600 underline decoration-dotted">${data}</span>`
            },
            { data: 'discount_amount', class: 'text-right' },
            { data: 'discount_type', class: 'text-right' },
            { data: 'vat_amount', class: 'text-right' },
            { data: 'total', class: 'text-right' },
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
                                props.actions.show_url &&
                                    h('a', { href: props.actions.show_url, target: '_blank', class: 'text-primary-600 hover:text-primary-800' }, [
                                        h(ArrowTopRightOnSquareIcon, { class: 'w-6 h-6' })
                                    ]),
                                props.actions.edit_url &&
                                    h('a', { href: props.actions.edit_url, class: 'text-indigo-600 hover:text-indigo-800' }, [h(PencilSquareIcon, { class: 'w-6 h-6' })]),
                                props.actions.delete_id &&
                                    h(
                                        'button',
                                        {
                                            class: 'text-red-600 hover:text-red-800',
                                            onClick: () => window.deleteRecord(props.actions.delete_id)
                                        },
                                        [h(TrashIcon, { class: 'w-6 h-6' })]
                                    )
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
