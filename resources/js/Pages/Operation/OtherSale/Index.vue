<script setup>
import { reactive, onMounted } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'

import Alert from '@/Components/Alert.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'

import Status from '@/Components/Status.vue'
import Combobox from '@/Components/Combobox.vue'

import { PlusIcon } from '@heroicons/vue/24/solid'

import { ArrowPathIcon, MagnifyingGlassIcon, ArrowTopRightOnSquareIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    filter: Object,
    accounts: Array,
    categories: Array,
    other_sales: Array
})

onMounted(() => {
    $('#table').DataTable({
        lengthMenu: [
            [10, 25, 50, 100, 200],
            [10, 25, 50, 100, 200]
        ],
        length: 10,
        dom: "<'flex justify-center sm:justify-end mb-3'B><'flex flex-col sm:flex-row justify-between'lf><'block overflow-auto'rt><'flex flex-col sm:flex-row justify-between'ip>",
        buttons: ['copy', 'excel']
    })
})

const form = reactive({
    account_id: props.filter.account_id,
    branch_id: props.filter.branch_id,
    other_sale_category_id: props.filter.other_sale_category_id,
    end_date: props.filter.end_date,
    start_date: props.filter.start_date
})

function clearFilter() {
    for (const [key, value] of Object.entries(form)) {
        form[key] = ''
    }
}

function submit() {
    router.visit(route('other_sale.index'), {
        data: form
    })
}

const deleteRecord = (record_id) => {
    if (confirm('Do you really want to delete this other_sale?')) {
        router.delete(route('other_sale.destroy', record_id))
    }
}

const breadcrumbs = [
    { name: 'Other Sales', href: route('other_sale.index'), current: false },
    { name: 'List Page', href: '#', current: false }
]
</script>
<template>
    <Head title="Other Sales" />

    <AuthenticatedLayout>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>
                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                        <Link
                            :href="route('other_sale.create')"
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
						<div class="flex-1">
							<h3 class="text-base font-semibold leading-6 text-gray-900">Other Sales</h3>
							<p class="mt-1 text-sm text-gray-500">
								{{ (props.filter.start_date ? props.filter.start_date.split("-").reverse().join("/") : '') + " - " +
									(props.filter.end_date ? props.filter.end_date.split("-").reverse().join("/") : '') }}
							</p>
						</div>
						<div class="flex-shrink-0 flex space-x-3">
						</div>
					</div>

                    <form @submit.prevent="submit">
                        <dl class="px-5 py-5 mx-auto max-w-5xl">
                            <div class="py-2 sm:grid sm:grid-cols-8 sm:gap-4">
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Category</label>
                                    <Combobox class="mt-1" v-model="form.other_sale_category_id" :items="categories" />
                                </dd>

                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Account</label>
                                    <Combobox class="mt-1" v-model="form.account_id" :items="accounts" />
                                </dd>

                                <dd v-if="form.start_date" class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                    <input
                                        v-model="form.start_date"
                                        type="date"
                                        class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                </dd>

                                <dd v-if="form.end_date" class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">End Date</label>
                                    <input
                                        v-model="form.end_date"
                                        type="date"
                                        class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                </dd>

                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Action</label>
                                    <div class="inline-flex mt-1 rounded" role="group">
                                        <button
                                            type="submit"
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-l shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                            <MagnifyingGlassIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                            Search
                                        </button>

                                        <button
                                            @click="clearFilter"
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
                                <th class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">S.N.</th>
                                <th class="w-40 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Date</th>
                                <th class="w-40 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Category</th>
                                <th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Account</th>
                                <th class="w-24 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Amount</th>
                                <th class="w-24 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr
                                v-for="(other_sale, index) in other_sales"
                                :key="other_sale.id"
                                :class="[index % 2 == 0 ? 'bg-white' : 'bg-gray-50', 'border-b']">
                                <td class="p-2 text-center">{{ index + 1 }}</td>

                                <td class="p-2 whitespace-wrap">
                                    {{ other_sale.datetime_format }}
                                </td>

                                <td class="p-2 whitespace-wrap">
                                    {{ categories.find((i) => i.id == other_sale.other_sale_category_id)?.name }}
                                </td>

                                <td class="p-2 whitespace-wrap">
                                    {{ accounts.find((i) => i.id == other_sale.account_id)?.name }}
                                </td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
                                    {{ other_sale.amount }}
                                </td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
                                    <div class="flex justify-end">
                                        <Link :href="route('other_sale.edit', other_sale.id)" class="text-indigo-600 hover:text-indigo-800 ml-3" title="edit">
                                            <PencilSquareIcon class="w-6 h-6" aria-hidden="true" />
                                        </Link>
                                        <button @click="deleteRecord(other_sale.id)" class="text-red-600 hover:text-red-800 ml-3" title="delete">
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
