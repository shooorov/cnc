<script setup>
import { usePage } from '@inertiajs/vue3'
import { reactive } from 'vue';

const page = usePage()

const app = reactive(page.props.app)
const branch = reactive(page.props.branch)
const setting = reactive(page.props.setting)
// const order = reactive(page.props.order)

const props = defineProps({
	order: Object,
})
</script>
<template>
    <div class="max-w-sm w-80 mx-auto py-8 font-mono">
        <table class="border-collapse w-full">
            <tbody>
                <tr v-if="app.logo_invoice">
                    <td class="align-middle text-xs font-normal" scope="col">
                        <img class="mx-auto h-24 w-auto uppercase-24" :src="app.logo_invoice" alt="company_logo" />
                    </td>
                </tr>
                <tr>
                    <td class="align-middle text-xs font-normal" scope="col">
                        <h1 class="w-full text-2xl font-semibold text-center">{{ setting.company_name }}</h1>
                    </td>
                </tr>
                <tr>
                    <td class="align-middle text-sm font-normal" scope="col">
                        <p class="mx-auto text-center tracking-wider">{{ branch.address ?? setting.company_address }}</p>
                    </td>
                </tr>
                <tr v-show="branch.phone">
                    <td class="align-middle text-sm font-normal" scope="col">
                        <p class="mx-auto uppercase tracking-wider text-center">Contact Number: {{ branch.phone }}</p>
                    </td>
                </tr>
                <tr v-show="setting.bin_number">
                    <td class="align-middle text-sm font-normal" scope="col">
                        <p class="mx-auto uppercase tracking-widest text-center">{{ setting.bin_number }}</p>
                    </td>
                </tr>
                <tr>
                    <th class="pt-4 align-middle text-sm font-bold uppercase tracking-wider text-center" colspan="2" scope="col">
                        ({{ props.order.status != 'complete' ? 'Bill' : 'Money Receipt' }})
                    </th>
                </tr>
                <tr v-show="props.order.order_count">
                    <th class="pt-4 align-middle text-sm font-bold uppercase tracking-wider text-center" colspan="2" scope="col">
                        Order Number: {{ props.order.order_count }}
                    </th>
                </tr>
                <!-- <tr v-show="props.order.order_count">
                    <td class="align-middle text-sm font-medium uppercase text-right" scope="col">Order Number: {{ props.order.order_count }}</td>
                </tr> -->
            </tbody>
        </table>

        <hr class="mt-2 mb-2 border-t-2 border-gray-500 border-dashed" />

        <table class="border-collapse w-full">
            <tbody>
                <tr>
                    <td class="align-middle text-sm font-medium uppercase tracking-wider text-left" scope="col">Date</td>
                    <td class="align-middle text-sm font-medium uppercase text-right" scope="col">{{ props.order.datetime_format }}</td>
                </tr>
                <tr>
                    <td class="w-1/3 align-middle text-sm font-medium uppercase tracking-wider text-left" scope="col">Invoice</td>
                    <td class="w-2/3 align-middle text-sm font-medium uppercase text-right" scope="col"># {{ props.order.invoice_number }}</td>
                </tr>
                <tr>
                    <td class="align-middle text-sm font-medium uppercase tracking-wider text-left" scope="col">Cashier</td>
                    <td class="align-middle text-sm font-medium text-right" scope="col">{{ props.order.creator_name }}</td>
                </tr>
                <tr>
                    <td class="align-middle text-sm font-medium uppercase tracking-wider text-left" scope="col">Waiter</td>
                    <td class="align-middle text-sm font-medium text-right" scope="col">{{ props.order.waiter_name }}</td>
                </tr>
                <tr>
                    <td class="align-middle text-sm font-medium uppercase tracking-wider text-left" scope="col">Table No</td>
                    <td class="align-middle text-sm font-medium text-right" scope="col">{{ props.order.table_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="align-middle text-sm font-medium uppercase tracking-wider text-left" scope="col">Payment</td>
                    <td class="align-middle text-sm font-medium uppercase text-right" scope="col">{{ props.order.payment_method_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="align-middle text-sm font-medium uppercase tracking-wider text-left" scope="col">Customer</td>
                    <td class="align-middle text-sm font-medium text-right" scope="col">{{ props.order.customer_name ?? 'N/A' }}</td>
                </tr>
                <tr v-show="props.order.delivery_address">
                    <td class="align-middle text-sm font-medium uppercase tracking-wider text-left" scope="col">Delivery</td>
                    <td class="align-middle text-sm font-medium text-right" scope="col">{{ props.order.delivery_address ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>

        <hr class="my-2 border-t-2 border-gray-500 border-dashed" />

        <table class="border-collapse w-full">
            <thead>
                <tr>
                    <th class="align-top pb-2 text-xs font-semibold uppercase text-left" scope="col">Description</th>
                    <th class="w-10 align-top pb-2 text-xs font-semibold uppercase text-center" scope="col">Qty</th>
                    <th class="w-16 align-top pb-2 text-xs font-semibold uppercase text-center" scope="col">Rate</th>
                    <th class="w-16 align-top pb-2 text-xs font-semibold uppercase text-right" scope="col">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in props.order.products" :key="index">
                    <td :class="['align-top font-normal uppercase text-left text-xs py-2']">{{ (index + 1)+ ". " + item.product_name }}</td>
                    <td :class="['align-top font-normal uppercase text-center text-xs py-2']">{{ item.quantity }}</td>
                    <td :class="['align-top font-normal uppercase text-right', 'text-xs py-2']">{{ item.rate }}</td>
                    <td :class="['align-top font-normal uppercase text-right', 'text-xs py-2']">{{ item.total }}</td>
                </tr>
            </tbody>
        </table>

        <hr class="my-2 border-t-2 border-gray-500 border-dashed" />

        <table class="border-collapse w-full">
            <tbody>
                <tr>
                    <td class="align-middle text-sm font-medium uppercase text-left" colspan="4">Sub Total</td>
                    <td class="align-middle text-sm font-medium uppercase text-right">{{ Number(props.order.sub_total).toFixed(2) }}</td>
                </tr>
                <tr>
                    <td class="align-middle text-sm font-medium uppercase text-left" colspan="4">Discount</td>
                    <td class="align-middle text-sm font-medium uppercase text-right">{{ Number(props.order.discount_amount).toFixed(2) }}</td>
                </tr>
                <tr v-if="props.order.service_cost > 0">
                    <td class="align-middle text-sm font-medium uppercase text-left" colspan="4">Service Cost</td>
                    <td class="align-middle text-sm font-medium uppercase text-right">{{ Number(props.order.service_cost).toFixed(2) }}</td>
                </tr>
                <tr>
                    <td class="align-middle text-sm font-medium uppercase text-left" colspan="4">VAT</td>
                    <td class="align-middle text-sm font-medium uppercase text-right">{{ Number(props.order.vat_amount).toFixed(2) }}</td>
                </tr>
                <tr>
                    <td class="align-middle text-lg font-bold uppercase text-left py-1" colspan="4">Total</td>
                    <td class="align-middle text-lg font-bold uppercase text-right">{{ Number(props.order.total).toFixed(2) }}</td>
                </tr>
                <tr v-if="props.order.status == 'complete'">
                    <td class="align-middle text-sm font-medium uppercase text-left" colspan="4">Cash</td>
                    <td class="align-middle text-sm font-medium uppercase text-right">{{ Number(props.order.cash).toFixed(2) }}</td>
                </tr>
                <tr v-if="props.order.status == 'complete'">
                    <td class="align-middle text-sm font-medium uppercase text-left" colspan="4">Change</td>
                    <td class="align-middle text-sm font-medium uppercase text-right">{{ Number(props.order.change).toFixed(2) }}</td>
                </tr>
            </tbody>
        </table>

        <hr v-show="props.order.note" class="my-2 border-t-2 border-gray-500 border-dashed" />

        <table v-show="props.order.note" class="border-collapse w-full">
            <tbody>
                <tr>
                    <td class="align-middle text-sm text-left"><span class="font-bold uppercase">Note:</span> {{ props.order.note }}</td>
                </tr>
            </tbody>
        </table>

        <hr class="mt-2 mb-6 border-t-2 border-gray-500 border-dashed" />

        <hr class="mt-10 border-t-2 border-gray-500 border-dashed" />
        <hr class="mt-0.5 mb-2 border-t-2 border-gray-500 border-dashed" />

        <table class="border-collapse mx-auto">
            <tbody>
                <tr>
                    <td class="align-middle text-sm text-center">{{ branch.pos_end_line }}</td>
                </tr>
            </tbody>
        </table>

        <hr class="mt-2 border-t-2 border-gray-500 border-dashed" />
        <hr class="mt-0.5 mb-6 border-t-2 border-gray-500 border-dashed" />
    </div>
</template>
