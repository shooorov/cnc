<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Listbox from '@/Components/Listbox.vue';
import Alert from '@/Components/Alert.vue';
import InputError from '@/Components/InputError.vue';

const page = usePage()

const props = defineProps({
	string_change: Object,
	setting: Object,
	branch: Object,
	order: Object,
	discount_types: Array,
	status: String,
	token: String,
});

const form = useForm({
	order_id: props.order.id,
	sub_total: props.order.sub_total,
	discount_type: props.order.discount_type,
	discount_rate: props.order.discount_rate,
	discount_amount: props.order.discount_amount,
	vat_rate: props.order.vat_rate ?? 5,
	vat_amount: props.order.vat_amount,
	total: props.order.total,
	status: '',
	token: props.token,
});

form.group_items = props.order.products ?? [];

form.total_text = form.sub_total + ' - ' + form.discount_amount + ' + ' + form.vat_amount + ' = ' + form.total

const data = [
	{ name: 'Date', value: props.order.date },
	{ name: 'Customer', value: props.order.customer_name },
	{ name: 'Manager', value: props.order.creator_name },
	{ name: 'Served By', value: props.order.waiter_name },
]

const calculation = () => {
	let discount_rate = Number(form.discount_rate);
	let vat_rate = Number(form.vat_rate);
	let group_items = form.group_items;
	console.log(group_items);

	let sub_total = group_items.reduce((carry, val) => carry + (Number(val.rate) * Number(val.quantity)), 0) ?? 0;

	let non_vatable_amount = group_items.filter(i => !i.vat_applicable).reduce((carry, val) => carry + (Number(val.rate) * Number(val.quantity)), 0) ?? 0;

	let discount_percent = discount_rate / 100;
	let discount_amount = filterAmount((form.discount_type == 'percent') ? sub_total * discount_percent : discount_rate);

	let fraction_discount = filterAmount(non_vatable_amount > 0 ? (((non_vatable_amount / sub_total) ?? 0) * discount_amount) : 0);

	let new_non_vatable_amount = non_vatable_amount - fraction_discount;

	let vatable_amount = sub_total - discount_amount - new_non_vatable_amount;

	let vat_percent = vat_rate / 100;
	let vat_amount = filterAmount(vatable_amount * vat_percent);

	let total = sub_total - discount_amount + vat_amount;

	form.sub_total = sub_total.toFixed(2);
	form.discount_amount = discount_amount.toFixed(2);
	form.vatable_amount = vatable_amount.toFixed(2);
	form.vat_amount = vat_amount;
	form.total = total.toFixed(2);
	form.total_text = form.sub_total + ' - ' + form.discount_amount + ' + ' + form.vat_amount + ' = ' + form.total
}

const filterAmount = (amount) => {
	return true ? Math.round(amount) : amount;
}

const approve = () => {
	if(confirm("Are you sure you want to Approve?")) { 
		page.props.alertMessage = {};

		form.status = 'approved'
		form.post(route('order.approval.update'), {
			onFinish: () => form.reset(),
		})
	}
};

