<template>
    <Head title="User" />

    <div>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>
                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                        <Link :href="route('user.create')" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
                            <PlusIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                            Create
                        </Link>
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
                                    <div class="shrink-0">
                                        <img class="h-12 w-12 rounded-full ring-4 ring-white" :src="user.image" alt="">
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                                            {{ user.name }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-4 mt-4 shrink-0 flex">
                                <a v-show="user.mobile && user.id != auth.user.id" :href="'tel:' + user.phone" class="relative inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    <PhoneIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                                    <span>Phone</span>
                                </a>

                                <a v-show="user.email && user.id != auth.user.id" :href="'mailto:' + user.email" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    <EnvelopeIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                                    <span>Email</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <Alert />

                    <div class="px-4 py-5 sm:px-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">
                                    Full name
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ user.name }}
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">
                                    Role name
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ user.role_name }}
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">
                                    Email address
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ user.email }}
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">
                                    Mobile
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ user.mobile}}
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">
                                    Address
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ user.address }}
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

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Alert from '@/Components/Alert.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import StatusOptions from '@/Components/StatusOptions.vue';

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
        StatusOptions,
        Head,
        Link,ChevronDownIcon,
        EnvelopeIcon,
        PencilSquareIcon,
        PhoneIcon,
        PlusIcon,
        TrashIcon,
    },
    props: {

        user: Object,
        auth: Object,
    },
    setup() {
        const breadcrumbs = [
            { name: 'Users', href: route('user.index'), current: false },
            { name: 'History Page', href: '#', current: false },
        ]

        return {
            breadcrumbs,
        }
    },
}
</script>
