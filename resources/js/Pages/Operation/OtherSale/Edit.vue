<script setup>
import { reactive, ref } from 'vue'
import { router, Head, Link, useForm, usePage } from '@inertiajs/vue3'

import Alert from '@/Components/Alert.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'

import InputError from '@/Components/InputError.vue'
import Combobox from '@/Components/Combobox.vue'

import { PlusIcon } from '@heroicons/vue/24/solid'

import { PencilSquareIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    other_sale: Object,
    other_sale_categories: Array,
    accounts: Array
})

const page = usePage()

const form = useForm({
    date: props.other_sale.date,
    amount: props.other_sale.amount,
    check_no: props.other_sale.check_no,
    account_id: props.other_sale.account_id,
    branch_id: props.other_sale.branch_id,
    other_sale_category_id: props.other_sale.other_sale_category_id,
    description: props.other_sale.description
})

function submit() {
    router.patch(route('other_sale.update', props.other_sale.id), form)
}

const breadcrumbs = [
    { name: 'Other Sales', href: route('other_sale.index'), current: false },
    { name: 'Edit Page', href: '#', current: false }
]

</script>
<template>
    <Head title="Edit Other Sale" />

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
                    <div class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">Other Sale Edit</p>

                        <div class="flex-shrink-0 flex space-x-3">
                        </div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="space-y-4 sm:space-y-6 px-5 py-6">
                            <div class="max-w-5xl mx-auto space-y-4 sm:space-y-6">
                                <div class="md:px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm leading-10 font-semibold text-gray-700 tracking-wider text-right pr-8">
                                        Date <span class="text-red-500">*</span>
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <input
                                            v-model="form.date"
                                            type="datetime-local"
                                            class="focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent block w-full px-4 sm:text-sm border-gray-300 rounded" />
                                        <InputError :message="$page.props.errors.date" />
                                    </dd>
                                </div>

                                <div class="md:px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm leading-10 font-semibold text-gray-700 tracking-wider text-right pr-8">
                                        Category <span class="text-red-500">*</span>
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <Combobox class="mt-1" v-model="form.other_sale_category_id" :items="other_sale_categories" />
                                        <InputError :message="$page.props.errors.other_sale_category_id" />
                                    </dd>
                                </div>

                                <div class="md:px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm leading-10 font-semibold text-gray-700 tracking-wider text-right pr-8">
                                        Account <span class="text-red-500">*</span>
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <Combobox class="mt-1" v-model="form.account_id" :items="accounts" />
                                        <InputError :message="$page.props.errors.account_id" />
                                    </dd>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <input
                                            v-model="form.check_no"
                                            placeholder="Check No."
                                            type="text"
                                            class="mt-1 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent block w-full px-4 sm:text-sm border-gray-300 rounded" />
                                        <InputError :message="$page.props.errors.check_no" />
                                    </dd>
                                </div>

                                <div class="md:px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm leading-10 font-semibold text-gray-700 tracking-wider text-right pr-8">
                                        Amount <span class="text-red-500">*</span>
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <div class="relative">
                                            <input
                                                v-model="form.amount"
                                                placeholder="Amount"
                                                type="text"
                                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                class="block w-full px-4 pr-16 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                            <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm"
                                                >&#2547;</span
                                            >
                                        </div>
                                        <InputError :message="$page.props.errors.amount" />
                                    </dd>
                                </div>

                                <div class="md:px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8">Description</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <textarea
                                            v-model="form.description"
                                            type="text"
                                            placeholder="Description Here"
                                            rows="4"
                                            class="focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent block w-full px-4 sm:text-sm border-gray-300 rounded"></textarea>
                                        <InputError :message="$page.props.errors.description" />
                                    </dd>
                                </div>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-gray-300" /></div>
                                <div class="relative flex justify-center ml-4"><span class="px-3 bg-white text-lg font-medium text-gray-900"></span></div>
                            </div>

                            <div class="max-w-5xl mx-auto space-y-4 sm:space-y-6">
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
