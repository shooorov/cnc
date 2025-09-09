<template>

	<Head title="Deliveries" />

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
					<div class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
						<p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">
							Pending Deliveries
							<!-- <span v-if="start_date != end_date">({{ start_date }} - {{ end_date }})</span>
							<span v-else>({{ start_date }})</span> -->
						</p>
						<div class="flex-shrink-0 flex space-x-3">

						</div>

					</div>

					<Alert />

					<table class="table-auto sm:table-fixed min-w-full w-full" id="table" :data-url="route('delivery.load')">
						<thead>
							<tr>
								<th
									class="w-10 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">
									S.N.</th>
								<th
									class="w-20 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">
									Number</th>
								<th
									class="w-20 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">
									Date</th>
								<th
									class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">
									Address</th>
								<th
									class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">
									Customer</th>
								<th
									v-if="!auth?.user?.is_rider"
									class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">
									Rider</th>
								<th
									class="w-28 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">
									Action</th>
							</tr>
						</thead>
                        <tbody class="bg-white">
                            <tr v-for="(order, index) in orders" :key="order.id" :class="[index % 2 == 0 ? 'bg-white' : 'bg-gray-50', 'border-b']">
                                <td class="p-2 text-center"> {{ Number(index) + 1 }} </td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
									<div class="text-sm leading-5 text-gray-700">{{ order.invoice_number }}</div>
                                </td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
									<div class="text-sm leading-5 text-gray-700">{{ order.datetime_format }}</div>
                                </td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
									<div class="text-sm leading-5 text-gray-700">{{ order.order_delivery?.address }}</div>
                                </td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
									<div class="text-sm leading-5 text-gray-700">{{ order.order_delivery?.customer_mobile }}</div>
                                </td>

                                <td v-if="!auth?.user?.is_rider" class="px-3 py-1 whitespace-wrap break-words">
									<div class="text-sm leading-5 text-gray-700">{{ order.order_delivery?.rider_name }}</div>
                                </td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
                                    <div class="flex justify-end">
                                        <Link :href="route('delivery.show', order.id)" class="text-primary-600 hover:text-primary-800 ml-3" title="detail">
                                            <ArrowTopRightOnSquareIcon class="w-6 h-6" aria-hidden="true" />
                                        </Link>
                                        <Link :href="route('delivery.edit', order.id)" class="text-indigo-600 hover:text-indigo-800 ml-3" title="edit">
                                            <PencilSquareIcon class="w-6 h-6" aria-hidden="true" />
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
import { reactive } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusOptions from '@/Components/StatusOptions.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Alert from '@/Components/Alert.vue';
import Status from '@/Components/Status.vue';
import Combobox from '@/Components/Combobox.vue';

import {
	PlusIcon,
	ChevronUpDownIcon,
} from '@heroicons/vue/24/solid';

import {
	ArrowTopRightOnSquareIcon,
	FunnelIcon,
	PencilSquareIcon,
	PrinterIcon,
	TrashIcon,
	MagnifyingGlassIcon,
	ShoppingCartIcon,
	ArrowPathIcon,
	TableCellsIcon,
} from '@heroicons/vue/24/outline';

export default {
	layout: AuthenticatedLayout,

	components: {
		Alert,
		Breadcrumb,
		Combobox,
		Head,
		Menu,
		MenuButton,
		MenuItem,
		MenuItems,
		Link,
		Status,
		StatusOptions,

		PlusIcon,
		ChevronUpDownIcon,
		ArrowTopRightOnSquareIcon,
		FunnelIcon,
		PencilSquareIcon,
		PrinterIcon,
		TrashIcon,
		MagnifyingGlassIcon,
		ShoppingCartIcon,
		ArrowPathIcon,
		TableCellsIcon,
	},

	props: {
		
		string_change: Object,
		navigation: Object,
		auth: Object,

		orders: Array,
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
			if (confirm(message)) { router.delete(route); }
		},
	},

	setup(props) {
		const breadcrumbs = [
			{ name: 'Deliveries', href: route('delivery.index'), current: false },
			{ name: 'List Page', href: '#', current: false },
		];

		const form = reactive(props.filter)

		function clearFilter() {
			for (const [key, value] of Object.entries(form)) {
				form[key] = '';
			}
		}

		function submit() {
            router.visit(route('delivery.index'), {
  				data: form,
			});
		}

		return {
			breadcrumbs,
			form,
			submit,
			clearFilter
		}
	},
}
</script>
