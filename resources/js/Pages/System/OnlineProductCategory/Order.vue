<template>
    <Head title="Specification" />

    <div>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>

                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                        <Link :href="route('online_product_category.create')" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
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
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0"> {{ 'Online ' + string_change.product + ' Categories Order' }} </p>

                        <div class="flex-shrink-0 flex space-x-3">
                        </div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
						<dl class="space-y-8 py-6 px-6">
							<table class="table-auto sm:table-fixed min-w-full w-full">
								<thead>
									<tr>
										<th class="p-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center w-12">S.N.</th>
										<th class="p-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Name</th>
										<th class="p-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Order</th>
									</tr>
								</thead>
								<tbody class="bg-white">
									<tr v-for="(category, index) in online_product_categories" :key="category.id" :class="[index % 2 == 0 ? 'bg-white' : 'bg-gray-50', 'border-b']">
										<td class="p-2 whitespace-wrap break-words">
											<div class="text-sm leading-5 text-gray-700 text-center">{{ (parseInt(index) + 1) }}</div>
										</td>

										<td class="p-2 whitespace-wrap break-words">
											<div class="text-sm leading-5 text-gray-700">{{ category.name }}</div>
										</td>

                                        <td class="p-2 text-center whitespace-wrap break-words">
                                            <div class="flex justify-end">
                                                <Link @click="positioning(category.id, 'top')" v-show="index" class="text-indigo-600 hover:text-indigo-800 ml-3" title="top">
                                                    <ChevronDoubleUpIcon class="w-6 h-6" aria-hidden="true" />
                                                </Link>
                                                <Link @click="positioning(category.id, 'up')" v-show="index" class="text-indigo-600 hover:text-indigo-800 ml-3" title="up">
                                                    <ChevronUpIcon class="w-6 h-6" aria-hidden="true" />
                                                </Link>
                                                <Link @click="positioning(category.id, 'down')" v-show="index != (online_product_categories.length - 1)" class="text-indigo-600 hover:text-indigo-800 ml-3" title="down">
                                                    <ChevronDownIcon class="w-6 h-6" aria-hidden="true" />
                                                </Link>
                                                <Link @click="positioning(category.id, 'bottom')" v-show="index != (online_product_categories.length - 1)" class="text-indigo-600 hover:text-indigo-800 ml-3" title="bottom">
                                                    <ChevronDoubleDownIcon class="w-6 h-6" aria-hidden="true" />
                                                </Link>
                                                <Link v-show="index" v-bind:class="index == (online_product_categories.length - 1)? 'mr-9' : ''" class="text-indigo-600 hover:text-indigo-800"></Link>
                                                <Link v-show="index" v-bind:class="index == (online_product_categories.length - 1)? 'mr-9' : ''" class="text-indigo-600 hover:text-indigo-800"></Link>
                                            </div>
                                        </td>
                                    </tr>
								</tbody>
							</table>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"> <div class="w-full border-t border-gray-300" /> </div>
                                <div class="relative flex justify-center ml-4"> <span class="px-3 bg-white text-lg font-medium text-gray-900"></span> </div>
                            </div>

                            <div class="max-w-xl mx-auto">
                                <div class="flex justify-end">
                                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
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
    </div>

</template>

<script>
import { ref, reactive } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Alert from '@/Components/Alert.vue';

import {
    PlusIcon,
} from '@heroicons/vue/24/solid';

import {
    ChevronUpIcon,
    ChevronDoubleUpIcon,
    ChevronDownIcon,
    ChevronDoubleDownIcon,
    PencilSquareIcon,
} from '@heroicons/vue/24/outline';

export default {
    layout: AuthenticatedLayout,

    components: {
        Alert,
        Breadcrumb,
        Head,
        Link,
        ref,

        ChevronUpIcon,
        ChevronDoubleUpIcon,
        ChevronDownIcon,
        ChevronDoubleDownIcon,
        PencilSquareIcon,
        PlusIcon,
    },

    props: {

        string_change: Object,

        online_product_categories: Array,
    },

    methods: {
        positioning(id, position) {
            this.form.position = position;
            this.form.category_id = id;
            this.submit();
        }
    },

    setup (props) {
        const breadcrumbs = [
            { name: 'Online ' + props.string_change.product_s + ' Categories', href: route('online_product_category.index'), current: false },
            { name: 'Order Page', href: '#', current: false },
        ]

        const form = reactive({
            position: null,
            category_id: null,
        })

        function submit() {
            router.post(route('online_product_category.positioning'), form);
        }

        return {
            breadcrumbs,
            form,
            submit,
        }
    },
}
</script>
