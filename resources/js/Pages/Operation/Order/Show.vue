<script setup>
import { ref } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import { ChevronDownIcon, EnvelopeIcon, PencilSquareIcon, PhoneIcon, PlusIcon, PrinterIcon, TrashIcon } from '@heroicons/vue/24/solid'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Alert from '@/Components/Alert.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'

import StatusOptions from '@/Components/StatusOptions.vue'

const page = usePage()

const props = defineProps({
    order: Object,
	statuses: Object,
})

const breadcrumbs = [
    { name: 'Orders', href: route('order.index'), current: false },
    { name: 'Detail Page', href: '#', current: false }
]

const data = [
    { name: 'Date', value: props.order.datetime_format },
    { name: 'Invoice', value: props.order.invoice_number },
    { name: 'Branch', value: props.order.branch_name },
    { name: 'Manager', value: props.order.creator_name },
    { name: 'Served By', value: props.order.waiter_name },
    { name: 'Table Number', value: props.order.table_name ?? 'N/A' },
    { name: 'Payment Method', value: props.order.payment_method_name ?? 'N/A' },
    { name: 'Customer', value: props.order.customer_name ?? 'N/A' },
    { name: 'Type', value: props.order.type },
    { name: 'Delivery Address', value: props.order.delivery_address ?? 'N/A' },
    { name: 'Delivery Cost', value: props.order.delivery_cost ?? 'N/A' },
    { name: 'Sub Total', value: props.order.sub_total },
    { name: 'Discount', value: props.order.discount_amount },
    { name: 'VAT', value: props.order.vat_amount },
    // { name: 'Total', value: props.order.sub_total + ' - ' + props.order.discount_amount + ' + ' + props.order.vat_amount + ' = ' + props.order.total },
    { name: 'Total', value: props.order.total }
]
</script>
<template>
    <Head title="Order" />

    <AuthenticatedLayout>
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
                <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                    <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
                        <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
                            <div class="ml-4 mt-4">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                                            {{ order.number }} - {{ order.method_name }} - Serial: {{ order.serial }}
                                        </h3>
                                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Order details and product information.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-4 mt-4 shrink-0 flex">
                                <a
                                    v-if="page.props.navigation.routes.includes('pos.print')"
                                    :href="route('pos.print', order.id)"
                                    target="_blank"
                                    class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
                                    <PrinterIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                                    Invoice
                                </a>

								<StatusOptions class="ml-3" as="button" :status="order.status" :items="statuses" :href="route('order.status.update', order.id)"/>

                            </div>
                        </div>
                    </div>

                    <Alert />

                    <div class="border-t border-gray-200">
                        <dl>
                            <div class="max-w-6xl mx-auto">
                                <div
                                    v-for="(item, index) in data"
                                    :key="index"
                                    :class="[index % 2 == 1 ? 'bg-white' : 'bg-gray-50', 'px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6']">
                                    <dt class="text-sm font-medium text-gray-500">{{ item.name }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <span class="capitalize"> {{ item.value }} </span>
                                    </dd>
                                </div>
                            </div>
                        </dl>

                        <div class="relative">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-gray-300" /></div>
                            <div class="relative flex justify-center ml-4">
                                <span class="px-3 bg-white text-lg font-medium text-gray-900"> {{ 'Order ' + page.props.string_change.product_s }} </span>
                            </div>
                        </div>

                        <dl class="my-8">
                            <div class="sm:col-span-2">
                                <dd class="mt-1 px-4 sm:px-6 text-sm text-gray-900">
                                    <table class="border-collapse table-fixed w-full text-sm">
                                        <thead>
                                            <tr>
                                                <th class="w-20 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Image</th>
                                                <th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Product</th>
                                                <th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Rate</th>
                                                <th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Quantity</th>
                                                <th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Total</th>
                                                <!-- <th class="w-64 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Topping - Rate * Qty = Total</th> -->
                                            </tr>
                                        </thead>

                                        <tbody class="bg-white">
                                            <tr
                                                v-for="(product, index) in order.group_products"
                                                :key="index"
                                                :class="[index % 2 == 0 ? 'bg-white' : 'bg-primary-50', 'border-b border-gray-100 pl-0 p-3 text-gray-900']">
                                                <td class="p-2 pl-6">
                                                    <img class="shrink-0 h-6 w-6 rounded-full" :src="product.image_url" alt="" />
                                                </td>
                                                <td class="p-2 whitespace-wrap">{{ product.product_name }}</td>
                                                <td class="p-2 whitespace-wrap">{{ product.rate }}</td>
                                                <td class="p-2 whitespace-wrap">{{ product.quantity }}</td>
                                                <td class="p-2 whitespace-wrap">{{ product.total }}</td>
                                                <!-- <td class="p-2 pr-6">
                                                    <span v-for="topping in product.toppings" :key="topping.id" class="">
                                                        <span>{{ topping.name }} </span> <br>
                                                    </span>
                                                </td> -->
                                            </tr>
                                        </tbody>
                                    </table>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
