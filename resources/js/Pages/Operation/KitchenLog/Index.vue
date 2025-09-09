<template>
    <Head title="Kitchen Logs" />

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
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">
                            Kitchen Logs
                            <span v-if="form.start_date || form.end_date"> ({{ form.start_date }} - {{ form.end_date }}) </span>
                            <span v-else> ({{ today }}) </span>
                        </p>

                        <div class="flex-shrink-0 flex space-x-3">
                        </div>
                    </div>

                    <form @submit.prevent="submit">
                        <dl class="px-5 py-5 mx-auto max-w-5xl">
                            <div class="py-2 sm:grid sm:grid-cols-8 sm:gap-4">
                                <dd v-show="auth.user.is_admin" class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1"></dd>
                                <dd v-show="auth.user.is_admin" class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700"> User </label>
                                    <Combobox class="mt-1" v-model="form.user_id" :items="users" />
                                </dd>

								<dd v-if="form.start_date" class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">Start Date</label>
									<input v-model="form.start_date" type="date"
                                        class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
								</dd>

								<dd v-if="form.end_date" class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">End Date</label>
									<input v-model="form.end_date" type="date"
										class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
								</dd>

								<dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
									<label class="block text-sm font-medium text-gray-700">Action</label>
									<div class="inline-flex mt-1 rounded" role="group">
										<button type="submit"
											class="inline-flex items-center px-4 py-2 border border-transparent rounded-l shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
											<MagnifyingGlassIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
											Search
										</button>

										<button @click="clearFilter" type="reset"
											class="inline-flex items-center px-4 py-1 border border-primary-600 rounded-r shadow-sm text-sm font-medium text-primary-700 bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
											<ArrowPathIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
											Clear
										</button>
									</div>
								</dd>
							</div>
						</dl>
					</form>

                    <Alert />

                    <table class="table-auto sm:table-fixed min-w-full w-full" id="table">
                        <thead>
                            <tr>
                                <th class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">S.N.</th>
                                <th v-show="auth.user.is_admin && !form.user_id" class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">User</th>
                                <th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Time</th>
                                <th class="w-48 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Duration</th>
                                <th class="w-36 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Production</th>
                                <th class="w-24 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Item</th>
                                <th class="w-24 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white">
                            <tr v-for="(kitchen_log, index) in kitchen_logs" :key="index" :class="[index % 2 == 0 ? 'bg-white' : 'bg-gray-50', 'border-b']">
                                <td class="p-2 text-center"> {{ parseInt(index) + 1 }} </td>

                                <td v-show="auth.user.is_admin && !form.user_id" class="p-2 whitespace-wrap">
                                    <div class="text-sm leading-5 text-gray-600">{{ kitchen_log.user_name }}</div>
                                </td>

                                <td class="p-2 whitespace-wrap">
                                    <div class="text-sm leading-5 text-gray-600">{{ kitchen_log.started_at_format + ' - ' + (kitchen_log.ended_at_format?? ' running') }}</div>
                                </td>

                                <td class="p-2 whitespace-wrap">
                                    <div class="text-sm leading-5 text-gray-600">{{ kitchen_log.time_duration }}</div>
                                </td>

                                <td class="p-2 whitespace-wrap">
                                    <div class="text-sm leading-5 text-gray-600">{{ kitchen_log.total_production }} </div>
                                </td>

                                <td class="p-2 whitespace-wrap">
                                    <div class="text-sm leading-5 text-gray-600">{{ kitchen_log.total_item }} </div>
                                </td>

                                <td class="px-3 py-1 whitespace-wrap break-words">
                                    <div class="flex justify-end">
                                        <button @click="deleteRecord(kitchen_log.id)" class="text-red-600 hover:text-red-800 ml-3" title="delete">
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
    </div>
</template>

<script>
import { reactive } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';

import Alert from '@/Components/Alert.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Status from '@/Components/Status.vue';
import Combobox from '@/Components/Combobox.vue';

import {
    PlusIcon,
} from '@heroicons/vue/24/solid';

import {
    ArrowTopRightOnSquareIcon,
    ArrowPathIcon,
    MagnifyingGlassIcon,
    PencilSquareIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';

const breadcrumbs = [
    { name: 'Kitchen Logs', href: route('kitchen.index'), current: false },
    { name: 'List Page', href: '#', current: false },
];

export default {
    layout: AuthenticatedLayout,

    components: {
        Alert,
        Combobox,
        Breadcrumb,
        Head,
        Link,Status,

        PlusIcon,
        ArrowTopRightOnSquareIcon,
        ArrowPathIcon,
        MagnifyingGlassIcon,
        PencilSquareIcon,
        TrashIcon,
    },

    props:{
        auth: Object,

        filter: Object,
        today: String,
        users: Array,
        kitchen_logs: Array,
    },

    mounted() {
        $('#table').DataTable({
            lengthMenu: [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
            length: 10,
            dom: "<'flex justify-center sm:justify-end mb-3'B><'flex flex-col sm:flex-row justify-between'lf><'block overflow-auto'rt><'flex flex-col sm:flex-row justify-between'ip>",
            buttons: [
                'copy', 'excel'
            ],
        });
    },

    methods: {
        deleteRecord(record_id) {
            if(confirm("Do you really want to delete this kitchen_log?")) {
                router.delete(route('kitchen_log.destroy', record_id));
            }
        }
    },

    setup(props) {
        const form = reactive({
            user_id: props.filter.user_id,
            end_date: props.filter.end_date,
            start_date: props.filter.start_date,
        })

		function clearFilter() {
			for (const [key, value] of Object.entries(form)) {
				form[key] = '';
			}
		}

        function submit() {
            router.visit(route('kitchen.index'), {
  				data: form,
			});
        }

        return {
            breadcrumbs,
            form,
			clearFilter,
            submit,
        }
    }
}
</script>
