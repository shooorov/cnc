<script setup>
import { reactive, ref } from 'vue';
import { router, Head, Link, useForm } from '@inertiajs/vue3';
import {
    PencilSquareIcon,
    EyeIcon,
    EyeSlashIcon,
} from '@heroicons/vue/24/solid';

import {
    UserIcon,
} from '@heroicons/vue/24/outline';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Alert from '@/Components/Alert.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

const props = defineProps({
	profile: Object,
})

const current_invisible = ref(true)
const new_invisible = ref(true)
const confirm_invisible = ref(true)

const breadcrumbs = [
	{ name: 'Account', href: route('profile.index'), current: false },
	{ name: 'Change Password', href: '#', current: true },
]

const form = useForm({
	current_password: null,
	new_password: null,
	new_password_confirmation: null,
})

function submit() {
	form.patch(route('profile.password.update'), {
		onFinish: () => {
			form.reset()
		}
	});
}
</script>
<template>
    <Head title="Password" />

    <AuthenticatedLayout>
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

        <div class="mt-5">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
                        <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
                            <div class="ml-4 mt-4">
                                <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">Change Password</p>
                            </div>
                            <div class="ml-4 mt-4 shrink-0 flex">
                            </div>
                        </div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="space-y-8 px-5 py-6">

                            <div class="max-w-5xl mx-auto space-y-4 sm:space-y-6">
                                <div class="grid sm:grid-cols-3 sm:gap-2">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8"> Current Password <span class="text-red-500">*</span> </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <div class="flex rounded shadow-sm">
                                            <input class="focus-within:z-10 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent block w-full px-4 sm:text-sm border-gray-300 rounded-l" :type="current_invisible ? 'password' : 'text'" placeholder="Current Password" v-model="form.current_password" label="Current Password" />
                                            <span class="inline-flex items-center justify-center px-4 rounded-r border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                                <EyeIcon v-show="!current_invisible" @click="current_invisible = !current_invisible" class="w-5 h-5 cursor-pointer hover:text-green-500" aria-hidden="true" />
                                                <EyeSlashIcon v-show="current_invisible" @click="current_invisible = !current_invisible" class="w-5 h-5 cursor-pointer hover:text-red-500" aria-hidden="true" />
                                            </span>
                                        </div>
                                    </dd>
                                </div>

                                <div class="grid sm:grid-cols-3 sm:gap-2">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8"> New Password <span class="text-red-500">*</span></dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <div class="flex rounded shadow-sm">
                                            <input class="focus-within:z-10 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent block w-full px-4 sm:text-sm border-gray-300 rounded-l" :type="new_invisible ? 'password' : 'text'" placeholder="New Password" v-model="form.new_password" label="New Password" />
                                            <span class="inline-flex items-center justify-center px-4 rounded-r border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                                <EyeIcon v-show="!new_invisible" @click="new_invisible = !new_invisible" class="w-5 h-5 cursor-pointer hover:text-green-500" aria-hidden="true" />
                                                <EyeSlashIcon v-show="new_invisible" @click="new_invisible = !new_invisible" class="w-5 h-5 cursor-pointer hover:text-red-500" aria-hidden="true" />
                                            </span>
                                        </div>
                                    </dd>
                                </div>

                                <div class="grid sm:grid-cols-3 sm:gap-2">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8"> Confirm Password <span class="text-red-500">*</span></dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <div class="flex rounded shadow-sm">
                                            <input class="focus-within:z-10 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent block w-full px-4 sm:text-sm border-gray-300 rounded-l" :type="confirm_invisible ? 'password' : 'text'" placeholder="Confirm Password" v-model="form.new_password_confirmation" label="Confirm Password" />
                                            <span class="inline-flex items-center justify-center px-4 rounded-r border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                                <EyeIcon v-show="!confirm_invisible" @click="confirm_invisible = !confirm_invisible" class="w-5 h-5 cursor-pointer hover:text-green-500" aria-hidden="true" />
                                                <EyeSlashIcon v-show="confirm_invisible" @click="confirm_invisible = !confirm_invisible" class="w-5 h-5 cursor-pointer hover:text-red-500" aria-hidden="true" />
                                            </span>
                                        </div>
                                    </dd>
                                </div>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"> <div class="w-full border-t border-gray-300" /> </div>
                                <div class="relative flex justify-center ml-4"> <span class="px-3 bg-white text-lg font-medium text-gray-900"></span> </div>
                            </div>

                            <div class="grid sm:grid-cols-3 sm:gap-2 sm:px-6">
                                <dt class="text-sm leading-5 font-semibold text-gray-500"></dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div class="mt-6 flex space-x-3 md:mt-0">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                            <PencilSquareIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                            Update
                                        </button>
                                    </div>
                                </dd>
                            </div>
                        </dl>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>