const reject = () => {
	if(confirm("Are you sure you want to Reject?")) { 
		page.props.alertMessage = {};

		form.status = 'rejected'
		form.post(route('order.approval.update'), {
			onFinish: () => form.reset(),
		})
	}
};
</script>
<template>
	<GuestLayout>
		<Head title="Order Approval" />

		<div class="mb-4 text-sm text-gray-600">
				This is a secure area of the application. Only admin can view this page.
		</div>

		<div class="mb-4 text-sm">
			<Alert />

			<div v-if="status != 'pending'">
				<div class="py-1 whitespace-nowrap font-bold text-base text-left">
					<p :class="[status == 'approved' ? 'bg-green-500' : 'bg-red-500', 'py-2 px-3 text-white']">This order is {{ status }}</p>
				</div>
			</div>

			<div class="sm:flex sm:items-start mt-5">
				<h3 class="text-lg font-medium leading-6 text-gray-900">
					# Order need Approval ({{ branch.name }})
				</h3>
			</div>

			<div v-if="status == 'pending'" class="mt-5 grid grid-cols-2 gap-1 text-xs">
				<div class="py-1 whitespace-nowrap sm:text-sm text-stone-500 text-left">
					<Listbox class="w-full" v-model="form.discount_type" :items="discount_types" :hideIcon="true" />
					<InputError class="mt-2" :message="form.errors.discount_type" />
				</div>
				<div class="py-1 whitespace-nowrap sm:text-sm text-stone-500 text-left">
					<input v-model="form.discount_rate" @keyup="calculation()" :placeholder="[form.discount_type == 'percent'? 'Discount Rate in %' : 'Discount in amount']" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off" :class="['block w-full px-4 text-left text-xs sm:text-sm border-stone-300 focus:ring-primary-400 focus:border-primary-400 hover:bg-stone-100 focus:bg-transparent rounded']">
					<InputError class="mt-2" :message="form.errors.discount_rate" />
				</div>
			</div>

			<div class="sm:flex sm:items-start mt-5">
				<dl class="w-full">
					<div v-for="item, index in data" :key="index" :class="[index % 2 == 1 ? 'bg-white' : 'bg-gray-50', 'px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4']">
						<dt class="text-sm font-medium text-gray-500"> {{ item.name }} </dt>
						<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
							<span class="capitalize"> {{ item.value }} </span>
						</dd>
					</div>

					<div class="bg-white px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4']">
						<dt class="text-sm font-medium text-gray-500"> Sub Total </dt>
						<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
							<span class="capitalize"> {{ form.sub_total }} </span>
						</dd>
					</div>
					<div class="bg-gray-50 px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4']">
						<dt class="text-sm font-medium text-gray-500"> Discount </dt>
						<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
							<span class="capitalize"> {{ form.discount_amount }} </span>
						</dd>
					</div>
					<div class="bg-white px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4']">
						<dt class="text-sm font-medium text-gray-500"> VAT </dt>
						<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
							<span class="capitalize"> {{ form.vat_amount }} </span>
						</dd>
					</div>
					<div class="bg-gray-50 px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4']">
						<dt class="text-sm font-medium text-gray-500"> Discount </dt>
						<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
							<span class="capitalize"> {{ form.total_text }} </span>
						</dd>
					</div>
				</dl>
			</div>

			<div class="sm:flex sm:items-start mt-5">
				<h3 class="text-lg font-medium leading-6 text-gray-900">
					# Order {{ string_change.product_s }}
				</h3>
			</div>

			<div class="sm:flex sm:items-start mt-5">
				<table class="border-collapse table-auto w-full text-sm">
					<thead>
						<tr>
							<th class="px-3 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Product</th>
							<th class="px-3 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Rate</th>
							<th class="px-3 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Qty</th>
							<th class="px-3 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Total</th>
						</tr>
					</thead>

					<tbody class="bg-white">
						<tr v-for="(product, index) in form.group_items" :key="index" :class="[index % 2 == 0 ? 'bg-white' : 'bg-gray-50', 'border-b border-gray-100 pl-0 p-3 text-gray-900']">
							<td class="px-3 py-2 whitespace-wrap text-left">{{ product.product_name }}</td>
							<td class="px-3 py-2 whitespace-wrap text-right">{{ product.rate }}</td>
							<td class="px-3 py-2 whitespace-wrap text-center">{{ product.quantity }}</td>
							<td class="px-3 py-2 whitespace-wrap text-right">{{ product.total }}</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div v-if="status == 'pending'" class="mt-5 sm:mt-4 sm:flex sm:justify-between">
				<button type="button" @click="approve" :disabled="form.processing" :class="{ 'opacity-25' : form.processing, 'inline-flex w-full uppercase justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:w-auto sm:text-sm' : true }">Approve</button>
				<button type="button" @click="reject" :disabled="form.processing" :class="{ 'opacity-25' : form.processing, 'mt-3 inline-flex w-full uppercase justify-center rounded-md border border-gray-300 bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm' : true }">Reject</button>
			</div>
		</div>
	</GuestLayout>
</template>