<template>
	<Head> <title> {{ string_change.product }} Requisition </title> </Head>

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
                <div class="bg-white shadow sm:rounded-lg overflow-hidden">

                    <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
                        <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
                            <div class="ml-4 mt-4">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900"> {{ requisition.date }} </h3>
                                        <p class="mt-1 max-w-2xl text-sm text-gray-500"> Requisition details and {{ string_change.product }} information. </p>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-4 mt-4 shrink-0 flex">
                                <a target="_blank" :href="route('report.product_requisition', requisition.id)" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-0 focus:ring-offset-gray-100 focus:ring-primary-400">
                                    <PrinterIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                                    Print
                                </a>
                            </div>
                        </div>
                    </div>

                    <Alert />

                    <div class="border-t border-gray-200">
                        <dl>
                            <div class="bg-white md:px-8 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Total</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ requisition.total }}
                                </dd>
                            </div>
                        </dl>

                        <div class="relative">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true"> <div class="w-full border-t border-gray-300" /> </div>
                            <div class="relative flex justify-center ml-4"> <span class="px-3 bg-white text-lg font-medium text-gray-900"> {{ string_change.product_s }} </span> </div>
                        </div>

                        <dl class="my-8">
                            <div class="sm:col-span-2">
                                <dd class="text-sm text-gray-900">
                                    <table class="border-collapse table-fixed w-full text-sm">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="w-80 px-8 py-3 text-xs text-left font-medium border-b text-gray-500 uppercase tracking-wider">{{ string_change.product }}</th>
                                                <th scope="col" class="w-56 px-8 py-3 text-xs text-left font-medium border-b text-gray-500 uppercase tracking-wider">Quantity</th>
                                                <th scope="col" class="w-40 px-8 py-3 text-xs text-left font-medium border-b text-gray-500 uppercase tracking-wider">Avg Rate</th>
                                                <th scope="col" class="w-40 px-8 py-3 text-xs text-left font-medium border-b text-gray-500 uppercase tracking-wider">Total</th>
                                            </tr>
                                        </thead>

                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="(group_item, index) in requisition.products" :key="index" :class="[index % 2 == 0 ? 'bg-white' : 'bg-primary-50']">
                                                <td class="px-8 py-3 text-sm leading-5 text-gray-700">{{ group_item.product_name }} </td>
                                                <td class="px-8 py-3 text-sm leading-5 text-gray-700">{{ group_item.quantity }} {{ group_item.item_unit }}</td>
                                                <td class="px-8 py-3 text-sm leading-5 text-gray-700">{{ group_item.avg_rate }} </td>
                                                <td class="px-8 py-3 text-sm leading-5 text-gray-700">{{ group_item.total }} </td>
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
    </div>

</template>

<script>
import { Head, Link } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Alert from '@/Components/Alert.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import StatusOptions from '@/Components/StatusOptions.vue';

import {
    ChevronDownIcon,
    EnvelopeIcon,
    PencilSquareIcon,
    PhoneIcon,
    PlusIcon,
    TrashIcon,
    PrinterIcon,
} from '@heroicons/vue/24/solid';

export default {
    layout: AuthenticatedLayout,

    components: {
        Alert,
        Breadcrumb,
        Head,
        Link,StatusOptions,

        PrinterIcon,
        ChevronDownIcon,
        EnvelopeIcon,
        PencilSquareIcon,
        PhoneIcon,
        PlusIcon,
        TrashIcon,
    },

    props: {
        string_change: Object,
        requisition: Object,
        auth: Object,
    },

    setup(props) {
        const breadcrumbs = [
            { name: props.string_change.product + ' Requisitions', href: route('product_requisition.index'), current: false },
            { name: 'Detail Page', href: '#', current: false },
        ]

        return {
            breadcrumbs,
        }
    },
}
</script>
