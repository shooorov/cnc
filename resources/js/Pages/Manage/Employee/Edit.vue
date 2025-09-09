<template>
    <Head title="Edit Employee" />

    <div>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>
                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                        <Link :href="route('employee.create')" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
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
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">Employee Edit</p>

                        <div class="flex-shrink-0 flex space-x-3">
                        </div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="space-y-4 sm:space-y-6 px-5 py-6">
                            <div class="max-w-5xl mx-auto space-y-4 sm:space-y-6">
                                <div class="grid sm:grid-cols-3 sm:gap-2">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8"> Designation <span class="text-red-500">*</span> </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <Listbox class="mt-1" v-model="form.designation_id" :items="designations" />
                                    </dd>
                                </div>

                                <div class="grid sm:grid-cols-3 sm:gap-2">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8"> Name <span class="text-red-500">*</span> </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <input v-model="form.name" placeholder="Name" type="text" class="block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                    </dd>
                                </div>

                                <div class="grid sm:grid-cols-3 sm:gap-2">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8"> Email <span class="text-red-500">*</span> </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
										<input v-model="form.email" placeholder="Email" type="text" autocomplete="off" class="block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                    </dd>
                                    <dt v-show="employee.in_user" class="text-sm leading-10 font-semibold text-gray-700 tracking-wider text-left pr-8"> will affect login email too. </dt>
                                </div>

                                <div class="grid sm:grid-cols-3 sm:gap-2">
                                    <dt class="text-sm sm:leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8"> Gross </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                        <div class="relative">
                                            <input v-model="form.gross" placeholder="Gross" type="text" readonly autocomplete="off" class="mt-1 block w-full px-4 pr-16 focus:outline-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded">
                                            <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                        </div>
                                    </dd>
                                </div>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"> <div class="w-full border-t border-gray-300" /> </div>
                                <div class="relative flex justify-center ml-4"> <span class="px-3 bg-white text-lg font-medium text-gray-900"> Salary </span> </div>
                            </div>

                            <div class="max-w-5xl mx-auto space-y-4 sm:space-y-6">
                                <div class="grid grid-cols-8 gap-3">

                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Basic </label>
                                        <div class="relative">
                                            <input v-model="form.basic" @keyup="calculation()" @change="calculation()" placeholder="Basic" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="mt-1 block w-full px-4 pr-16 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                            <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                        </div>
                                    </div>

                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Rent </label>
                                        <div class="relative">
                                            <input v-model="form.rent" @keyup="calculation()" @change="calculation()" placeholder="Rent" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="mt-1 block w-full px-4 pr-16 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                            <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                        </div>
                                    </div>

                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Food </label>
                                        <div class="relative">
                                            <input v-model="form.food" @keyup="calculation()" @change="calculation()" placeholder="Food" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="mt-1 block w-full px-4 pr-16 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                            <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                        </div>
                                    </div>

                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Transport </label>
                                        <div class="relative">
                                            <input v-model="form.transport" @keyup="calculation()" @change="calculation()" placeholder="Transport" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="mt-1 block w-full px-4 pr-16 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                            <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                        </div>
                                    </div>

                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Medical </label>
                                        <div class="relative">
                                            <input v-model="form.medical" @keyup="calculation()" @change="calculation()" placeholder="Medical" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="mt-1 block w-full px-4 pr-16 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                            <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                        </div>
                                    </div>

                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Other </label>
                                        <div class="relative">
                                            <input v-model="form.other" @keyup="calculation()" @change="calculation()" placeholder="Other" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="mt-1 block w-full px-4 pr-16 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                            <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                        </div>
                                    </div>

                                    <div class="col-span-6 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Bonus </label>
                                        <div class="relative">
                                            <input v-model="form.bonus" @keyup="calculation()" @change="calculation()" placeholder="Bonus" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="mt-1 block w-full px-4 pr-16 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                            <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"> <div class="w-full border-t border-gray-300" /> </div>
                                <div class="relative flex justify-center ml-4"> <span class="px-3 bg-white text-lg font-medium text-gray-900"> </span> </div>
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
import { reactive } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Alert from '@/Components/Alert.vue';
import InputError from '@/Components/InputError.vue';
import Listbox from '@/Components/Listbox.vue';

import {
    PlusIcon,
    XMarkIcon,
    ArrowPathIcon,
} from '@heroicons/vue/24/solid';

import {
    PencilSquareIcon,
} from '@heroicons/vue/24/outline';

export default {
    layout: AuthenticatedLayout,

    components: {
        Alert,
        Breadcrumb,Head,
        InputError,
        Listbox,
        Link,

        PlusIcon,
        PencilSquareIcon,
        XMarkIcon,
        ArrowPathIcon,
    },

    props: {


        designations: Array,
        employee: Object,
    },

    methods: {
        calculation(){
            this.form.gross = parseInt(this.form.basic || 0);
            this.form.gross += parseInt(this.form.rent || 0);
            this.form.gross += parseInt(this.form.medical || 0);
            this.form.gross += parseInt(this.form.transport || 0);
            this.form.gross += parseInt(this.form.food || 0);
            this.form.gross += parseInt(this.form.other || 0);
        },

    },

    setup (props) {
        const breadcrumbs = [
            { name: 'Employees', href: route('employee.index'), current: false },
            { name: 'Edit Page', href: '#', current: false },
        ]

        const form = reactive({
            designation_id: props.employee.designation_id,
            name: props.employee.name,
            email: props.employee.email,
            gross: props.employee.gross,
            basic: props.employee.basic,
            rent: props.employee.rent,
            food: props.employee.food,
            transport: props.employee.transport,
            medical: props.employee.medical,
            other: props.employee.other,
            bonus: props.employee.bonus,
        })

        function submit() {
            router.patch(route('employee.update', props.employee.id), form)
        }

        return {
            breadcrumbs,
            form,
            submit
        }
    },
}
</script>
