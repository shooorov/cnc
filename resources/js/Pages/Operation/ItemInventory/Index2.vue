<script setup>
import { reactive, onMounted } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusOptions from '@/Components/StatusOptions.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Alert from '@/Components/Alert.vue';

import Status from '@/Components/Status.vue';

import {
	PlusIcon,
	MinusIcon,
	XMarkIcon,
} from '@heroicons/vue/24/solid';

import {
	ArrowPathIcon,
	ArrowTopRightOnSquareIcon,
	MagnifyingGlassIcon,
	ClipboardDocumentIcon,
	PencilSquareIcon,
	PrinterIcon,
	TrashIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
	auth: Object,
	string_change: Object,
	navigation: Object,

	filter: Object,
	direction: String,

	end_date: String,
	start_date: String,

	item_inventories: Array,
})

onMounted(() => {
	$('#table').DataTable({
		lengthMenu: [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
		length: 10,
		dom: "<'flex justify-center sm:justify-end mb-3'B><'flex flex-col sm:flex-row justify-between'lf><'block overflow-auto'rt><'flex flex-col sm:flex-row justify-between'ip>",
		buttons: [
			'copy', 'excel'
		],
	});
})

const form = reactive({
	item_id: props.filter.item_id,
	end_date: props.filter.end_date,
	start_date: props.filter.start_date,
})

const destroy = (route, message = "Are you sure you want to delete?") => {
	if (confirm(message)) { router.delete(route); }
}

const breadcrumbs = [
	{ name: props.string_change.item_inventory + ' In', href: route('item_inventory.in'), current: false },
	{ name: 'List Page', href: '#', current: false },
];

const clearFilter = () => {
	for (const [key, value] of Object.entries(form)) {
		form[key] = '';
	}
}

const submit = () => {
	if (props.direction == 'in') {
		router.visit(route('item_inventory.in'), {
			data: form,
		});
	} else {
		router.visit(route('item_inventory_out'), {
			data: form,
		});
	}
}

</script>
<template>
	<Head title="Inventories" />

	<AuthenticatedLayout>
		<div class="bg-white shadow">
			<div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
				<div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
					<div class="flex-1 min-w-0">
						<Breadcrumb :breadcrumbs="breadcrumbs" />
					</div>
					<div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
						<Link :href="route('item_inventory.create')"
							class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
						<PlusIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
						In
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
							<h3 class="text-base font-semibold leading-6 text-gray-900">
								{{ string_change.item_inventory }} {{ direction }}
							</h3>
							<p class="mt-1 text-sm text-gray-500">
								{{ (props.filter.start_date ? props.filter.start_date.split("-").reverse().join("/") : '') +
									" - " +
									(props.filter.end_date ? props.filter.end_date.split("-").reverse().join("/") : '') }}
							</p>
						</div>
						<div class="flex-shrink-0 flex space-x-3">
							<Link v-if="navigation.routes.includes('item_inventory.activities')"
								:href="route('item_inventory.activities')"
								class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
							<ClipboardDocumentIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
							Activities
							</Link>

							<Link v-if="navigation.routes.includes('item_inventory.compare')"
								:href="route('item_inventory.compare')"
								class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
							<ClipboardDocumentIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
							Compare
							</Link>

							<a v-if="navigation.routes.includes('report.item_inventory')"
								:href="route('report.item_inventory', { item_id: form.item_id, start_date: form.start_date, end_date: form.end_date })"
								target="_blank"
								class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-0 focus:ring-offset-gray-100 focus:ring-primary-400">
								<PrinterIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
								Print
							</a>
						</div>
					</div>
					<form @submit.prevent="submit">
						<dl class="px-5 py-5 mx-auto max-w-5xl">
							<div class="py-2 sm:grid sm:grid-cols-8 sm:gap-4">
								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">Start Date</label>
									<input v-model="form.start_date" type="date"
										class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
								</dd>

								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">End Date</label>
									<input v-model="form.end_date" type="date"
										class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
								</dd>

								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">Action</label>
									<div class="inline-flex mt-1 rounded" role="group">
										<button type="submit"
											class="inline-flex items-center px-4 py-2 border border-transparent rounded-l shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
											<MagnifyingGlassIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
											Search
										</button>

										<button @click="clearFilter"
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

					<table class="table-auto sm:table-fixed min-w-full w-full" id="table">
						<thead>
							<tr>
								<th
									class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">
									S.N.</th>
								<th
									class="w-40 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">
									<span v-show="direction == 'in'">Total / </span> Date
								</th>
								<th
									class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">
									{{ string_change.item }} Information</th>
								<th v-show="direction == 'in'"
									class="w-24 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">
									Action</th>
							</tr>
						</thead>
						<tbody class="bg-white">
							<tr v-for="(item_inventory, index) in item_inventories" :key="item_inventory.id"
								:class="[index % 2 == 0 ? 'bg-white' : 'bg-gray-50', 'border-b']">
								<td class="p-2 text-center"> {{ Number(index) + 1 }} </td>

								<td class="px-3 py-1 whitespace-wrap break-words">
									<div v-show="direction == 'in'" class="text-sm leading-5 text-gray-7600">
										{{
											item_inventory.total }} </div>
									<div class="text-sm leading-5 text-gray-7600">{{ item_inventory.datetime_format }}</div>
									<!-- <div v-show="auth.user?.is_admin" class="text-sm leading-5 text-gray-7600">{{  }}</div> -->
								</td>

								<td class="px-3 py-1 whitespace-wrap break-words">
									<table class="table-auto sm:table-fixed min-w-full w-full text-xs divide-y divide-gray-300">
										<thead>
											<tr>
												<th class="w-5 px-2 py-1 border border-gray-300">#</th>
												<th class="px-2 py-1 border border-gray-300">{{ string_change.item }}</th>
												<th class="w-2/5 px-2 py-1 border border-gray-300 text-right">
													<div class="flex items-center">
														<div class="px-1 w-16 text-right">Qty</div>
														<div v-if="direction == 'in'" class="flex items-center">
															<div class="px-1 text-center">
																<XMarkIcon class="w-4 h-4" aria-hidden="true" />
															</div>
															<div class="px-1 w-12 text-right"> Rate</div>
															<div class="px-1 text-center font-light text-lg/3">
																=
															</div>
															<div class="px-1 w-16 text-left"> Total</div>
														</div>
													</div>
												</th>
											</tr>
										</thead>
										<tbody class="bg-transparent border divide-y divide-gray-300">
											<tr v-for="(item, key) in item_inventory.group_items" :key="item.id">
												<td class="px-2 py-1 border border-gray-300 text-center">
													{{ (key + 1) }}
												</td>
												<td class="px-2 py-1 capitalize border border-gray-300 text-left">
													{{ item.item_name }}
												</td>
												<td class="px-2 py-1 border border-gray-300 text-right">
													<div class="flex items-center">
														<div class="px-1 w-16 text-right">{{ item.required_quantity_unit }}</div>
														<div v-if="direction == 'in'" class="flex items-center">
															<div class="px-1 text-center">
																<XMarkIcon class="w-4 h-4" aria-hidden="true" />
															</div>
															<div class="px-1 w-12 text-right">{{ Number(item.rate) }}</div>
															<div class="px-1 text-center font-light text-lg/3">
																=
															</div>
															<div class="px-1 w-16 text-left">{{ Number(item.total) }} TK</div>
														</div>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</td>

								<td v-show="direction == 'in'" class="p-2">
									<div class="flex justify-end">
										<Link :href="route('item_inventory.edit', item_inventory.id)"
											class="text-indigo-600 hover:text-indigo-800 ml-3" title="edit">
										<PencilSquareIcon class="w-6 h-6" aria-hidden="true" />
										</Link>
										<button @click="destroy(route('item_inventory.destroy', item_inventory.id))"
											class="text-red-600 hover:text-red-800 ml-3" title="delete">
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
	</AuthenticatedLayout>
</template>