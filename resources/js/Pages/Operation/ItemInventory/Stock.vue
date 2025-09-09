<template>
    <Head title="Inventories" />

    <div>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>
                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                        <Link :href="route('item_inventory.create')" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
                            <PlusIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                            In
                        </Link>
                        <!-- <Link :href="route('prepare.create')" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
                            <MinusIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                            Out
                        </Link> -->
                    </div>
                </div>
            </div>
        </div>

        <div class="py-5">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">{{ string_change.item_inventory }} Stock</p>

                        <div class="flex-shrink-0 flex space-x-3">
                            <Link v-if="navigation.routes.includes('item_inventory.compare')" :href="route('item_inventory.compare')" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
                                <ClipboardDocumentIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                                Compare
                            </Link>

                            <a v-if="navigation.routes.includes('report.item_stock')" :href="route('report.item_stock')" target="_blank" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-0 focus:ring-offset-gray-100 focus:ring-primary-400">
                                <PrinterIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                                Print
                            </a>
                        </div>
                    </div>

                    <Alert />

                    <table class="table-auto sm:table-fixed min-w-full w-full" id="table">
                        <thead>
                            <tr>
                                <th class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">S.N.</th>
                                <th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">{{ string_change.item }}</th>
                                <th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">Quantity</th>
                                <th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">Avg Rate</th>
                                <th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">Min Limit</th>
                                <th class="w-20 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr v-for="(item, index) in items" :key="item.id" :class="[index % 2 == 0 ? 'bg-white' : 'bg-gray-50', 'border-b', item.min_limit >= item.quantity? 'text-red-400' : 'text-gray-700']">
                                <td class="p-2 text-center"> {{ Number(index) + 1 }} </td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
                                    <div class="text-sm leading-5 text-left">{{ item.name }}</div>
                                </td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
                                    <div class="text-sm leading-5 text-center">{{ item.quantity }} {{ item.unit }}</div>
                                </td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
                                    <div class="text-sm leading-5 text-center">{{ item.avg_rate }}</div>
                                </td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
                                    <div class="text-sm leading-5 text-center">{{ item.min_limit }} {{ item.unit }}</div>
                                </td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
                                    <div class="flex justify-end">
                                        <Link :href="route('item.edit', item.id)" class="text-indigo-600 hover:text-indigo-800 ml-3" title="edit">
                                            <PencilSquareIcon class="w-6 h-6" aria-hidden="true" />
                                        </Link>
                                        <Link :href="route('item_inventory.in', {item_id: item.id})" class="text-indigo-600 hover:text-indigo-800 ml-3" title="edit">
                                            <CircleStackIcon class="w-6 h-6" aria-hidden="true" />
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusOptions from '@/Components/StatusOptions.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Alert from '@/Components/Alert.vue';

import Status from '@/Components/Status.vue';

import {
    PlusIcon,
    PrinterIcon,
    MinusIcon,
} from '@heroicons/vue/24/solid';

import {
    ArrowTopRightOnSquareIcon,
    CircleStackIcon,
    ClipboardDocumentIcon,
    PencilSquareIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';

export default {
    layout: AuthenticatedLayout,

    components: {
        Alert,
        Breadcrumb,
        Head,
        Link,Status,
        StatusOptions,

        ArrowTopRightOnSquareIcon,
        CircleStackIcon,
        ClipboardDocumentIcon,
        MinusIcon,
        PlusIcon,
        PrinterIcon,
        PencilSquareIcon,
        TrashIcon,
    },

    props:{
        string_change: Object,
		navigation: Object,

		filter: Object,
        items: Array,
    },

    mounted() {
        $('#table').DataTable({
            lengthMenu: [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
            length: 10,
            dom: "<'flex justify-center sm:justify-end mb-3'B><'flex flex-col sm:flex-row justify-between'lf><'block overflow-auto'rt><'flex flex-col sm:flex-row justify-between'ip>",
            buttons: [
                'copy', 'excel'
            ],
        });
    },

    setup(props) {
        const breadcrumbs = [
            { name: props.string_change.item_inventory + ' Stock', href: route('item_inventory_stock'), current: false },
            { name: 'Stock Page', href: '#', current: false },
        ];

        return {
            breadcrumbs,
        }
    },

}
</script>
