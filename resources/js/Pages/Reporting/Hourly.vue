<template>
    <Head title="Products" />

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
                            Sale Daily ({{ form.start_date.split("-").reverse().join("/") }} - {{ form.end_date.split("-").reverse().join("/") }})
                        </p>

                        <div class="flex-shrink-0 flex space-x-3">
                        </div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="px-5 py-5 mx-auto max-w-5xl">
                            <div class="py-2 sm:grid sm:grid-cols-8 sm:gap-4">
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1"></dd>

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

										<button @click="clearFilter"
											class="inline-flex items-center px-4 py-1 border border-primary-600 rounded-r shadow-sm text-sm font-medium text-primary-700 bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
											<ArrowPathIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
											Clear
										</button>
									</div>
								</dd>
							</div>
						</dl>
					</form>

					<div class="px-6 py-8">
						<div id="chart_line" style="height: 500px;" class="w-full bg-white overflow-hidden shadow rounded-lg"></div>
						<!-- <div id="chart_column" style="height: 500px;" class="w-full bg-white overflow-hidden shadow rounded-lg"></div> -->
					</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { reactive } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusOptions from '@/Components/StatusOptions.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Alert from '@/Components/Alert.vue';
import ModelOptions from '@/Components/ModelOptions.vue';
import Combobox from '@/Components/Combobox.vue';
import Status from '@/Components/Status.vue';

import { ChartLine } from '@/charts';

import {
    PlusIcon,
    CogIcon,
} from '@heroicons/vue/24/solid';

import {
    ArrowTopRightOnSquareIcon,
    ArrowPathIcon,
    MagnifyingGlassIcon,
    PencilSquareIcon,
    PrinterIcon,
	PresentationChartLineIcon,
	TableCellsIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';

export default {
    layout: AuthenticatedLayout,

    components: {
        Alert,
        Breadcrumb,
        Combobox,
        Head,
        Link,
        ModelOptions,
        Status,
        StatusOptions,

        ArrowTopRightOnSquareIcon,
        ArrowPathIcon,
        CogIcon,
        MagnifyingGlassIcon,
        PencilSquareIcon,
        PlusIcon,
		PresentationChartLineIcon,
        PrinterIcon,
		TableCellsIcon,
        TrashIcon,
    },

    props:{
		navigation: Object,
        string_change: Object,

        filter: Object,
        data: Array,
        branch: Object,
    },

    mounted() {
        console.log("mounted");
        this.loadChart();
    },

    watch: {
        'branch'() {
            console.log("on branch change");
            this.loadChart();
        }
    },

    methods: {
        loadChart() {
			ChartLine("chart_line", this.data, "Hourly", "Sales");
			// ChartColumn("chart_column", this.data);
        },
    },

    setup(props) {
        const breadcrumbs = [
			{ name: 'Sale', href: route('summary.overview'), current: false },
            { name: 'Summary', href: '#', current: false },
        ];

        const form = reactive({
            end_date: props.filter.end_date,
            start_date: props.filter.start_date,
        })

		function clearFilter() {
			for (const [key, value] of Object.entries(form)) {
				form[key] = '';
			}
		}

        function submit() {
            router.visit(route('summary.hourly'), {
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
