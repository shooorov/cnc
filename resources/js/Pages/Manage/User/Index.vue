<script setup>
import { onMounted, reactive, ref } from 'vue'
import { router, Head, Link, usePage, useForm } from '@inertiajs/vue3'
import {
    ArrowRightOnRectangleIcon,
    ChatBubbleBottomCenterTextIcon,
    ArrowTopRightOnSquareIcon,
    PencilSquareIcon,
    TrashIcon,
    CogIcon,
	PlusIcon,
	MagnifyingGlassIcon,
	ArrowPathIcon,
	EnvelopeIcon,
	UsersIcon
} from '@heroicons/vue/24/outline'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import Alert from '@/Components/Alert.vue'
import Status from '@/Components/Status.vue'
import Combobox from '@/Components/Combobox.vue'

const page = usePage()

const props = defineProps({
    filter: Object,
    users: Array,
    roles: Array,
    statuses: Array,
    available_branches: Array,
})

const form = reactive(props.filter)

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

const destroy = (route, message = 'Are you sure you want to delete?') => {
    if (confirm(message)) {
        router.delete(route)
    }
}

const clearFilter = () => {
    for (const [key, value] of Object.entries(form)) {
        form[key] = ''
    }
}

const submit = () => {
    router.visit(route('user.index'), {
        data: form
    })
}

const breadcrumbs = [
    { name: 'Users', href: route('user.index'), current: false },
    { name: 'List Page', href: '#', current: false }
]

const colorClasses = {
    'super admin': 'bg-red-100 text-red-700 rounded-full',
    waiter: 'bg-yellow-100 text-yellow-800 rounded-full',
    chef: 'bg-blue-100 text-blue-700 rounded-full',
    barista: 'bg-purple-100 text-purple-700 rounded-full',
    rider: 'bg-pink-100 text-pink-700 rounded-full',

    pending: 'bg-yellow-100 text-yellow-900 rounded-md',
    active: 'bg-green-100 text-green-900 rounded-md',
    inactive: 'bg-rose-100 text-rose-900 rounded-md'
}
</script>
<template>
    <Head title="Users" />

    <AuthenticatedLayout>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>
                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                        <Link
                            :href="route('user.create')"
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
					<div
						class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
						<div class="flex-1">
							<h3 class="text-base font-semibold leading-6 text-gray-900">Users</h3>
							<p class="mt-1 text-sm text-gray-500">
							</p>
						</div>
						<div class="flex-shrink-0 flex space-x-3">
                            <Link
                                :href="route('user.digest')"
                                class="inline-flex items-center w-40 rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
                                <EnvelopeIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                                Email Digest
                            </Link>

                            <Link
                                :href="route('branch.access')"
                                class="inline-flex items-center w-40 rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
                                <UsersIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                                Branch Users
                            </Link>

                            <!-- <Link :href="route('user.history')" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
                                <ChatBubbleBottomCenterTextIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                                History
                            </Link> -->
						</div>
					</div>

                    <Alert />

					<form @submit.prevent="submit">
						<dl class="px-5 py-5 mx-auto max-w-5xl">
							<div class="py-2 sm:grid sm:grid-cols-8 sm:gap-4">
								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">Branches</label>
									<Combobox class="mt-1" v-model="form.branch" :items="available_branches" />
								</dd>

								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">Roles</label>
									<Combobox class="mt-1" v-model="form.role" :items="roles" />
								</dd>

								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">status</label>
									<Combobox class="mt-1" v-model="form.status" :items="statuses" />
								</dd>

								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">Action</label>
									<div class="inline-flex mt-1 rounded" role="group">
										<button
											type="submit"
											class="inline-flex items-center px-4 py-2 border border-transparent rounded-l shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
											<MagnifyingGlassIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
											Search
										</button>

										<button
											@click="clearFilter"
											class="inline-flex items-center px-4 py-1 border border-primary-600 rounded-r shadow-sm text-sm font-medium text-primary-700 bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
											<ArrowPathIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
											Clear
										</button>
									</div>
								</dd>
							</div>
						</dl>
					</form>

					<table class="table-auto min-w-full w-full" id="table">
                        <thead>
                            <tr>
                                <th class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">S.N.</th>
                                <th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Name</th>
                                <th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Branch</th>
                                <th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Role</th>
                                <th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Status</th>
                                <th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr v-for="(user, index) in users" :key="index" :class="[index % 2 == 0 ? 'bg-white' : 'bg-gray-50', 'border-b']">
                                <td class="px-3 py-2 text-center">{{ index + 1 }}</td>

                                <td class="px-3 py-2 whitespace-wrap break-words">
                                    <div class="flex items-center">
                                        <div class="shrink-0 h-10 w-10 mr-4">
                                            <img class="h-10 w-10 rounded-full" :src="user?.image_url" alt="" />
                                        </div>
                                        <div>
                                            <div class="text-sm leading-5 font-semibold text-gray-700">{{ user.name }}</div>
                                            <div class="text-sm leading-5 text-gray-600">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-3 py-2 whitespace-wrap break-words">
									{{ user.branch_name }}
                                </td>

                                <td class="px-3 py-2 whitespace-nowrap text-center">
                                    <Status class="" :name="user.role_name" :colors="colorClasses" />
                                </td>

                                <td class="px-3 py-2 whitespace-wrap text-center">
                                    <Status class="" :name="user.status" :colors="colorClasses" />
                                </td>

                                <td class="px-3 py-2 whitespace-wrap break-words">
                                    <div class="flex justify-end gap-2 mg:gap-3">
                                        <Link
                                            v-show="page.props.auth.user.id != user.id && page.props.auth.user.is_admin"
                                            :href="route('user.login', user.id)"
                                            class="text-yellow-600 hover:text-yellow-800"
                                            title="login">
                                            <ArrowRightOnRectangleIcon class="w-6 h-6" aria-hidden="true" />
                                        </Link>
                                        <Link :href="route('user.show', user.id)" class="text-primary-600 hover:text-primary-800" title="detail">
                                            <ArrowTopRightOnSquareIcon class="w-6 h-6" aria-hidden="true" />
                                        </Link>
                                        <Link :href="route('user.edit', user.id)" class="text-indigo-600 hover:text-indigo-800" title="edit">
                                            <PencilSquareIcon class="w-6 h-6" aria-hidden="true" />
                                        </Link>
                                        <button @click="destroy(route('user.destroy', user.id))" class="text-red-600 hover:text-red-800" title="delete">
                                            <TrashIcon class="w-6 h-6" aria-hidden="true" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
