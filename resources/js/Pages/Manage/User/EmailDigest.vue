<script setup>
import { reactive, ref } from 'vue'
import { router, Head, Link, usePage, useForm } from '@inertiajs/vue3'
import { PlusIcon } from '@heroicons/vue/24/solid'
import { PencilSquareIcon } from '@heroicons/vue/24/outline'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import Notification from '@/Components/Notification.vue'

const page = usePage()

const props = defineProps({
	users: Array,
	digest: Object,
})

const form = useForm({
	digest: props.digest,
	user_id: null,
	branch_id: null,
})

props.users.forEach(user => {
	form.digest[user.id] = []
	page.props.branches.forEach(branch => {
		form.digest[user.id][branch.id] = props.digest[user.id] ? props.digest[user.id][branch.id] : false
	})
})

function update(user_id, branch_id) {
	form.user_id = user_id
	form.branch_id = branch_id
	page.props.alertMessage = {};

	form.post(route('user.digest.update'), {
		onFinish: () => { }
	})
}

const breadcrumbs = [
	{ name: 'Users', href: route('user.index'), current: false },
	{ name: 'Email Digest Page', href: '#', current: false }
]
</script>
<template>
	<Head title="Email Digest" />

	<AuthenticatedLayout>
		<div class="bg-white shadow">
			<div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
				<div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
					<div class="flex-1 min-w-0">
						<Breadcrumb :breadcrumbs="breadcrumbs" />
					</div>
					<div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
						<Link :href="route('user.create')"
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
						<p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">Email Digest</p>

						<div class="flex-shrink-0 flex space-x-3">
						</div>
					</div>

					<Notification />

					<form @submit.prevent="submit">
						<dl class="space-y-8 py-6 px-6">
							<table class="table-auto sm:table-fixed min-w-full w-full">
								<thead>
									<tr>
										<th
											class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">
											S.N.</th>
										<th
											class="border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">
											Name</th>
										<th v-for="branch in page.props.branches" :key="branch.id"
											class="w-32 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">
											{{ branch.name }}
										</th>
									</tr>
								</thead>
								<tbody class="bg-white">
									<tr v-for="(user, index) in users" :key="user.id"
										:class="[index % 2 == 0 ? 'bg-white' : 'bg-gray-50', 'border-b']">
										<td class="px-3 py-1 whitespace-wrap break-words">
											<div class="text-sm leading-5 text-gray-700 text-center">
												{{ parseInt(index) + 1 }}
											</div>
										</td>

										<td class="px-3 py-1 whitespace-wrap break-words">
											<Link :href="route('user.edit', user.id)"
												class="text-sm leading-5 text-blue-600 hover:underline">
											{{ user.name }}
											</Link>
											<div class="text-sm leading-5 text-gray-700 text-left">{{ user.email }}</div>
										</td>

										<td v-for="item in page.props.branches" :key="item.id"
											class="p-2 whitespace-nowrap text-center">
											<input type="checkbox"
												:id="`access_${user.id}_${item.id}`"
												@change="update(user.id, item.id)" v-model="form.digest[user.id][item.id]"
												class="form-checkbox w-5 h-5 border-gray-300 text-indigo-500 cursor-pointer focus:ring-indigo-500 rounded" />
										</td>
									</tr>
								</tbody>
							</table>

							<div class="relative">
								<div class="absolute inset-0 flex items-center" aria-hidden="true">
									<div class="w-full border-t border-gray-300" />
								</div>
								<div class="relative flex justify-center ml-4"><span
										class="px-3 bg-white text-lg font-medium text-gray-900"></span></div>
							</div>

							<div class="mx-auto">
								<div class="flex justify-end">
								</div>
							</div>
						</dl>
					</form>
				</div>
			</div>
		</div>
	</AuthenticatedLayout></template>
