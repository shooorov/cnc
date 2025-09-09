<script setup>
import { reactive } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { PencilSquareIcon } from '@heroicons/vue/24/outline'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import Alert from '@/Components/Alert.vue'

const page = usePage()

const props = defineProps({
    role: Object,
    permissions: Object,
    replaceable_words: Object,
    hidden_options: Array
})

const breadcrumbs = [
    { name: 'Roles', href: route('role.index'), current: false },
    { name: 'Permission Page', href: '#', current: false }
]

const form = useForm({
    permissions: props.permissions
})

const replaceable = reactive(Object.keys(props.replaceable_words))

const submit = () => {
	page.props.alertMessage = {};

    form.patch(route('role.permission.update', props.role.id), {})
}

const dressUp = (original_title) => {
    if (original_title) {
        let string_array = original_title.split('_')
        for (let i = 0; i < string_array.length; i++) {
            let name = string_array[i]
            if (replaceable.includes(name)) {
                name = props.replaceable_words[name]
            }
            // updated code -- --check---- upper code bracket name doesn't match
            Object.keys(props.replaceable_words).forEach((item) => {
                name = name.replace(item, props.replaceable_words[item])
            })

            let new_value = name.charAt(0).toUpperCase() + name.slice(1)

            string_array[i] = new_value
        }
        return string_array.join(' ')
    }
    return original_title
}

const checkAll = (permissions, event) => {
    let is_checked = event.target.checked
    Object.values(permissions).forEach(function (permission) {
        Object.values(permission).forEach(function (item) {
            item.is_checked = is_checked
        })
    })
}

const checkParent = (options, event) => {
    let is_checked = event.target.checked
    Object.values(options).forEach(function (item) {
        item.is_checked = is_checked
    })
}
</script>
<template>
    <Head title="Role Permission" />

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
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0 mb-4 sm:mb-0">{{ role.name }}'s Permission</p>

                        <div class="flex-shrink-0 flex space-x-3">
                            <div class="flex items-center">
                                <label :for="`permissions`" class="mr-3 block select-none cursor-pointer">All</label>
                                <input
                                    :id="`permissions`"
                                    @change="checkAll(form.permissions, $event)"
                                    ref="all"
                                    type="checkbox"
                                    class="all-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 rounded transition duration-150 ease-in-out cursor-pointer" />
                            </div>

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

                    <form @submit.prevent="submit">
                        <dl class="py-6 px-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6 xl:gap-8 mb-6">
                                <div v-for="(options, name) in form.permissions" :key="name" v-show="!hidden_options.includes(name)" class="group sm:px-4">
                                    <dt class="pl-0 pr-4 pb-2 my-3 text-lg leading-10 font-medium text-gray-700 border-b border-modify-500">
                                        <div class="flex items-center">
                                            <input
                                                :id="`permissions[${name}]`"
                                                @change="checkParent(options, $event)"
                                                type="checkbox"
                                                class="form-checkbox w-5 h-5 cursor-pointer focus:ring-primary-600 focus:border-primary-600 text-primary-600 rounded transition duration-150 ease-in-out" />
                                            <label :for="`permissions[${name}]`" class="ml-2 block leading-5 select-none cursor-pointer">
                                                {{ dressUp(name) }}
                                            </label>
                                        </div>
                                    </dt>
                                    <dd v-for="(value, title) in options" :key="title" class="pl-7 sm:pl-0 pr-4 mt-2 text-sm text-gray-700">
                                        <div class="flex items-center">
                                            <input
                                                :id="`permissions[${name}][${title}]`"
                                                v-model="value.is_checked"
                                                type="checkbox"
                                                class="form-checkbox w-5 h-5 cursor-pointer focus:ring-primary-600 focus:border-primary-600 text-primary-600 rounded transition duration-150 ease-in-out" />
                                            <label :for="`permissions[${name}][${title}]`" class="ml-2 block leading-5 select-none cursor-pointer">
                                                {{ dressUp(title) }}
                                            </label>
                                        </div>
                                    </dd>
                                </div>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-gray-300" /></div>
                                <div class="relative flex justify-center ml-4"><span class="px-3 bg-white text-lg font-medium text-gray-900"></span></div>
                            </div>

                            <div class="max-w-full mx-auto py-4">
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
