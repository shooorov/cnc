<template>
    <Head title="Show Inventory" />

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
                    <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
                        <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
                            <div class="ml-4 mt-4">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ string_change.product_inventory }} In Detail</h3>
                                        <p class="mt-1 max-w-2xl text-sm text-gray-500"> {{ string_change.product }} details and {{ string_change.item }} information. </p>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-4 mt-4 shrink-0 flex">
                                <!-- <a target="_blank" :href="route('prepare.print', product_inventory.id)" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-0 focus:ring-offset-gray-100 focus:ring-primary-400">
                                    <PrinterIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                                    Print
                                </a> -->
                            </div>
                        </div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="space-y-8 px-5 py-6">

                            <div class="max-w-3xl mx-auto">
                                <div class="grid grid-cols-3 gap-6">
                                    <div class="col-span-3 sm:col-span-1"></div>

                                    <div class="col-span-3 sm:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1"> Inventory Date </label>
                                        <label class="block w-full px-4 py-2 border sm:text-sm border-gray-300 rounded"> {{ product_inventory.date }}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"> <div class="w-full border-t border-gray-300" /> </div>
                                <div class="relative flex justify-center ml-4"> <span class="px-3 bg-white text-lg font-medium text-gray-900"> {{ string_change.product_s }} </span> </div>
                            </div>

                            <div class="max-w-3xl mx-auto">
                                <table class="table-auto sm:table-fixed min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ string_change.product }}</th>
                                            <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(group_product, index) in product_inventory.products" :key="index"  :class="[index % 2 == 1? 'bg-gray-50' : 'bg-white']">
                                            <td class="px-4 py-2 text-center">{{ group_product.product_name }}</td>
                                            <td class="px-4 py-2 text-center">{{ group_product.quantity }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"> <div class="w-full border-t border-gray-300" /> </div>
                                <div class="relative flex justify-center ml-4"> <span class="px-3 bg-white text-lg font-medium text-gray-900"> {{ string_change.item_s }} </span> </div>
                            </div>

                            <div class="max-w-5xl mx-auto space-y-4 sm:space-y-6">
                                <table class="table-auto sm:table-fixed min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                            <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ string_change.item }}</th>
                                            <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">In Stock</th>
                                            <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"> Total Required </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(item, index) in product_inventory.required_items" :key="index" :class="[key % 2 == 1? 'bg-gray-50' : 'bg-white', item.in_stock < (item.quantity * item.product_quantity)? 'text-red-400' : '']">
                                            <td class="px-4 py-2 text-center"> {{ Number(index) + 1 }} </td>

                                            <td class="px-4 py-2 text-center">
                                                {{ item.item_name }}
                                            </td>

                                            <td class="px-4 py-2 text-center">
                                                {{ item.in_stock }} {{ item.unit }}
                                            </td>

                                            <td class="px-4 py-2 text-center">
                                                {{ item.quantity }} {{ item.unit }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </dl>
                    </form>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import { Head, Link } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Alert from '@/Components/Alert.vue';
import InputError from '@/Components/InputError.vue';

import {
    PlusIcon,
    MinusIcon,
} from '@heroicons/vue/24/solid';

import {
    PencilSquareIcon,
    XMarkIcon,
    PrinterIcon,
} from '@heroicons/vue/24/outline';

export default {
    layout: AuthenticatedLayout,

    components: {
        Alert,
        Breadcrumb,
        Head,
        InputError,
        Link,
        MinusIcon,
        PencilSquareIcon,
        PlusIcon,
        PrinterIcon,
        XMarkIcon,
    },

    props: {
        string_change: Object,
        product_inventory: Object,
    },

    setup(props) {
        const breadcrumbs = [
            { name: props.string_change.product_inventory + ' In', href: route('product_inventory.in'), current: false },
            { name: 'Detail Page', href: '#', current: false },
        ]

        return {
            breadcrumbs,
        }
    }
}
</script>
