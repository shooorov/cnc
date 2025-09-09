<script setup>
import { reactive } from 'vue';
import { router, Head, Link, useForm, usePage } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Alert from '@/Components/Alert.vue';
import InputError from '@/Components/InputError.vue';
import Combobox from '@/Components/Combobox.vue';

import {
    PlusIcon,
    MinusIcon,
    XMarkIcon,
} from '@heroicons/vue/24/solid';

import {
    ClipboardDocumentIcon,
    PencilSquareIcon,
} from '@heroicons/vue/24/outline';

const page = usePage()

const props = defineProps({
	orders: Array,
	products: Array,
	items: Array,
	units: Array,
	date: String,
})

const blank_item = reactive({
	item_id: '',
	quantity: '',
	unit: '',
	rate: '',
	total: '',
	avg_rate: null,
})

const form = useForm({
	inventory_date: props.date,
	total: null,
	group_items: [ {...blank_item.value} ],
})

const changeItem = (index) => {
	let this_item = form.group_items[index];

	let selected_item = props.items.find(i => i.id == this_item.item_id);
	this_item.avg_rate = selected_item?.avg_rate?? null;

	if(!this_item.unit) {
		this_item.unit = selected_item.unit;
	}
}

const calculation = (index, input) => {
	let this_item = form.group_items[index];
	if(input == 'rate'){
		this_item.total = ((this_item.rate || 0) * (this_item.quantity || 0)).toFixed(2);
		this_item.total = Number(this_item.total)> 0? Number(this_item.total) : '';
	}else{
		this_item.rate = this_item.total && this_item.quantity? (this_item.total / this_item.quantity).toFixed(2) : 0;
		this_item.rate = Number(this_item.rate)> 0? Number(this_item.rate) : '';
	}

	let selected_item = props.items.find(i => i.id == this_item.item_id);
	this_item.avg_rate = selected_item?.avg_rate?? null;

	if(!this_item.unit) {
		this_item.unit = selected_item.unit;
	}
	form.total = form.group_items.reduce((carry, val) => carry + Number(val.total), 0);
}

const removeItem = (index) => {
	if (confirm('Are you sure you want to delete this element?')) {
		form.group_items.splice(index, 1);
	}
}

const addItem = (length = 1) => {
	for(let i= 1; i <= length; i++){
		form.group_items.push({...blank_item.value});
	}
}

const breadcrumbs = [
	{ name: page.props.string_change.item_inventory + ' In', href: route('item_inventory.in'), current: false },
	{ name: 'Create Page', href: '#', current: false },
]

const submit = () => {
	router.post(route('item_inventory.store'), form);
}
</script>
<template>
    <Head title="Create Inventory" />

    <AuthenticatedLayout>
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
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">{{ page.props.string_change.item_inventory }} In Create</p>

                        <div class="flex-shrink-0 flex space-x-3">
                            <Link v-if="page.props.navigation.routes.includes('item_inventory.compare')" :href="route('item_inventory.compare')" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
                                <ClipboardDocumentIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                                Compare
                            </Link>
                        </div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="space-y-4 sm:space-y-6 px-5 py-6">

                            <div class="max-w-xl mx-auto">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700"> Inventory Date <span class="text-red-500">*</span> </label>
                                        <input v-model="form.inventory_date" type="datetime-local" class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                        <InputError :message="$page.props.errors.inventory_date" />
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700"> Total <span class="text-red-500">*</span> </label>
                                        <input v-model="form.total" type="text" placeholder="Total" readonly class="mt-1 block w-full px-4 focus:ring-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded">
                                        <InputError :message="$page.props.errors.total" />
                                    </div>
                                </div>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"> <div class="w-full border-t border-gray-300" /> </div>
                                <div class="relative flex justify-center ml-4"> <span class="px-3 bg-white text-lg font-medium text-gray-900"> {{ page.props.string_change.item_s }} </span> </div>
                            </div>

                            <div class="max-w-5xl mx-auto space-y-4 sm:space-y-6">
                                <table class="table-auto sm:table-fixed min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                            <th scope="col" class="py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ page.props.string_change.item }}</th>
                                            <th scope="col" class="w-1/4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                            <th scope="col" class="w-20 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                            <th scope="col" class="w-40 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                                            <th scope="col" class="w-40 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                            <th scope="col" class="w-24 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Rate</th>
                                            <th scope="col" class="w-6 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(group_item, index) in form.group_items" :key="index">
                                            <td class="px-2"> {{ Number(index) + 1 }} </td>

                                            <td :class="$page.props.errors.group_items && $page.props.errors.group_items[index]?.unit? 'border border-red-500' : 'border-red-500'">
                                                <Combobox class="" v-model="group_item.item_id" :items="items" />
                                                <!-- <select v-model="group_item.item_id" @change="changeItem(index)" class="block w-full px-2 pr-6 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                                    <option value="">Select</option>
                                                    <option v-for="item in items" :key="item.id" :value="item.id">{{ item.name }}</option>
                                                </select> -->
                                            </td>

                                            <td>
                                                <input v-model="group_item.quantity" @keyup="calculation(index, 'rate')" placeholder="Quantity" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="block w-full px-4 pr-24 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                            </td>

                                            <td>
                                                <select v-model="group_item.unit" class="block w-full px-2 pr-6 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                                    <option value="">Unit</option>
                                                    <option v-for="name, key in units" :key="key" :value="key">{{ key }}</option>
                                                </select>
                                            </td>

                                            <td>
                                                <input v-model="group_item.rate" @change="calculation(index, 'rate')" @keyup="calculation(index, 'rate')" placeholder="Rate" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off" class="block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                            </td>

                                            <td>
                                                <input v-model="group_item.total" @change="calculation(index, 'total')" @keyup="calculation(index, 'total')" placeholder="Total" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off" class="block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                            </td>

                                            <td>
                                                <input v-model="group_item.avg_rate" readonly placeholder="Avg Rate" type="text" class="block w-full px-4 focus:ring-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded">
                                            </td>

                                            <td>
                                                <button @click="removeItem(index)" type="button" class="p-1 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    <XMarkIcon class="h-4 w-4" aria-hidden="true" />
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>

                                    <tfoot class="bg-white">
                                        <tr>
                                            <th scope="col" class="py-2 text-left text-lg font-medium font-mono"></th>
                                            <th colspan="5" class="text-left pt-3">
                                                <button @click="addItem()" type="button" class="inline-flex items-center mr-3 px-4 py-2 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    Add Item
                                                    <PlusIcon class="ml-2 -mr-0.5 h-4 w-4" aria-hidden="true" />
                                                </button>
                                                <button @click="addItem(5)" type="button" class="inline-flex items-center mr-3 px-4 py-2 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    Add 5 Item
                                                    <PlusIcon class="ml-2 -mr-0.5 h-4 w-4" aria-hidden="true" />
                                                </button>
                                            </th>
                                            <th scope="col" class="py-2 text-left text-lg font-medium font-mono">{{ Number(form.total).toFixed(2 ) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"> <div class="w-full border-t border-gray-300" /> </div>
                                <div class="relative flex justify-center ml-4"> <span class="px-3 bg-white text-lg font-medium text-gray-900"></span> </div>
                            </div>

                            <div class="max-w-5xl mx-auto space-y-4 sm:space-y-6">
                                <div class="flex justify-start">
                                    <button type="submit" class="mr-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                        <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                        Create
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