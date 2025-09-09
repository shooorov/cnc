<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import Alert from '@/Components/Alert.vue'
import InputError from '@/Components/InputError.vue'
import { PencilSquareIcon, PlusIcon, ArrowPathIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import StatusOptions from '@/Components/StatusOptions.vue'

const page = usePage()

const props = defineProps({
	statuses: Object,
    selected_branch: Object,
})

const breadcrumbs = [
    { name: 'Branches', href: route('branch.index'), current: false },
    { name: 'Edit Page', href: '#', current: false }
]

const form = useForm({
    name: props.selected_branch.name,
    short_code: props.selected_branch.short_code,
    contact_number: props.selected_branch.phone,
    address: props.selected_branch.address,
    vat_rate: props.selected_branch.vat_rate,
    delivery_cost: props.selected_branch.delivery_cost,
    opening_hours: props.selected_branch.opening_hours,
    barista_target: props.selected_branch.barista_target,
    chef_target: props.selected_branch.chef_target,
    pos_end_line: props.selected_branch.pos_end_line,
    sale_target: props.selected_branch.sale_target,


})

function submit() {
	page.props.alertMessage = {};

    form.post(route('branch.update', props.selected_branch.id), {})
}
</script>
<template>
    <Head title="Edit Branch" />

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
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">Branch Edit</p>

                        <div class="flex-shrink-0 flex space-x-3">
							<StatusOptions class="ml-3" as="button" :status="selected_branch.status" :items="statuses"
									:href="route('branch.status.update', selected_branch.id)" />
						</div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="space-y-4 sm:space-y-6 px-5 py-6">
                            <div class="max-w-xl mx-auto">
                                <div class="grid grid-cols-4 gap-4">
                                    <div class="col-span-4 sm:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700"> Name <span class="text-red-500">*</span> </label>
                                        <input
                                            v-model="form.name"
                                            placeholder="Name"
                                            type="text"
                                            class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                        <InputError :message="$page.props.errors.name" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Short Code <span class="text-red-500">*</span> </label>
                                        <input
                                            v-model="form.short_code"
                                            placeholder="Short Code"
                                            type="text"
                                            class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                        <InputError :message="$page.props.errors.short_code" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Contact Number </label>
                                        <input
                                            v-model="form.contact_number"
                                            placeholder="Contact Number"
                                            type="text"
                                            class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                        <InputError :message="$page.props.errors.contact_number" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700"> Address </label>
                                        <textarea
                                            v-model="form.address"
                                            placeholder="Address"
                                            rows="3"
                                            class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded"></textarea>
                                        <InputError :message="$page.props.errors.address" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Opening Hours </label>
                                        <input
                                            v-model="form.opening_hours"
                                            placeholder="Opening Hours"
                                            type="text"
                                            class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                        <InputError :message="$page.props.errors.opening_hours" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> VAT </label>
                                        <div class="relative">
                                            <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm"
                                                >%</span
                                            >
                                            <input
                                                v-model="form.vat_rate"
                                                placeholder="VAT"
                                                type="number"
                                                step=".01"
                                                class="mt-1 block w-full px-4 pr-12 focus:outline-none focus:ring-indigo-400 focus:border-indigo-400 focus:bg-transparent hover:bg-gray-100 sm:text-sm border-gray-300 rounded" />
                                        </div>
                                        <InputError :message="$page.props.errors.vat_rate" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Service Cost </label>
                                        <input
                                            v-model="form.service_cost"
                                            placeholder="Service Cost"
                                            type="text"
                                            class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                        <InputError :message="$page.props.errors.service_cost" />
                                    </div>

                                    <div v-if="$page.props.module.delivery" class="col-span-4 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Delivery Cost </label>
                                        <input
                                            v-model="form.delivery_cost"
                                            placeholder="Delivery Cost"
                                            type="text"
                                            class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                        <InputError :message="$page.props.errors.delivery_cost" />
                                    </div>

                                    <!-- <div v-if="$page.props.module.production" class="col-span-4 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Barista Production Target </label>
                                        <input
                                            v-model="form.barista_target"
                                            placeholder="Barista Target"
                                            type="text"
                                            class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                        <InputError :message="$page.props.errors.barista_target" />
                                    </div>

                                    <div v-if="$page.props.module.production" class="col-span-4 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Chef Production Target </label>
                                        <input
                                            v-model="form.chef_target"
                                            placeholder="Chef Target"
                                            type="text"
                                            class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                        <InputError :message="$page.props.errors.chef_target" />
                                    </div> -->

                                    <div class="col-span-4 sm:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700"> POS End Line </label>
                                        <textarea
                                            v-model="form.pos_end_line"
                                            placeholder="POS End Line"
                                            rows="3"
                                            class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded"></textarea>
                                        <InputError :message="$page.props.errors.pos_end_line" />
                                    </div>

                                    
                                <div class="col-span-4 sm:col-span-4">
                                    <label class="block text-sm font-medium text-gray-700">This Month Sale Target</label>
                                    <input 
                                        v-model="form.sale_target"
                                        placeholder="This Month Sale Targets"
                                        type="number"
                                        class="mt-1 block w-full px-4 py-2 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded"
                                        style="height: 60px;" 
                                    />
                                    <InputError :message="$page.props.errors.sale_target" />
                                </div>
                                </div>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-gray-300" /></div>
                                <div class="relative flex justify-center ml-4"><span class="px-3 bg-white text-lg font-medium text-gray-900"></span></div>
                            </div>

                            <div class="max-w-xl mx-auto">
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
