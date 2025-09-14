<template>
    <Head>
        <title>Edit {{ string_change.product }} Requisition</title>
    </Head>

    <div>
        <!-- Header -->
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>
                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                        <Link
                            :href="route('product_requisition.create')"
                            class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
                            <PlusIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                            Create
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="py-5">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">{{ string_change.product }} Requisition Edit</p>
                        <div class="flex-shrink-0 flex space-x-3">
                            <button
                                @click="submit"
                                class="mr-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <PencilSquareIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                Update
                            </button>
                        </div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="space-y-4 sm:space-y-6 px-5 py-6">
                            <!-- Requisition Info -->
                            <div class="max-w-xl mx-auto grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700"> Requisition Date <span class="text-red-500">*</span> </label>
                                    <input
                                        v-model="form.requisition_date"
                                        ref="requisitionDateRef"
                                        type="date"
                                        class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                    <InputError :message="form.errors?.requisition_date" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700"> Total Around <span class="text-red-500">*</span> </label>
                                    <input
                                        :value="form.total_format"
                                        readonly
                                        type="text"
                                        placeholder="Total"
                                        class="mt-1 block w-full px-4 focus:ring-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded" />
                                    <InputError :message="form.errors?.total" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700"> Central Kitchen <span class="text-red-500">*</span> </label>
                                    <Combobox class="mt-1" v-model="form.central_kitchen_id" :items="central_kitchens" />
                                    <InputError :message="form.errors?.central_kitchen_id" />
                                </div>
                            </div>

                            <!-- Products Table -->
                            <div class="relative max-w-5xl mx-auto space-y-4 sm:space-y-6">
                                <table class="table-auto sm:table-fixed min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ string_change.product }}
                                            </th>
                                            <th scope="col" class="w-1/4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                            <th scope="col" class="w-40 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                                            <th scope="col" class="w-40 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(group_item, index) in form.group_items" :key="index">
                                            <td>
                                                <input
                                                    v-model="group_item.product_name"
                                                    readonly
                                                    type="text"
                                                    autocomplete="off"
                                                    class="block w-full px-4 focus:ring-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded" />
                                            </td>

                                            <td>
                                                <input
                                                    v-model="group_item.quantity"
                                                    @keyup="calculation(index)"
                                                    placeholder="Quantity"
                                                    type="text"
                                                    :ref="(el) => (quantityRefs[index] = el)"
                                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                    class="block w-full px-4 pr-24 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                                <InputError :message="form.errors?.group_items?.[index]?.quantity" />
                                            </td>

                                            <td>
                                                <input
                                                    v-model="group_item.rate"
                                                    placeholder="Rate"
                                                    type="text"
                                                    :required="group_item.quantity > 0"
                                                    @keyup="calculation(index)"
                                                    :ref="(el) => (rateRefs[index] = el)"
                                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                    class="block w-full px-4 pr-24 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                                <InputError :message="form.errors?.group_items?.[index]?.rate" />
                                            </td>

                                            <td>
                                                <input
                                                    v-model="group_item.total"
                                                    placeholder="Total"
                                                    readonly
                                                    type="text"
                                                    autocomplete="off"
                                                    class="block w-full px-4 focus:ring-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded" />
                                            </td>
                                        </tr>
                                    </tbody>

                                    <tfoot class="bg-white">
                                        <tr>
                                            <td colspan="3" class="py-2 px-2 text-right text-base">Total Around</td>
                                            <th colspan="1" class="py-2 px-2 text-left text-lg font-medium font-mono">
                                                {{ form.total_format }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Submit -->
                            <div class="max-w-5xl mx-auto space-y-4 sm:space-y-6">
                                <div class="flex justify-start">
                                    <button
                                        type="submit"
                                        class="mr-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
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
    </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { nextTick, reactive, ref } from 'vue'

import Alert from '@/Components/Alert.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import Combobox from '@/Components/Combobox.vue'
import InputError from '@/Components/InputError.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import { PencilSquareIcon, PlusIcon } from '@heroicons/vue/24/solid'

defineOptions({ layout: AuthenticatedLayout })

const props = defineProps({
    string_change: Object,
    requisition: Object,
    central_kitchens: Array
})

// Breadcrumbs
const breadcrumbs = [
    { name: props.string_change.product + ' Requisitions', href: route('product_requisition.index'), current: false },
    { name: 'Edit Page', href: '#', current: false }
]

// Refs for validation focus
const requisitionDateRef = ref(null)
const rateRefs = ref([])
const quantityRefs = ref([])

// Reactive form
const form = reactive({
    requisition_date: props.requisition.date,
    central_kitchen_id: props.requisition.central_kitchen_id,
    total: props.requisition.total,
    total_format: props.requisition.products.reduce((carry, val) => carry + Number(val.total), 0).toLocaleString('en-US'),
    group_items: props.requisition.products.map((item) => ({ ...item, rate: item.rate || '' })),
    errors: {}
})

// Calculation
function calculation(index) {
    const item = form.group_items[index]
    item.total = Number(((item.rate || 0) * (item.quantity || 0)).toFixed(3))
    form.total = form.group_items.reduce((carry, val) => carry + Number(val.total || 0), 0)
    form.total_format = form.total.toLocaleString('en-US')
}

// Submit with validation
async function submit() {
    form.errors = {}

    if (!form.requisition_date) {
        form.errors.requisition_date = 'Requisition date is required'
        await nextTick()
        requisitionDateRef.value?.focus()
        return
    }

    for (let i = 0; i < form.group_items.length; i++) {
        const item = form.group_items[i]
        if (item.quantity && !item.rate) {
            form.errors.group_items = form.errors.group_items || []
            form.errors.group_items[i] = { rate: 'Rate is required when quantity is entered' }
            await nextTick()
            rateRefs.value[i]?.focus()
            return
        }
    }

    router.patch(route('product_requisition.update', props.requisition.id), form)
}
</script>
``
