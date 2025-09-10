<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue'
import Notification from '@/Components/Notification.vue'
import Status from '@/Components/Status.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { PlusIcon } from '@heroicons/vue/24/solid'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const page = usePage()

const props = defineProps({
    users: Array,
    page_roles: Array,
    access: Object
})

const form = useForm({
    access: props.access,
    user_id: null,
    branch_id: null
})

props.users.forEach((user) => {
    form.access[user.id] = []
    page.props.branches.forEach((branch) => {
        form.access[user.id][branch.id] = props.access[user.id] ? props.access[user.id][branch.id] : false
    })
})

const roles = props.page_roles

roles.push({ name: 'Waiter' })
roles.push({ name: 'Chef' })
roles.push({ name: 'Barista' })

const filterBranches = ref(
    page.props.branches.map((i) => {
        return {
            id: i.id,
            name: i.name
        }
    })
)

const filterRoles = ref(
    roles.map((i) => {
        return {
            name: i.name
        }
    })
)

const selected_roles = computed(() => {
    return filterRoles.value.filter((i) => i.is_checked).map((i) => i.name.toLowerCase())
})

const isAllBranchUnChecked = computed(() => {
    return filterBranches.value.filter((i) => i.is_checked).length == 0
})
const isAllRoleUnChecked = computed(() => {
    return filterRoles.value.filter((i) => i.is_checked) == 0
})

function update(user_id, branch_id) {
    form.user_id = user_id
    form.branch_id = branch_id
    page.props.alertMessage = {}

    form.post(route('branch.access.update'), {
        onFinish: () => {}
    })
}

const breadcrumbs = [
    { name: 'Branches', href: route('branch.index'), current: false },
    { name: 'Access Page', href: '#', current: false }
]

const colorClasses = {
    super_admin: 'bg-red-100 text-red-700 rounded-full',
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
    <Head title="Branch Access" />

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
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">Branch Access</p>

                        <div class="flex-shrink-0 flex space-x-3"></div>
                    </div>

                    <Notification />

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
                        <div class="grid sm:grid-cols-5 sm:gap-2">
                            <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8">Role</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-4 grid sm:grid-cols-4 sm:gap-2">
                                <div v-for="role in filterRoles" :key="role.name" class="mt-3 space-y-4">
                                    <label class="inline-flex items-center mr-5">
                                        <input
                                            v-model="role.is_checked"
                                            type="checkbox"
                                            class="form-checkbox w-5 h-5 border-gray-300 text-purple-500 cursor-pointer focus:ring-purple-500 rounded" />
                                        <span class="ml-2 tracking-wide cursor-pointer"> {{ role.name }} </span>
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
                                        <th class="w-32 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Name</th>
                                        <th class="w-16 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Role</th>
                                        <!-- <th class="w-1/2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Type</th> -->
                                        <th
                                            v-for="item in filterBranches"
                                            :key="item.id"
                                            v-show="item.is_checked || isAllBranchUnChecked"
                                            class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">
                                            {{ item.name }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <tr
                                        v-for="(user, index) in users.filter((user) => user.is_admin != true)"
                                        :key="user.id"
                                        v-show="selected_roles.includes(user.role_name) || isAllRoleUnChecked"
                                        :class="[index % 2 == 0 ? 'bg-white' : 'bg-gray-50', 'border-b']">
                                        <td class="px-3 py-1 whitespace-wrap break-words">
                                            <div class="text-sm leading-5 text-gray-700 text-center">{{ parseInt(index) + 1 }}</div>
                                        </td>

                                        <td class="px-3 py-1 whitespace-wrap break-words">
                                            <Link :href="route('user.edit', user.id)" class="text-sm leading-5 text-blue-600 hover:underline">{{ user.name }}</Link>
                                        </td>

                                        <td class="px-3 py-1 whitespace-wrap break-words">
                                            <Status class="ml-1" :name="user.role_name" :colors="colorClasses" />
                                        </td>

                                        <td
                                            v-for="item in filterBranches"
                                            :key="item.id"
                                            v-show="item.is_checked || isAllBranchUnChecked"
                                            class="p-2 whitespace-nowrap text-center">
                                            <input
                                                type="checkbox"
                                                :id="`access_${user.id}_${item.id}`"
                                                @change="update(user.id, item.id)"
                                                v-model="form.access[user.id][item.id]"
                                                class="form-checkbox w-5 h-5 border-gray-300 text-indigo-500 cursor-pointer focus:ring-indigo-500 rounded" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                    <div class="w-full border-t border-gray-300" />
                                </div>
                                <div class="relative flex justify-center ml-4"><span class="px-3 bg-white text-lg font-medium text-gray-900"></span></div>
                            </div>

                            <div class="mx-auto">
                                <div class="flex justify-end"></div>
                            </div>
                        </dl>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
