<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { computed, onMounted, reactive } from 'vue'

import Alert from '@/Components/Alert.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ArrowPathIcon, ArrowTopRightOnSquareIcon, ClipboardDocumentIcon, MagnifyingGlassIcon, PrinterIcon } from '@heroicons/vue/24/outline'
import { PlusIcon } from '@heroicons/vue/24/solid'

defineOptions({ layout: AuthenticatedLayout })

const props = defineProps({
    string_change: Object,
    navigation: Object,
    filter: Object,
    end_date: String,
    start_date: String,
    items_list: Array
})

const page = usePage()
const form = reactive({
    item_id: props.filter.item_id,
    end_date: props.filter.end_date,
    start_date: props.filter.start_date
})

// Branch Filter
const filterBranches = page.props.branches.map((branch) => ({
    id: branch.id,
    name: branch.name,
    is_checked: false
}))

const isAllBranchUnChecked = computed(() => filterBranches.filter((b) => b.is_checked).length === 0)

// Submit / Clear
const submit = () => {
    router.visit(route('item_inventory.compare'), { data: form })
}

const clearFilter = () => {
    for (const [key, value] of Object.entries(form)) {
        form[key] = ''
    }

    submit()
}

// Breadcrumbs
const breadcrumbs = [
    { name: props.string_change.item_inventory + ' In', href: route('item_inventory.in'), current: false },
    { name: 'Compare Page', href: '#', current: false }
]

// DataTable
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
</script>

<template>
    <Head title="Inventories History" />

    <div class="bg-white shadow">
        <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
            <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                <div class="flex-1 min-w-0">
                    <Breadcrumb :breadcrumbs="breadcrumbs" />
                </div>
                <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                    <Link
                        :href="route('item_inventory.create')"
                        class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
                        <PlusIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" /> In
                    </Link>
                </div>
            </div>
        </div>
    </div>

    <div class="py-5">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
                    <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">
                        {{ string_change.item_inventory }} Compare ({{ start_date }} - {{ end_date }})
                    </p>

                    <div class="flex-shrink-0 flex space-x-3">
                        <Link
                            v-if="navigation.routes.includes('item_inventory.activities')"
                            :href="route('item_inventory.activities')"
                            class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700">
                            <ClipboardDocumentIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" /> Activities
                        </Link>

                        <Link
                            v-if="navigation.routes.includes('item_inventory.compare')"
                            :href="route('item_inventory.compare')"
                            class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700">
                            <ClipboardDocumentIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" /> Compare
                        </Link>

                        <a
                            v-if="navigation.routes.includes('report.item_inventory')"
                            :href="route('report.item_inventory', { item_id: form.item_id, start_date: form.start_date, end_date: form.end_date })"
                            target="_blank"
                            class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700">
                            <PrinterIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" /> Print
                        </a>
                    </div>
                </div>

                <!-- Branch Filter -->
                <dl class="px-5 py-5 mx-auto max-w-5xl">
                    <div class="grid grid-cols-5 gap-4">
                        <div v-for="branch in filterBranches" :key="branch.id">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="branch.is_checked" class="form-checkbox" />
                                <span class="ml-2">{{ branch.name }}</span>
                            </label>
                        </div>
                    </div>
                </dl>

                <form @submit.prevent="submit">
                    <dl class="px-5 py-5 mx-auto max-w-5xl">
                        <div class="py-2 sm:grid sm:grid-cols-6 sm:gap-4">
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input v-model="form.start_date" type="date" class="mt-1 block w-full px-4 border-gray-300 rounded" />
                            </dd>

                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">End Date</label>
                                <input v-model="form.end_date" type="date" class="mt-1 block w-full px-4 border-gray-300 rounded" />
                            </dd>

                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Action</label>
                                <div class="inline-flex mt-1 rounded" role="group">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-l shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                                        <MagnifyingGlassIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" /> Search
                                    </button>
                                    <button
                                        @click="clearFilter"
                                        type="button"
                                        class="inline-flex items-center px-4 py-1 border border-primary-600 rounded-r shadow-sm text-sm font-medium text-primary-700 bg-white hover:bg-primary-50">
                                        <ArrowPathIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" /> Clear
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
                            <th rowspan="2" class="w-12 p-2 bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">S.N.</th>
                            <th rowspan="2" class="w-32 p-2 bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Item</th>

                            <template v-for="branch in filterBranches" :key="branch.id">
                                <th
                                    colspan="3"
                                    class="p-2 border bg-gray-100 text-xs font-bold uppercase tracking-wider text-center"
                                    v-show="branch.is_checked || isAllBranchUnChecked">
                                    {{ branch.name }}
                                </th>
                            </template>

                            <th rowspan="2" class="w-20 p-2 bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">Action</th>
                        </tr>
                        <tr>
                            <template v-for="branch in filterBranches" :key="branch.id">
                                <th
                                    v-show="branch.is_checked || isAllBranchUnChecked"
                                    class="p-2 border-l bg-gray-100 text-xs whitespace-wrap font-bold uppercase tracking-wider text-center">
                                    Cost (tk)
                                </th>
                                <th
                                    v-show="branch.is_checked || isAllBranchUnChecked"
                                    class="p-2 bg-gray-100 text-xs whitespace-wrap font-bold uppercase tracking-wider text-center">
                                    Avg
                                </th>
                                <th
                                    v-show="branch.is_checked || isAllBranchUnChecked"
                                    class="p-2 border-r bg-gray-100 text-xs whitespace-wrap font-bold uppercase tracking-wider text-center">
                                    Total Purchase
                                </th>
                            </template>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        <tr v-for="(item, index) in items_list" :key="index" :class="[index % 2 === 0 ? 'bg-white' : 'bg-gray-50', 'border-b']">
                            <td class="p-2 text-center">{{ index + 1 }}</td>
                            <td class="p-2 whitespace-wrap">
                                <div class="text-sm leading-5 text-gray-700 capitalize">{{ item.name }}</div>
                            </td>

                            <template v-for="branch in filterBranches" :key="branch.id">
                                <td v-show="branch.is_checked || isAllBranchUnChecked" class="p-2 whitespace-wrap border-l text-center">
                                    {{ item.branches[branch.id]?.rates ?? 'N/A' }}
                                </td>
                                <td v-show="branch.is_checked || isAllBranchUnChecked" class="p-2 whitespace-wrap text-center">
                                    {{ item.branches[branch.id]?.avg_rate ?? 'N/A' }}
                                </td>
                                <td v-show="branch.is_checked || isAllBranchUnChecked" class="p-2 whitespace-nowrap border-r text-center">
                                    {{ item.branches[branch.id]?.total ?? 'N/A' }}
                                </td>
                            </template>

                            <td class="px-3 py-1 whitespace-wrap break-words">
                                <div class="flex justify-center items-center">
                                    <Link
                                        :href="route('item_inventory.activities', { item_id: item.id, start_date: form.start_date, end_date: form.end_date })"
                                        class="text-primary-600 hover:text-primary-800"
                                        title="Activities">
                                        <ArrowTopRightOnSquareIcon class="w-6 h-6" aria-hidden="true" />
                                    </Link>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
