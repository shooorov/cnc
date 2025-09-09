<template>
    <Head title="Transfer Account" />

    <div>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>
                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                    </div>
                </div>
            </div>
        </div>

        <div class="py-5">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">Account Balance Transfer</p>

                        <div class="flex-shrink-0 flex space-x-3">
                        </div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="space-y-8 px-5 py-6">

                            <div class="max-w-5xl mx-auto space-y-4 sm:space-y-6">
                                <div class="md:px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8"> Type <span class="text-red-500">*</span> </dt>
                                    <dd class="flex-1 mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <label class="mb-5 inline-flex items-center">
                                            <input v-model="form.type" value="transfer" type="radio" class="form-radio w-5 h-5 border-gray-300 text-indigo-500 cursor-pointer focus:ring-indigo-500">
                                            <span class="ml-2 tracking-wide cursor-pointer"> Transfer Balance </span>
                                        </label><br>
                                        <label class="mb-5 inline-flex items-center">
                                            <input v-model="form.type" value="add" type="radio" class="form-radio w-5 h-5 border-gray-300 text-indigo-500 cursor-pointer focus:ring-indigo-500">
                                            <span class="ml-2 tracking-wide cursor-pointer"> Add Balance </span>
                                        </label><br>
                                        <label class="mb-5 inline-flex items-center">
                                            <input v-model="form.type" value="withdrawal" type="radio" class="form-radio w-5 h-5 border-gray-300 text-indigo-500 cursor-pointer focus:ring-indigo-500">
                                            <span class="ml-2 tracking-wide cursor-pointer"> Withdrawal Balance </span>
                                        </label><br>
                                        <InputError :message="$page.props.errors.type" />
                                    </dd>
                                </div>

                                <div class="md:px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8"> Date <span class="text-red-500">*</span> </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <input v-model="form.date" type="date" class="focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent block w-full px-4 sm:text-sm border-gray-300 rounded">
                                        <InputError :message="$page.props.errors.date" />
                                    </dd>
                                </div>

                                <div v-show="form.type != 'add'" class="md:px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8"> Form Account <span class="text-red-500">*</span> </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <Combobox class="mt-1" v-model="form.from_account_id" :items="accounts" />
                                        <InputError :message="$page.props.errors.from_account_id" />
                                    </dd>
                                </div>

                                <div class="md:px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8"> Amount <span class="text-red-500">*</span> </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <div class="relative">
                                            <input v-model="form.amount" placeholder="Amount" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="block w-full px-4 pr-16 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                            <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                        </div>
                                    </dd>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <input v-model="form.check_no" placeholder="Check No." type="text" class="focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent block w-full px-4 sm:text-sm border-gray-300 rounded">
                                        <InputError :message="$page.props.errors.check_no" />
                                    </dd>
                                </div>

                                <div v-show="form.type != 'withdrawal'" class="md:px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8"> To Account <span class="text-red-500">*</span> </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <Combobox class="mt-1" v-model="form.to_account_id" :items="accounts" />
                                        <InputError :message="$page.props.errors.to_account_id" />
                                    </dd>
                                </div>

                                <div class="md:px-0 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8"> Remark </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <textarea v-model="form.remark" type="text" placeholder="Remark Here" rows="4" class="focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent block w-full px-4 sm:text-sm border-gray-300 rounded"></textarea>
                                        <InputError :message="$page.props.errors.remark" />
                                    </dd>
                                </div>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-gray-300" /></div>
                                <div class="relative flex justify-center">
                                    <span class="px-3 bg-white text-lg font-medium text-gray-900"></span>
                                </div>
                            </div>

                            <div class="max-w-5xl mx-auto space-y-4 sm:space-y-6">
                                <div class="flex justify-end">
                                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
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
    </div>

</template>

<script>
import { reactive } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';

import Alert from '@/Components/Alert.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InputError from '@/Components/InputError.vue';
import Combobox from '@/Components/Combobox.vue';

import {
    PlusIcon,
} from '@heroicons/vue/24/solid';

import {
    PencilSquareIcon,
} from '@heroicons/vue/24/outline';

export default {
    layout: AuthenticatedLayout,

    components: {
    Alert,
    Breadcrumb,
    Combobox,
    Head,
    InputError,
    Link,

    PlusIcon,
    PencilSquareIcon,
},
    props: {

        accounts: Array,
    },

    setup (props) {
        const breadcrumbs = [
            { name: 'Transactions', href: route('transaction.index'), current: false },
            { name: 'Transfer Page', href: '#', current: false },
        ]

        const form = reactive({
            type: 'transfer',
            date: null,
            from_account_id: null,
            amount: null,
            check_no: null,
            to_account_id: null,
            remark: null,
        })

        function submit() {
            router.post(route('transaction.store'), form);
        }

        return {
            breadcrumbs,
            form,
            submit
        }
    }
}
</script>
