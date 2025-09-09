<template>
    <Head title="Kitchen Log" />

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
                <div class="bg-white shadow sm:rounded-lg overflow-hidden">

                    <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
                        <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
                            <div class="ml-4 mt-4">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                                            {{ kitchen_log.category_name }} ({{ kitchen_log.type }})
                                        </h3>
                                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                            Kitchen Log details and information.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 shrink-0 flex">
                            </div>
                        </div>
                    </div>

                    <Alert />

                    <div class="border-t border-gray-200">
                        <dl>
                            <div class="bg-white md:px-8 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Amount</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ kitchen_log.amount }}
                                </dd>
                            </div>
                            <div class="bg-primary-50 md:px-8 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Account</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ kitchen_log.account_name }}
                                </dd>
                            </div>
                            <div class="bg-white md:px-8 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Category</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ kitchen_log.remark }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import { Head, Link } from '@inertiajs/vue3';

import Alert from '@/Components/Alert.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

import {
    ChevronDownIcon,
    EnvelopeIcon,
    PencilSquareIcon,
    PhoneIcon,
    PlusIcon,
    TrashIcon,
} from '@heroicons/vue/24/solid';

export default {
    layout: AuthenticatedLayout,

    components: {
        Alert,
        Breadcrumb,
        Head,
        Link,
        ChevronDownIcon,
        EnvelopeIcon,
        PencilSquareIcon,
        PhoneIcon,
        PlusIcon,
        TrashIcon,
    },
    props: {
        auth: Object,


        kitchen_log: Object,
    },
    setup() {
        const breadcrumbs = [
            { name: 'Kitchen Logs', href: route('kitchen.index'), current: false },
            { name: 'Detail Page', href: '#', current: false },
        ]

        return {
            breadcrumbs,
        }
    },
}
</script>
