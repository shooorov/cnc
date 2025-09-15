<template>
    <Head> <title>Edit Kitchen Delivery</title> </Head>

    <div>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>

                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                        <Link
                            :href="route('kitchen_delivery.create')"
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
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">Kitchen Delivery Edit</p>

                        <div class="flex-shrink-0 flex space-x-3">
                            <button
                                @click="submit"
                                class="mr-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <PencilSquareIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                Update
                            </button>
                        </div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="space-y-4 sm:space-y-6 px-5 py-6">
                            <div class="max-w-xl mx-auto">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700"> Requisition Date <span class="text-red-500">*</span> </label>
                                        <input
                                            v-model="form.delivery_date"
                                            type="date"
                                            class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                        <InputError :message="$page.props.errors.delivery_date" />
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700"> Total Around <span class="text-red-500">*</span> </label>
                                        <input
                                            :value="form.total_format"
                                            type="text"
                                            placeholder="Total"
                                            readonly
                                            class="mt-1 block w-full px-4 focus:ring-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded" />
                                        <InputError :message="$page.props.errors.total" />
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700"> Delivery From Central Kitchen <span class="text-red-500">*</span> </label>
                                        <Combobox class="mt-1" v-model="form.central_kitchen_id" :items="central_kitchens" />
                                        <InputError :message="$page.props.errors.central_kitchen_id" />
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700"> {{ string_change.product }} Requisition <span class="text-red-500">*</span> </label>
                                        <div class="flex mx-auto">
                                            <Listbox class="" v-model="form.requisition_id" :items="requisitions" />
                                            <button
                                                v-if="form.requisition_id"
                                                @click="form.requisition_id = null"
                                                type="button"
                                                class="my-0.5 ml-1 inline-flex rounded p-1.5 text-gray-500 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-1">
                                                <span class="sr-only">Dismiss</span>
                                                <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                                            </button>
                                        </div>
                                        <InputError :message="$page.props.errors.requisition_id" />
                                    </div>
                                </div>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-gray-300" /></div>
                                <div class="relative flex justify-center ml-4"><span class="px-3 bg-white text-lg font-medium text-gray-900"> Kitchen Delivery Items </span></div>
                            </div>

                            <div class="max-w-5xl mx-auto space-y-4 sm:space-y-6">
                                <table class="table-auto sm:table-fixed min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ string_change.product }}</th>
                                            <th scope="col" class="w-28 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" title="Requisition Quantity">
                                                Req. Qty
                                            </th>
                                            <th scope="col" class="w-28 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" title="Delivery Quantity">
                                                Del Qty in pc
                                            </th>
                                            <th scope="col" class="w-28 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" title="Average Rate">
                                                Avg. Rate
                                            </th>
                                            <th scope="col" class="w-28 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" title="Average Total">
                                                Avg. Total
                                            </th>
                                            <th scope="col" class="w-28 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                                            <th scope="col" class="w-28 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" title="Delivery Total">
                                                Del Total
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(group_item, index) in form.group_items" v-show="group_item.show" :key="index">
                                            <td>
                                                <input
                                                    v-model="group_item.item_name"
                                                    readonly
                                                    type="text"
                                                    autocomplete="off"
                                                    class="block w-full px-4 focus:ring-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded" />
                                            </td>

                                            <td>
                                                <input
                                                    v-model="group_item.requisition_quantity"
                                                    readonly
                                                    type="text"
                                                    autocomplete="off"
                                                    class="block w-full px-4 focus:ring-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded" />
                                            </td>

                                            <td>
                                                <input
                                                    v-model="group_item.delivery_quantity"
                                                    @keyup="calculation(index)"
                                                    placeholder="Quantity"
                                                    type="text"
                                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                    class="block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                            </td>

                                            <td>
                                                <input
                                                    v-model="group_item.avg_rate"
                                                    placeholder="Avg Rate"
                                                    readonly
                                                    type="text"
                                                    autocomplete="off"
                                                    class="block w-full px-4 focus:ring-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded" />
                                            </td>

                                            <td>
                                                <input
                                                    :value="group_item.average_total_format"
                                                    placeholder="Total"
                                                    readonly
                                                    type="text"
                                                    autocomplete="off"
                                                    class="block w-full px-4 focus:ring-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded" />
                                            </td>

                                            <td>
                                                <input
                                                    v-model="group_item.rate"
                                                    @keyup="calculation(index)"
                                                    placeholder="Avg Rate"
                                                    type="text"
                                                    autocomplete="off"
                                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                    class="block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                            </td>

                                            <td>
                                                <input
                                                    :value="group_item.delivery_total_format"
                                                    placeholder="Total"
                                                    readonly
                                                    type="text"
                                                    autocomplete="off"
                                                    class="block w-full px-4 focus:ring-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded" />
                                            </td>
                                        </tr>
                                    </tbody>

                                    <tfoot class="bg-white">
                                        <tr>
                                            <th colspan="4" scope="col" class="py-2 text-right text-lg font-medium font-mono">Total Around</th>
                                            <th colspan="1" scope="col" class="py-2 text-center text-lg font-medium font-mono">{{ form.total_format }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-gray-300" /></div>
                                <div class="relative flex justify-center ml-4"><span class="px-3 bg-white text-lg font-medium text-gray-900"> </span></div>
                            </div>

                            <div class="max-w-5xl mx-auto space-y-4 sm:space-y-6">
                                <div class="flex justify-start">
                                    <button
                                        type="submit"
                                        class="mr-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
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
    </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive, watch } from 'vue'

