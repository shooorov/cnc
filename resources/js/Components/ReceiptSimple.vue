<script setup>
import { Head, usePage } from '@inertiajs/vue3';
const page = usePage()

const props = defineProps({
	branch: Object,
	setting: Object,
	order: Object,
})
</script>
<template>
    <Head title="POS Receipt" />

    <div class="max-w-sm w-80 mx-auto py-8 font-mono">
        <table class="border-collapse w-full">
            <tbody>
                <tr>
                    <td class="align-middle text-xs font-normal" scope="col">
                        <h1 class="w-full text-2xl font-semibold text-center"> {{ setting.company_name }} </h1>
                    </td>
                </tr>
                <tr>
                    <td class="align-middle text-sm font-normal" scope="col">
                        <p class="mx-auto text-center tracking-wider"> {{ branch.address ?? setting.company_address }} </p>
                    </td>
                </tr>
                <tr>
                    <th class="pt-4 align-middle text-sm font-bold uppercase tracking-wider text-center" colspan="2" scope="col">({{ order.status != 'complete' ? 'Bill' : 'Money Receipt' }})</th>
                </tr>
            </tbody>
        </table>

        <hr class="border-t-2 border-gray-500 border-dashed my-2">

        <table class="border-collapse w-full">
            <tbody>
                <tr>
                    <td class="align-middle text-sm font-medium uppercase tracking-wider text-left" scope="col">Date</td>
                    <td class="align-middle text-sm font-medium uppercase text-right" scope="col">{{ order.datetime_format }}</td>
                </tr>
                <tr>
                    <td class="w-1/3 align-middle text-sm font-medium uppercase tracking-wider text-left" scope="col">Invoice</td>
                    <td class="w-2/3 align-middle text-sm font-medium uppercase text-right" scope="col"># {{ order.invoice_number }}</td>
                </tr>
            </tbody>
        </table>

        <hr class="border-t-2 border-gray-500 border-dashed my-2">

        <table class="border-collapse w-full">
            <thead>
                <tr>
                    <!-- <th class="align-top pb-2 text-xs font-semibold uppercase text-center" scope="col">#</th> -->
                    <th  class="w-10 align-top pb-2 text-xs font-semibold uppercase text-center" scope="col">Qty</th>
                    <th class="align-top pb-2 text-xs font-semibold uppercase text-left" scope="col">Description</th>
                    <th class="w-16 align-top pb-2 text-xs font-semibold uppercase text-center" scope="col">Rate</th>
                    <th class="w-16 align-top pb-2 text-xs font-semibold uppercase text-right" scope="col">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item, index in order.products" :key="index">
                    <!-- <td class="align-top py-2 text-xs font-semibold text-center">{{ index + 1 }}.</td> -->
                    <td :class="['align-top font-normal uppercase text-center text-sm']">{{ item.quantity }}</td>
                    <td :class="['align-top font-normal uppercase text-left text-sm']">{{ item.product_name }}</td>
                    <td :class="['align-top font-normal uppercase text-right', 'text-xs py-2']">{{ item.rate }}</td>
                    <td :class="['align-top font-normal uppercase text-right', 'text-xs py-2']">{{ item.total }}</td>
                </tr>
            </tbody>
        </table>

        <hr class="border-t-2 border-gray-500 border-dashed my-2">

        <table class="border-collapse w-full">
            <tbody>
                <tr>
                    <td class="align-middle text-sm font-medium uppercase text-left" colspan="4">Sub Total</td>
                    <td class="align-middle text-sm font-medium uppercase text-right">{{ Number(order.sub_total).toFixed(2) }}</td>
                </tr>
                <tr v-if="order.discount_amount > 0">
                    <td class="align-middle text-sm font-medium uppercase text-left" colspan="4">Discount</td>
                    <td class="align-middle text-sm font-medium uppercase text-right">{{ Number(order.discount_amount).toFixed(2) }}</td>
                </tr>
                <tr v-if="order.service_cost > 0">
                    <td class="align-middle text-sm font-medium uppercase text-left" colspan="4">Service Cost</td>
                    <td class="align-middle text-sm font-medium uppercase text-right">{{ Number(order.service_cost).toFixed(2) }}</td>
                </tr>
                <tr v-if="order.vat_amount > 0">
                    <td class="align-middle text-sm font-medium uppercase text-left" colspan="4">VAT</td>
                    <td class="align-middle text-sm font-medium uppercase text-right">{{ Number(order.vat_amount).toFixed(2) }}</td>
                </tr>
                <tr>
                    <td class="align-middle text-lg font-bold uppercase text-left py-1" colspan="4">Total</td>
                    <td class="align-middle text-lg font-bold uppercase text-right">{{ Number(order.total).toFixed(2) }}</td>
                </tr>
                <tr v-if="order.status == 'complete'">
                    <td class="align-middle text-sm font-medium uppercase text-left" colspan="4">Cash</td>
                    <td class="align-middle text-sm font-medium uppercase text-right">{{ Number(order.cash).toFixed(2) }}</td>
                </tr>
                <tr v-if="order.status == 'complete'">
                    <td class="align-middle text-sm font-medium uppercase text-left" colspan="4">Change</td>
                    <td class="align-middle text-sm font-medium uppercase text-right">{{ Number(order.change).toFixed(2) }}</td>
                </tr>
            </tbody>
        </table>

		<hr class="mb-6 border-t-2 border-gray-500 border-dashed">
    </div>
</template>