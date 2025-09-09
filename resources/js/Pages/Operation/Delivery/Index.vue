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
							Deliveries
							<!-- <span v-if="start_date != end_date">({{ start_date }} - {{ end_date }})</span>
							<span v-else>({{ start_date }})</span> -->
						</p>
						<div class="flex-shrink-0 flex space-x-3">

						</div>

					</div>

					<form @submit.prevent="submit">
						<dl class="px-5 py-5 mx-auto max-w-5xl">
							<div class="py-2 sm:grid sm:grid-cols-8 sm:gap-4">
								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">Manager</label>
									<Combobox class="mt-1" v-model="filter.creator_id" :items="creators" />
								</dd>

								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">Rider</label>
									<Combobox class="mt-1" v-model="filter.rider_id" :items="riders" />
								</dd>

								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">Customer</label>
									<Combobox class="mt-1" v-model="filter.customer_id" :items="customers" />
								</dd>

								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">Start Date</label>
									<input v-model="filter.start_date" type="date"
                                        class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
								</dd>

								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">End Date</label>
									<input v-model="filter.end_date" type="date"
										class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
								</dd>

								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">Action</label>
									<div class="inline-flex mt-1 rounded" role="group">
										<button type="submit"
											class="inline-flex items-center px-4 py-2 border border-transparent rounded-l shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
											<MagnifyingGlassIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
											Search
										</button>

										<button @click="clearFilter" type="reset"
											class="inline-flex items-center px-4 py-1 border border-primary-600 rounded-r shadow-sm text-sm font-medium text-primary-700 bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
											<ArrowPathIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
											Clear
										</button>
									</div>
								</dd>
							</div>
						</dl>
					</form>

					<Alert />

					<table class="table-auto sm:table-fixed min-w-full w-full" id="ajax_table" :data-url="route('delivery.load')">
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
									class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">
									Rider</th>
								<th
									class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">
									Status</th>
								<th
									class="w-28 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">
									Action</th>
							</tr>
						</thead>
						<tbody class="bg-white">
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

		filter: Object,
		end_date: String,
		start_date: String,

		products: Array,
		creators: Array,
		riders: Array,
		customers: Array,
	},

	mounted() {
		$("#ajax_table").DataTable({
			responsive: true,
			serverSide: true,
			processing: true,
			lengthMenu: [
				[10, 25, 50, 100, 200],
				[10, 25, 50, 100, 200]
			],
			length: 10,
			dom: "<'flex flex-col sm:flex-row justify-between'lf><'block overflow-auto 'rt><'flex flex-col sm:flex-row justify-between items-center'ip>",
			ajax: {
				url: $("#ajax_table").data('url'),
				type: "GET",
				data: this.form
			},
			// createdRow: function (row, data) {
			// 	if (data.is_complete)
			// 		$(row).addClass('text-gray-700');
			// 	else
			// 		$(row).addClass('text-red-700');
			// },
			order: [[2, 'desc']],
			columns: [
				{
					data: 'SrNo',
					class: "px-2 sm:px-4 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5 sorting_1",
					render: function (data, type, row, meta) {
						return meta.row + meta.settings._iDisplayStart + 1;
					}
				},
				// {
				// 	data: 'SrNo',
				// 	class: "px-2 sm:px-4 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5 sorting_1",
				// },
				{
					class: "px-2 sm:px-4 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5",
					data: "invoice_number",
					sortable: false,
				},
				{
					class: "px-2 sm:px-4 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5",
					data: "datetime_format"
				},
				{
					class: "px-2 sm:px-4 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5",
					data: "address",
				},
				{
					class: "px-2 sm:px-4 py-1 sm:py-2 whitespace-nowrap border-b border-gray-200 text-sm leading-5",
					data: "customer_name",
				},
				{
					class: "px-2 sm:px-4 py-1 sm:py-2 whitespace-nowrap border-b border-gray-200 text-sm leading-5",
					data: "rider_name",
				},
				{
					class: "px-2 sm:px-4 py-1 sm:py-2 whitespace-nowrap border-b border-gray-200 text-sm leading-5",
					data: "status",
				},
				{
					class: "px-2 sm:px-4 py-1 sm:py-2 whitespace-wrap border-b border-gray-200 text-sm leading-5",
					data: "action",
					sortable: false,
				}
			]
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

		const colorClasses = {
			pending: 'bg-yellow-100 text-yellow-900 rounded-full',
			accept: 'bg-blue-100 text-blue-900 rounded-full',
			collect: 'bg-rose-100 text-rose-900 rounded-full',
			delivered: 'bg-green-100 text-green-900 rounded-full',
		};

		return {
			breadcrumbs,
			form,
			submit,
			clearFilter
		}
	},
}
</script>
