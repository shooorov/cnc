<script setup>
import { onUpdated, reactive, watch } from 'vue';
import { router, Head, Link, usePage } from '@inertiajs/vue3';
import {
	PlusIcon,
	MinusIcon,
	ArrowPathIcon,
	PaperClipIcon,
} from '@heroicons/vue/24/solid';

import {
	ClipboardDocumentIcon,
	PencilSquareIcon,
	XMarkIcon,
} from '@heroicons/vue/24/outline';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Alert from '@/Components/Alert.vue';
import InputError from '@/Components/InputError.vue';

const page = usePage()
const props = defineProps({
	string_change: Object,
	navigation: Object,
	item_inventory: Object,
	documents: Array,
})

const deleteRecord = (record_id) => {
	if (confirm("Do you really want to delete this document?")) {
		router.delete(route('document.destroy', record_id));
	}
}

const breadcrumbs = [
	{ name: props.string_change.item_inventory + ' In', href: route('item_inventory.in'), current: false },
	{ name: 'Document Page', href: '#', current: false },
]

const form = reactive({
	document_files: props.item_inventory.document,
})

function submit() {
	router.post(route('item_inventory.document_update', props.item_inventory.id), form)
}

onUpdated(() => {
	props.documents.map(doc => {
		let arr = doc.path.split('/');
		doc.name = arr[arr.length - 1];
	})
})
</script>
<template>
	<Head title="Document Inventory" />

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
					<div
						class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
						<p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">{{
							string_change.item_inventory }} In Document</p>

						<div class="flex-shrink-0 flex space-x-3">
							<!-- <Link v-if="navigation.routes.includes('item_inventory.edit')" :href="route('item_inventory.edit', item_inventory.id)" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
                                <PencilSquareIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                                Edit
                            </Link> -->
							<Link v-if="navigation.routes.includes('item_inventory.compare')"
								:href="route('item_inventory.compare')"
								class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
							<ClipboardDocumentIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
							Compare
							</Link>

							<Link v-if="navigation.routes.includes('item_inventory.in')" :href="route('item_inventory.in')"
								class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
							<XMarkIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
							Skip
							</Link>
						</div>
					</div>

					<Alert />

					<div class="px-4 py-5 sm:p-0">
						<dl class="sm:divide-y sm:divide-gray-200">
							<div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
								<dt class="text-sm font-medium text-gray-500">Inventory Date</dt>
								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{
									item_inventory.datetime_format }}</dd>
							</div>
							<div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
								<dt class="text-sm font-medium text-gray-500">{{ string_change.item_s }}</dt>
								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<table
										class="table-auto sm:table-fixed min-w-full border border-gray-300 divide-y divide-gray-200">
										<thead class="bg-gray-100">
											<tr>
												<th scope="col"
													class="w-80 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ string_change.item }}</th>
												<th scope="col"
													class="w-56 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													Quantity</th>
												<th scope="col"
													class="w-40 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													Rate</th>
												<th scope="col"
													class="w-40 px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													Total</th>
											</tr>
										</thead>

										<tbody class="bg-white divide-y divide-gray-200">
											<tr v-for="(group_item, index) in item_inventory.items" :key="index"
												:class="[index % 2 == 0 ? 'bg-white' : 'bg-primary-50']">
												<td class="px-4 py-2 text-sm leading-5 text-gray-700"> {{
													group_item.item_name }} </td>
												<td class="px-4 py-2 text-sm leading-5 text-gray-700"> {{
													group_item.quantity + ' ' + group_item.item_unit }} </td>
												<td class="px-4 py-2 text-sm leading-5 text-gray-700"> {{ group_item.rate }}
												</td>
												<td class="px-4 py-2 text-sm leading-5 text-gray-700"> {{ group_item.total
												}} </td>
											</tr>
										</tbody>
									</table>
								</dd>
							</div>
							<div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
								<dt class="text-sm font-medium text-gray-500">Documents</dt>
								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<ul role="list"
										:class="[documents.length ? 'border border-gray-200 rounded-md' : '', 'divide-y divide-gray-200']">
										<li v-for="document in documents" :key="document.id"
											class="px-3 py-2 flex items-center justify-between text-sm">
											<div class="w-0 flex-1 flex items-center">
												<PaperClipIcon class="shrink-0 h-5 w-5 text-gray-400" aria-hidden="true" />
												<span class="ml-2 flex-1 w-0 truncate"> {{ document.path }} </span>
											</div>
											<div class="ml-4 shrink-0">
												<a :href="document.full_path" target="_blank"
													class="font-medium text-indigo-600 hover:text-indigo-500"> Download </a>
												<button @click="deleteRecord(document.id)"
													class="ml-3 font-medium text-red-600 hover:text-red-500"> Remove
												</button>
											</div>
										</li>
									</ul>
								</dd>
							</div>
							<div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
								<dt class="text-sm font-medium text-gray-500">Upload Document</dt>
								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<form @submit.prevent="submit">

										<div class="flex mt-1 relative w-full">
											<div class="flex text-sm text-gray-700 items-center">
												<input class="border py-1" ref="document" type="file" multiple
													@input="form.document_files = $event.target.files" accept="*" />
											</div>

											<button type="submit"
												class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
												<PencilSquareIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
												Upload
											</button>
										</div>
										<InputError :message="page.props.errors.document_files" />
									</form>
								</dd>
							</div>
						</dl>
					</div>
				</div>
			</div>
		</div>
	</AuthenticatedLayout>
</template>