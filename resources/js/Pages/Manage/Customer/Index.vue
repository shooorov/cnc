<template>
    <Head title="Customers" />

    <div>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>
                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                        <Link :href="route('customer.create')" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
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
					<div
						class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
						<div class="flex-1">
							<h3 class="text-base font-semibold leading-6 text-gray-900">Customers</h3>
							<p class="mt-1 text-sm text-gray-500">
							</p>
						</div>
						<div class="flex-shrink-0 flex space-x-3">
						</div>
					</div>

                    <Alert />

                    <table class="table-auto sm:table-fixed min-w-full w-full" id="table">
                        <thead>
                            <tr>
                                <th class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">S.N.</th>
                                <th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Name</th>
                                <th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Mobile</th>
                                <th class="w-24 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Balance</th>
                                <th class="w-20 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">Orders</th>
                                <th class="w-24 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr v-for="(customer, index) in customers" :key="customer.id" :class="[index % 2 == 0 ? 'bg-white' : 'bg-gray-50', 'border-b']">
                                <td class="p-2 text-center"> {{ Number(index) + 1 }} </td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
                                    <div class="text-sm leading-5 text-gray-700">{{ customer.name }}</div>
                                    <div v-show="customer.user_name" class="text-sm leading-5 text-gray-700"><span class="font-medium">Admin: </span> {{ customer.user_name }}</div>
                                    <div class="text-sm leading-5 text-gray-600">{{ customer.address }}</div>
                                </td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
                                    {{ customer.mobile }}
                                </td>

                                <td class="px-3 py-1 whitespace-wrap break-words text-right">
                                    {{ customer.balance }}
                                </td>

                                <td class="px-3 py-1 whitespace-wrap break-words text-center">
                                    <Link :href="route('order.index', {customer_id: customer.id})" class="text-indigo-600 hover:text-indigo-800 ml-3" :title="customer.order_count + ' Orders'">
                                        {{ customer.order_count ?? 0 }}
                                    </Link>
                                </td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
                                    <div class="flex justify-end">
                                        <Link :href="route('customer.edit', customer.id)" class="text-indigo-600 hover:text-indigo-800 ml-3" title="edit">
                                            <PencilSquareIcon class="w-6 h-6" aria-hidden="true" />
                                        </Link>
                                        <button @click="destroy(route('customer.destroy', customer.id))" class="text-red-600 hover:text-red-800 ml-3" title="delete">
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
    </div>
</template>

<script>
import { router, Head, Link } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusOptions from '@/Components/StatusOptions.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Alert from '@/Components/Alert.vue';

import Status from '@/Components/Status.vue';

import {
    PlusIcon,
} from '@heroicons/vue/24/solid';

import {
    ArrowTopRightOnSquareIcon,
    PencilSquareIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';

const breadcrumbs = [
    { name: 'Customers', href: route('customer.index'), current: false },
    { name: 'List Page', href: '#', current: false },
];

export default {
    layout: AuthenticatedLayout,

    components: {
        Alert,
        Breadcrumb,
        Head,
        Link,Status,
        StatusOptions,

        PlusIcon,
        ArrowTopRightOnSquareIcon,
        PencilSquareIcon,
        TrashIcon,
    },

    props:{
        customers: Array,
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

    methods: {
        destroy(route, message = "Are you sure you want to delete?") {
            if(confirm(message)) { router.delete(route); }
        },
    },

    setup() {
        return {
            breadcrumbs,
        }
    },

}
</script>
