<script setup>
import { reactive } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Alert from '@/Components/Alert.vue';
import InputError from '@/Components/InputError.vue';
import Combobox from '@/Components/Combobox.vue';

import {
	PlusIcon,
	XMarkIcon,
	ArrowPathIcon,
} from '@heroicons/vue/24/solid';

import {
	PencilSquareIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
	string_change: Object,
	types: Array,
	category: Object,
	products: Array,
})

const form = reactive({
	name: props.category.name,
	image: props.category.image,
	image_default: props.category.image_default,
	image_removed: false,
	group_items: props.category.items.length ? props.category.items : [{
		product_id: '',
	}],
})

const onFileInput = (file) => {
	return URL.createObjectURL(file);
}

const removeItem = (index) => {
	if (confirm('Are you sure you want to delete this element?')) {
		form.group_items.splice(index, 1);
	}
}

const addItem = () => {
	form.group_items.push({
		product_id: '',
	});
}
const breadcrumbs = [
	{ name: 'Online ' + props.string_change.product_s + ' Categories', href: route('online_product_category.index'), current: false },
	{ name: 'Edit Page', href: '#', current: false },
]

const submit = () => {
	router.post(route('online_product_category.update', props.category.id), form)
}
</script>
<template>
	<Head :title="'Edit Online ' + string_change.product + ' Category'" />

	<AuthenticatedLayout>
		<div class="bg-white shadow">
			<div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
				<div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
					<div class="flex-1 min-w-0">
						<Breadcrumb :breadcrumbs="breadcrumbs" />
					</div>
					<div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
						<Link :href="route('online_product_category.create')"
							class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
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
						<p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0"> {{ 'Online ' +
							string_change.product + ' Category Edit' }}</p>

						<div class="flex-shrink-0 flex space-x-3">
						</div>
					</div>

					<Alert />

					<form @submit.prevent="submit">
						<dl class="space-y-4 sm:space-y-6 px-5 py-6">

							<div class="max-w-xl mx-auto">
								<div class="grid grid-cols-4 gap-4">
									<div class="col-span-4 sm:col-span-4">
										<label class="mb-1 block text-sm font-medium text-gray-700"> Image </label>
										<div class="relative h-32 w-64">
											<div
												class="flex text-sm text-gray-700 border border-gray-400 rounded-md bg-gray-50">
												<img class="object-cover h-32 w-64" :src="form.image"
													title="Click to upload image">
												<div class="absolute bottom-0 bg-gray-50 w-full text-center opacity-60 cursor-pointer"
													@click="$refs.image.click()"><span class="text-xs text-black">Click to
														upload</span></div>
												<input ref="image" type="file" class="hidden"
													@input="form.image_file = $event.target.files[0]"
													@change="form.image = onFileInput($event.target.files[0]);"
													accept=".png, .jpg, .jpeg" />
											</div>
											<div v-show="form.image != form.image_default"
												class="absolute top-0 right-0 px-0 py-0 bg-gray-50 opacity-60"
												title="Remove Image">
												<XMarkIcon class="w-5 h-5 text-red-500 hover:text-red-700 cursor-pointer"
													aria-hidden="true"
													@click="form.image = form.image_default; form.image_file = null; form.image_removed = true" />
											</div>
											<div v-show="form.image != category.image"
												class="absolute top-0 left-0 px-0 py-0 bg-gray-50 opacity-60"
												title="Refresh Image">
												<ArrowPathIcon
													class="w-5 h-5 text-primary-500 hover:text-primary-700 cursor-pointer"
													aria-hidden="true"
													@click="form.image = category.image; form.image_file = null; form.image_removed = false" />
											</div>
										</div>
										<InputError :message="$page.props.errors.image" />
									</div>

									<div class="col-span-4 sm:col-span-4">
										<label class="block text-sm font-medium text-gray-700"> Name <span
												class="text-red-500">*</span> </label>
										<input v-model="form.name" placeholder="Name" type="text"
											class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
										<InputError :message="$page.props.errors.name" />
									</div>
								</div>
							</div>

							<div class="relative my-8">
								<div class="absolute inset-0 flex items-center" aria-hidden="true">
									<div class="w-full border-t border-gray-300" />
								</div>
								<div class="relative flex justify-center"> <span
										class="px-3 bg-white text-lg font-medium text-gray-900"> {{ string_change.product_s
										}} </span> </div>
							</div>

							<datalist id="product_list">
								<option v-for="product in products" :key="product.id" :value="product.name">{{ product.name
								}}</option>
							</datalist>
							<div class="max-w-xl mx-auto">
								<table class="table-auto sm:table-fixed min-w-full divide-y divide-gray-200">
									<thead class="bg-gray-50">
										<tr>
											<th scope="col"
												class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
												{{ string_change.product }}</th>
											<th scope="col"
												class="w-10 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
											</th>
										</tr>
									</thead>

									<tbody class="bg-white divide-y divide-gray-200">
										<tr v-for="(group_item, index) in form.group_items" :key="index"
											class="divide-gray-200">
											<td class="p-0">
												<Combobox class="" v-model="group_item.product_id" :items="products" />
												<!-- <input list="product_list" v-model="group_item.product_name" type="text" class="block w-full px-2 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded-md" /> -->
											</td>
											<td class="px-3 py-2">

												<button v-show="form.group_items.length > 1" @click="[removeItem(index)]"
													type="button"
													class="p-1 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
													<XMarkIcon class="h-4 w-4" aria-hidden="true" />
												</button>
											</td>
										</tr>
									</tbody>

									<tfoot class="bg-white">
										<tr>
											<th colspan="2" class="text-left pt-3">
												<button @click="addItem()" type="button"
													class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
													Add Item
													<PlusIcon class="ml-2 -mr-0.5 h-4 w-4" aria-hidden="true" />
												</button>
											</th>
										</tr>
									</tfoot>
								</table>
							</div>

							<div class="relative">
								<div class="absolute inset-0 flex items-center" aria-hidden="true">
									<div class="w-full border-t border-gray-300" />
								</div>
								<div class="relative flex justify-center ml-4"> <span
										class="px-3 bg-white text-lg font-medium text-gray-900"></span> </div>
							</div>

							<div class="max-w-xl mx-auto">
								<div class="flex justify-end">
									<button type="submit"
										class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
										<PencilSquareIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
										Update
									</button>
								</div>
							</div>
						</dl>
					</form>
				</div>
			</div>
		</div>
	</AuthenticatedLayout>
</template>