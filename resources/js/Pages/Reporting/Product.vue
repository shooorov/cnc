<script setup>
import { reactive, onMounted } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';
import {
	ArrowPathIcon,
	MagnifyingGlassIcon,
	PrinterIcon,
} from '@heroicons/vue/24/outline';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Alert from '@/Components/Alert.vue';
import Combobox from '@/Components/Combobox.vue';

const props = defineProps({
	navigation: Object,
	string_change: Object,
	categories: Array,
	sold_products: Array,
	filter: Object,
})

onMounted(() => {
	$('#table').DataTable({
		lengthProduct: [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
		length: 10,
		dom: "<'flex justify-center sm:justify-end mb-3'B><'flex flex-col sm:flex-row justify-between'lf><'block overflow-auto'rt><'flex flex-col sm:flex-row justify-between'ip>",
		buttons: [
			'copy', 'excel'
		],
	});
})

const form = reactive({
	end_date: props.filter.end_date,
	start_date: props.filter.start_date,
	category_id: props.filter.category_id,
})

const clearFilter = () => {
	for (const [key, value] of Object.entries(form)) {
		form[key] = '';
	}
}

const submit = () => {
	router.visit(route('summary.product'), {
		data: form,
	});
}

const breadcrumbs = [
	{ name: 'Summary', href: route('summary.overview'), current: false },
	{ name: 'Product', href: '#', current: true },
];
</script>
<template>
	<Head title="Products" />

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
				<div class="bg-white shadow sm:rounded-lg">
					<div
						class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
						<div class="flex-1">
							<h3 class="text-base font-semibold leading-6 text-gray-900">Product Summary</h3>
							<p class="mt-1 text-sm text-gray-500">
								{{ props.filter.start_date.split("-").reverse().join("/") + " - " +
									props.filter.end_date.split("-").reverse().join("/") }}
							</p>
						</div>

						<div class="flex-shrink-0 flex space-x-3">
							<a :href="route('export.sale_products', form)" type="button"
								v-if="navigation.routes.includes('export.sale_products')"
								class="inline-flex items-center gap-x-1.5 rounded-md bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 shadow-sm hover:bg-blue-100 border border-blue-100">
								<PrinterIcon class="hidden sm:block -ml-0.5 h-5 w-5" aria-hidden="true" />
								Export
							</a>
						</div>
					</div>

					<Alert />

					<form @submit.prevent="submit">
						<dl class="px-5 py-5 mx-auto max-w-5xl">
							<div class="py-2 sm:grid sm:grid-cols-8 sm:gap-4">
								<!-- <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1"></dd> -->

								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">Category</label>
									<Combobox class="mt-1" v-model="form.category_id" :items="categories"
										placeholder="Select Category" />
								</dd>

								<dd v-if="props.filter.start_date" class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">Start Date</label>
									<input v-model="form.start_date" type="date"
										class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
								</dd>

								<dd v-if="props.filter.end_date" class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">End Date</label>
									<input v-model="form.end_date" type="date"
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

					<table class="table-auto sm:table-fixed min-w-full w-full" id="table">
						<thead>
							<tr>
								<th
									class="w-20 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">
									S.N.</th>
								<th
									class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">
									Name</th>
								<th
									class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">
									Price</th>
								<th
									class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">
									Sold Qty</th>
								<th
									class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">
									Total Amount</th>
							</tr>
						</thead>
						<tbody class="bg-white">
							<tr v-for="(product, index) in sold_products" :key="product.id"
								:class="[Number(index) % 2 == 0 ? 'bg-white' : 'bg-gray-50', 'border-b']">
								<td class="p-2 text-center"> {{ Number(index) + 1 }} </td>

								<td class="p-2 whitespace-wrap">
									<div class="text-sm leading-5 text-gray-700">{{ product.name }}</div>
								</td>

								<td class="p-2 whitespace-wrap">
									<div class="text-sm leading-5 text-gray-700">{{ product.rate }}</div>
								</td>

								<td class="px-3 py-1 whitespace-wrap break-words">
									<div class="text-sm leading-5 text-gray-700">{{ product.product_quantity }}</div>
								</td>

								<td class="p-2 whitespace-wrap">
									<div class="text-sm leading-5 text-gray-700">{{ product.product_amount }}</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</AuthenticatedLayout></template>