import Alert from '@/Components/Alert.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import Combobox from '@/Components/Combobox.vue'
import InputError from '@/Components/InputError.vue'
import Listbox from '@/Components/Listbox.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import { PencilSquareIcon } from '@heroicons/vue/24/outline'
import { PlusIcon, XMarkIcon } from '@heroicons/vue/24/solid'

// Use defineOptions to set layout
defineOptions({ layout: AuthenticatedLayout })

const props = defineProps({
    string_change: Object,
    kitchen_delivery: Object,
    items: Array,
    delivery_items: Array,
    requisitions: Array,
    central_kitchens: Array
})

const breadcrumbs = [
    { name: 'Kitchen Deliveries', href: route('kitchen_delivery.index'), current: false },
    { name: 'Edit Page', href: '#', current: true }
]

// Reactive form
const form = reactive({
    delivery_date: props.kitchen_delivery.date_format,
    requisition_id: props.kitchen_delivery.product_requisition_id,
    central_kitchen_id: props.kitchen_delivery.central_kitchen_id,
    total: props.kitchen_delivery.total, // raw number for calculations
    total_format: Number(props.kitchen_delivery.total).toLocaleString('en-IN'), // Bangladeshi/Indian style
    group_items: props.kitchen_delivery.items.map((item) => ({
        ...item,
        show: true,
        average_total: item.avg_rate * (item.delivery_quantity || 0),
        delivery_total: item.rate * (item.delivery_quantity || 0),

        average_total_format: Number(item.avg_rate * (item.delivery_quantity || 0)).toLocaleString('en-IN'),
        delivery_total_format: Number(item.rate * (item.delivery_quantity || 0)).toLocaleString('en-IN')
    }))
})

// Submit form
const submit = () => {
    router.patch(route('kitchen_delivery.update', props.kitchen_delivery.id), form)
}

// Calculation method
const calculation = (index) => {
    const item = form.group_items[index]
    item.average_total = Number((item.avg_rate * (item.delivery_quantity || 0)).toFixed(3))
    item.delivery_total = Number((item.rate * (item.delivery_quantity || 0)).toFixed(3))
    item.average_total_format = Number(item.avg_rate * (item.delivery_quantity || 0)).toLocaleString('en-IN')
    item.delivery_total_format = Number(item.rate * (item.delivery_quantity || 0)).toLocaleString('en-IN')

    form.total = form.group_items.reduce((carry, val) => carry + Number(val.delivery_total || 0), 0)
    form.total_format = form.total.toLocaleString('en-US')
    form.average_total = form.group_items.reduce((carry, val) => carry + Number(val.average_total || 0), 0).toLocaleString('en-US')
}

// Watch for requisition changes
watch(
    () => form.requisition_id,
    (newVal) => {
        if (!newVal) return

        const selectedRequisition = props.requisitions.find((r) => r.id == newVal)
        if (!selectedRequisition) return

        form.group_items = props.delviery_items.map((item) => {
            const requisitionItem = selectedRequisition.items.find((i) => i.product_id === item.product_id)
            return {
                ...item,
                delivery_quantity: requisitionItem ? requisitionItem.delivery_quantity : 0,
                requisition_quantity: requisitionItem ? requisitionItem.requisition_quantity : 0,
                avg_rate: item.avg_rate,
                rate: item.rate,
                show: true,
                average_total: Number((item.avg_rate * (requisitionItem?.quantity || 0)).toFixed(3)),
                delivery_total: Number((item.avg_rate * (requisitionItem?.delivery_quantity || 0)).toFixed(3)),

                average_total_format: Number(item.avg_rate * (item.delivery_quantity || 0)).toLocaleString('en-IN'),
                delivery_total_format: Number(item.rate * (item.delivery_quantity || 0)).toLocaleString('en-IN')
            }
        })
    }
)
</script>
