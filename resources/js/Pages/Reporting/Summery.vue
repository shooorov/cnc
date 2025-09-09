<script setup>
import { reactive } from 'vue';
import { router, Head } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Alert from '@/Components/Alert.vue';

import {
	Dialog,
	DialogOverlay,
	Menu,
	MenuButton,
	MenuItem,
	MenuItems,
	TransitionChild,
	TransitionRoot
} from '@headlessui/vue';

import {
	ArrowPathIcon,
	ChevronUpDownIcon,
	MagnifyingGlassIcon,
	PrinterIcon,
	TableCellsIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
	navigation: Object,
	string_change: Object,
	filter: Object,
	cards: Array,
})

const form = reactive({
	end_date: props.filter.end_date,
	start_date: props.filter.start_date,
})

const clearFilter = () => {
	for (const [key, value] of Object.entries(form)) {
		form[key] = '';
	}
}

const submit = () => {
	router.visit(route('summary.overview'), {
		data: form,
	});
}

const breadcrumbs = [
	{ name: 'Summary', href: '#', current: true },
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
						class="flex flex-col sm:flex-row sm:justify-between items-end px-4 py-5 border-b border-gray-200 sm:px-8">
						<div class="flex-1">
							<h3 class="text-base font-semibold leading-6 text-gray-900">Summary</h3>
							<p class="mt-1 text-sm text-gray-500">
								{{ props.filter.start_date.split("-").reverse().join("/") + " - " +
									props.filter.end_date.split("-").reverse().join("/") }}
							</p>
						</div>
                        <div class="flex-shrink-0 flex space-x-3">
                            <Menu as="div" class="relative inline-block">
                                <div>
                                    <MenuButton
                                        class="max-w-xs bg-white rounded-md border border-gray-300 flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 py-1 lg:py-2 px-2 lg:rounded-md lg:hover:bg-gray-50">
                                        <PrinterIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                                        <span class="ml-2 text-gray-700 text-sm font-medium lg:block">
                                            <span class="sr-only">Open report menu for </span>Report
                                        </span>
                                        <ChevronUpDownIcon class="shrink-0 ml-1 h-5 w-5 text-gray-400 lg:block" aria-hidden="true" />
                                    </MenuButton>
                                </div>

                                <transition
                                    enter-active-class="transition ease-out duration-100"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95">
                                    <MenuItems
                                        class="z-10 origin-top-right absolute right-0 mt-2 w-48 lg:w-40 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                                        <div class="py-1">
                                            <MenuItem v-if="navigation.routes.includes('report.sale')">
                                                <a
                                                    :href="route('report.sale', form)"
                                                    target="_blank"
                                                    class="text-gray-700 group flex items-center w-full px-4 py-2 text-sm hover:bg-gray-100">
                                                    Sale
                                                </a>
                                            </MenuItem>
                                            <MenuItem v-if="navigation.routes.includes('report.employee')">
                                                <a
                                                    :href="route('report.employee', form)"
                                                    target="_blank"
                                                    class="text-gray-700 group flex items-center w-full px-4 py-2 text-sm hover:bg-gray-100">
                                                    Employee
                                                </a>
                                            </MenuItem>
                                            <MenuItem v-if="navigation.routes.includes('report.vat')">
                                                <a
                                                    :href="route('report.vat', form)"
                                                    target="_blank"
                                                    class="text-gray-700 group flex items-center w-full px-4 py-2 text-sm hover:bg-gray-100">
                                                    VAT
                                                </a>
                                            </MenuItem>
                                        </div>
                                    </MenuItems>
                                </transition>
                            </Menu>

                            <Menu as="div" class="relative inline-block">
                                <div>
                                    <MenuButton
                                        class="max-w-xs bg-white rounded-md border border-gray-300 flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 py-1 lg:py-2 px-2 lg:rounded-md lg:hover:bg-gray-50">
                                        <TableCellsIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                                        <span class="ml-2 text-gray-700 text-sm font-medium lg:block">
                                            <span class="sr-only">Open report menu for </span>Export
                                        </span>
                                        <ChevronUpDownIcon class="shrink-0 ml-1 h-5 w-5 text-gray-400 lg:block" aria-hidden="true" />
                                    </MenuButton>
                                </div>

                                <transition
                                    enter-active-class="transition ease-out duration-100"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95">
                                    <MenuItems
                                        class="z-10 origin-top-right absolute right-0 mt-2 w-48 lg:w-40 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                                        <div class="py-1">
                                            <MenuItem v-if="navigation.routes.includes('export.orders')">
                                                <a
                                                    :href="route('export.orders', form)"
                                                    class="text-gray-700 group flex items-center w-full px-4 py-2 text-sm hover:bg-gray-100">
                                                    Order
                                                </a>
                                            </MenuItem>
                                            <MenuItem v-if="navigation.routes.includes('export.sales')">
                                                <a
                                                    :href="route('export.sales', form)"
                                                    class="text-gray-700 group flex items-center w-full px-4 py-2 text-sm hover:bg-gray-100">
                                                    Sale
                                                </a>
                                            </MenuItem>
                                            <MenuItem v-if="navigation.routes.includes('export.expenses')">
                                                <a
                                                    :href="route('export.expenses', form)"
                                                    class="text-gray-700 group flex items-center w-full px-4 py-2 text-sm hover:bg-gray-100">
                                                    Expense
                                                </a>
                                            </MenuItem>
                                        </div>
                                    </MenuItems>
                                </transition>
                            </Menu>
                        </div>
					</div>

					<Alert />

					<form @submit.prevent="submit">
						<dl class="px-5 py-5 mx-auto max-w-5xl">
							<div class="py-2 sm:grid sm:grid-cols-8 sm:gap-4">
								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1"> </dd>

								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">Start Date</label>
									<input v-model="form.start_date" type="date"
										class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
								</dd>

								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
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

					<div class="px-6 py-8">
						<table class="table-auto sm:table-fixed min-w-full w-full">
							<thead>
								<tr>
									<th
										class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">
										Description</th>
									<th
										class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">
										Value</th>
								</tr>
							</thead>
							<tbody class="bg-white">
								<tr v-for="card, index in cards" :key="index"
									:class="[index % 2 == 0 ? 'bg-white' : 'bg-gray-50', 'border-b']">
									<td class="px-5 py-2 whitespace-wrap">
										<div class="text-sm leading-5 text-gray-700">{{ card.name }} &nbsp;</div>
									</td>

									<td class="px-5 py-2 whitespace-wrap text-right">
										<div class="text-sm leading-5 text-gray-700">{{ card.amount }}</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</AuthenticatedLayout></template>