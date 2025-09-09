<script setup>
import { ref, reactive, computed } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import { PlusIcon, PencilSquareIcon } from '@heroicons/vue/24/solid'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import Alert from '@/Components/Alert.vue'

const props = defineProps({
    categories: Array,
    page_branches: Array
})

const filterBranches = ref(
    props.page_branches.map((i) => {
        return {
            id: i.id,
            name: i.name
        }
    })
)

const isAllBranchUnChecked = computed(() => {
    return filterBranches.value.filter((i) => i.is_checked).length == 0
})

const form = reactive({
    categories: props.categories
})

function submit() {
    router.post(route('branch.token_update'), form)
}

const breadcrumbs = [
    { name: 'Branches', href: route('branch.index'), current: false },
    { name: 'Token Page', href: '#', current: false }
]
</script>
<template>
    <Head title="Branch Token" />

    <AuthenticatedLayout>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>
                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                        <Link
                            :href="route('branch.create')"
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
                    <div class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">Branch Token Amount</p>

                        <div class="flex-shrink-0 flex space-x-3">
                            <button
                                type="submit"
                                @click="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                <PencilSquareIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                Update
                            </button>
                        </div>
                    </div>

                    <Alert />

                    <dl class="px-5 py-5 mx-auto max-w-5xl space-y-4">
                        <div class="grid sm:grid-cols-5 sm:gap-2">
                            <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8">Branch</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-4 grid sm:grid-cols-4 sm:gap-2">
                                <div v-for="branch in filterBranches" :key="branch.name" class="mt-3 space-y-4">
                                    <label class="inline-flex items-center mr-5">
                                        <input
                                            v-model="branch.is_checked"
                                            type="checkbox"
                                            class="form-checkbox w-5 h-5 border-gray-300 text-blue-500 cursor-pointer focus:ring-blue-500 rounded" />
                                        <span class="ml-2 tracking-wide cursor-pointer"> {{ branch.name }} </span>
                                    </label>
                                </div>
                            </dd>
                        </div>
                    </dl>

                    <form @submit.prevent="submit">
                        <dl class="space-y-8 py-6 px-6">
                            <table class="table-auto sm:table-fixed min-w-full w-full">
                                <thead>
                                    <tr>
                                        <th class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">S.N.</th>
                                        <th class="w-40 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Name</th>
                                        <th
                                            v-for="item in filterBranches"
                                            :key="item.id"
                                            v-show="item.is_checked || isAllBranchUnChecked"
                                            class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">
                                            {{ item.name }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <tr
                                        v-for="(category, index) in form.categories"
                                        :key="category.id"
                                        :class="[index % 2 == 0 ? 'bg-white' : 'bg-gray-50', 'border-b']">
                                        <td class="px-3 py-1 whitespace-wrap break-words">
                                            <div class="text-sm leading-5 text-gray-700 text-center">{{ parseInt(index) + 1 }}</div>
                                        </td>

                                        <td class="px-3 py-1 whitespace-wrap break-words">
                                            <div class="text-sm leading-5 text-gray-700 capitalize">{{ category.name }}</div>

                                            <!-- <Link :href="route('product_category.edit', category.id)" class="text-sm leading-5 text-blue-600 hover:underline">
                                                {{ category.name }}
                                            </Link> -->
                                        </td>

                                        <td
                                            v-for="item in filterBranches"
                                            :key="item.id"
                                            v-show="item.is_checked || isAllBranchUnChecked"
                                            class="p-2 whitespace-nowrap text-center">
                                            <input
                                                type="text"
                                                :id="`branch_${item.id}_${category.id}`"
                                                v-model="category.token_amount[item.id]"
                                                :placeholder="item.name"
                                                class="mt-1 block w-full px-4 py-2 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-transparent rounded" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-gray-300" /></div>
                                <div class="relative flex justify-center ml-4"><span class="px-3 bg-white text-lg font-medium text-gray-900"></span></div>
                            </div>

                            <div class="mx-auto">
                                <div class="flex justify-end">
                                    <button
                                        type="submit"
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
