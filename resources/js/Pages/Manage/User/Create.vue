<script setup>
import { reactive, ref, watch } from 'vue'
import { router, Head, Link, useForm, usePage } from '@inertiajs/vue3'

import Alert from '@/Components/Alert.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import Listbox from '@/Components/Listbox.vue'

import { PlusIcon, EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/solid'

import { PencilSquareIcon } from '@heroicons/vue/24/outline'
import InputError from '@/Components/InputError.vue'

const page = usePage()

const props = defineProps({
    user: Object,
    roles: Array
})

const form = useForm({
    type: null,
    role_id: null,
    name: null,
    email: null,
    password: null,
    mobile: null,
    address: null,
    branches: page.props.branches
})

watch(
    () => form.type,
    (newVal, oldVal) => {
        if (newVal != 'other') {
            this.form.role_id = null
        }
    }
)
const password_invisible = ref(true)

function submit() {
	page.props.alertMessage = {};

    form.post(route('user.store'), {
        onFinish: () => {}
    })
}
const breadcrumbs = [
    { name: 'Users', href: route('user.index'), current: false },
    { name: 'Create Page', href: '#', current: false }
]
</script>
<template>
    <Head title="Create User" />

    <AuthenticatedLayout>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>
                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4"></div>
                </div>
            </div>
        </div>

        <div class="py-5">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">User Create</p>

                        <div class="flex-shrink-0 flex space-x-3"></div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="space-y-4 sm:space-y-6 px-5 py-6">
                            <div class="max-w-5xl mx-auto space-y-4 sm:space-y-6">
                                <div class="grid sm:grid-cols-3 sm:gap-2">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8">
                                        Name <span class="text-red-500">*</span>
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <input
                                            v-model="form.name"
                                            placeholder="Name"
                                            type="text"
                                            class="block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                    </dd>
                                </div>

                                <div class="grid sm:grid-cols-3 sm:gap-2">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8">
                                        Email <span class="text-red-500">*</span>
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <input
                                            v-model="form.email"
                                            placeholder="Email"
                                            type="email"
                                            autocomplete="email"
                                            class="block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                    </dd>
                                </div>

                                <div class="grid sm:grid-cols-3 sm:gap-2">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8">
                                        Password <span class="text-red-500">*</span>
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <div class="flex rounded shadow-sm">
                                            <input
                                                class="focus-within:z-10 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent block w-full px-4 sm:text-sm border-gray-300 rounded-l"
                                                :type="password_invisible ? 'password' : 'text'"
                                                placeholder="Password"
                                                v-model="form.password"
                                                label="Password"
                                                autocomplete="new-password" />
                                            <button type="button"
                                                class="inline-flex items-center justify-center px-4 rounded-r border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                                <EyeIcon
                                                    v-show="!password_invisible"
                                                    @click="password_invisible = !password_invisible"
                                                    class="w-5 h-5 cursor-pointer hover:text-green-500"
                                                    aria-hidden="true" />
                                                <EyeSlashIcon
                                                    v-show="password_invisible"
                                                    @click="password_invisible = !password_invisible"
                                                    class="w-5 h-5 cursor-pointer hover:text-red-500"
                                                    aria-hidden="true" />
                                            </button>
                                        </div>
                                    </dd>
                                </div>

                                <div class="grid sm:grid-cols-3 sm:gap-2">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8">Mobile</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <div class="relative">
                                            <span class="absolute py-2 top-0 left-0 m-px px-4 rounded-l-md border-r border-gray-300 bg-gray-100 sm:text-sm"
                                                >+88</span
                                            >
                                            <input
                                                v-model="form.mobile"
                                                placeholder="Mobile"
                                                type="text"
                                                class="block w-full px-4 pl-16 focus:outline-none focus:ring-primary-400 focus:border-primary-400 focus:bg-transparent hover:bg-gray-100 sm:text-sm border-gray-300 rounded" />
                                        </div>
                                    </dd>
                                </div>

                                <!-- <div class="grid sm:grid-cols-3 sm:gap-2">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8"> Address </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <textarea v-model="form.address" placeholder="Address Here" rows="4" type="text" class="block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded"></textarea>
                                        <InputError :message="$page.props.errors.address" />
                                    </dd>
                                </div> -->

                                <div class="grid sm:grid-cols-3 sm:gap-2">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8">
                                        User Type <span class="text-red-500">*</span>
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <div class="mt-3 space-y-4">
                                            <label v-for="(name, key) in user.types" :key="key" class="flex items-center mr-5">
                                                <input
                                                    v-model="form.type"
                                                    :value="key"
                                                    type="radio"
                                                    class="form-radio w-5 h-5 border-gray-300 text-indigo-500 cursor-pointer focus:ring-indigo-500" />
                                                <span class="ml-2 tracking-wide cursor-pointer"> {{ name }} </span>
                                            </label>
                                            <label class="flex items-center mr-5">
                                                <input
                                                    v-model="form.type"
                                                    value="other"
                                                    type="radio"
                                                    class="form-radio w-5 h-5 border-gray-300 text-indigo-500 cursor-pointer focus:ring-indigo-500" />
                                                <span class="ml-2 tracking-wide cursor-pointer"> Other (Custom role based) </span>
                                            </label>
                                        </div>
                                    </dd>
                                </div>

                                <div v-if="form.type == 'other'" class="grid sm:grid-cols-3 sm:gap-2">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8">
                                        Role <span class="text-red-500">*</span>
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <Listbox class="mt-1" v-model="form.role_id" :items="roles" />
                                    </dd>
                                </div>

                                <div class="grid sm:grid-cols-3 sm:gap-2">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8">
                                        Branch Access <span class="text-red-500">*</span>
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <div class="mt-3 space-y-4">
                                            <label v-for="branch in form.branches" :key="branch.id" class="flex items-center mr-5">
                                                <input
                                                    v-model="branch.is_checked"
                                                    type="checkbox"
                                                    class="form-checkbox w-5 h-5 border-gray-300 text-indigo-500 cursor-pointer focus:ring-indigo-500" />
                                                <span class="ml-2 tracking-wide cursor-pointer"> {{ branch.name }} </span>
                                            </label>
                                        </div>
                                    </dd>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="grid sm:grid-cols-3 sm:gap-2 sm:px-6">
                                <dt class="text-sm leading-5 font-semibold text-gray-500"></dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div class="mt-6 flex space-x-3 md:mt-0">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                            <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                            Create
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
