<template>
    <Head title="Edit Product" />

    <div>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>
                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                        <Link
                            :href="route('product.create')"
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
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium mb-4 sm:mb-0">{{ string_change.product }} Edit</p>

                        <div class="flex-shrink-0 flex space-x-3">
                            <div class="mt-4 shrink-0 flex">
                                <StatusOptions class="ml-3" as="button" :status="status_of_product" :items="statuses" :href="route('product.status.update', productId)" />

                                <button
                                    type="button"
                                    @click="submit"
                                    class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    <PencilSquareIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                    Update
                                </button>
                            </div>
                        </div>
                    </div>

                    <Alert />

                    <form @submit.prevent="submit">
                        <dl class="space-y-4 sm:space-y-6 px-5 py-6">
                            <div class="max-w-xl mx-auto">
                                <div class="grid grid-cols-4 gap-4">
                                    <div class="col-span-4 sm:col-span-2">
                                        <label class="mb-1 block text-sm font-medium text-gray-700"> Image </label>
                                        <div class="relative h-32 w-64">
                                            <div class="flex text-sm text-gray-700 border border-gray-400 rounded-md bg-gray-50">
                                                <img class="object-cover h-32 w-64" :src="form.image" title="Click to upload image" />
                                                <div class="absolute bottom-0 bg-gray-50 w-full text-center opacity-60 cursor-pointer" @click="$refs.image.click()">
                                                    <span class="text-xs text-black">Click to upload</span>
                                                </div>
                                                <input
                                                    ref="image"
                                                    type="file"
                                                    class="hidden"
                                                    @input="form.image_file = $event.target.files[0]"
                                                    @change="form.image = onFileInput($event.target.files[0])"
                                                    accept=".png, .jpg, .jpeg" />
                                            </div>
                                            <div v-show="form.image != form.image_default" class="absolute top-0 right-0 px-0 py-0 bg-gray-50 opacity-60" title="Remove Image">
                                                <XMarkIcon
                                                    class="w-5 h-5 text-red-500 hover:text-red-700 cursor-pointer"
                                                    aria-hidden="true"
                                                    @click=";(form.image = form.image_default), (form.image_file = null), (form.image_removed = true)" />
                                            </div>
                                            <div v-show="form.image != product.data.image" class="absolute top-0 left-0 px-0 py-0 bg-gray-50 opacity-60" title="Refresh Image">
                                                <ArrowPathIcon
                                                    class="w-5 h-5 text-primary-500 hover:text-primary-700 cursor-pointer"
                                                    aria-hidden="true"
                                                    @click=";(form.image = product.data.image), (form.image_file = null), (form.image_removed = false)" />
                                            </div>
                                        </div>
                                        <InputError :message="$page.props.errors.image" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-2"></div>

                                    <div class="col-span-4 sm:col-span-2">
                                        <label class="mb-1 block text-sm font-medium text-gray-700"> Categories <span class="text-red-500">*</span> </label>
                                        <Listbox class="mt-1" v-model="form.product_category_id" :items="categories" />
                                        <InputError :message="$page.props.errors.product_category_id" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700"> Code </label>
                                        <input
                                            v-model="form.code"
                                            placeholder="Code"
                                            type="text"
                                            class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded-md" />
                                        <InputError :message="$page.props.errors.code" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700"> Name <span class="text-red-500">*</span> </label>
                                        <input
                                            v-model="form.name"
                                            placeholder="Name"
                                            type="text"
                                            class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded-md" />
                                        <InputError :message="$page.props.errors.name" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700"> English Name </label>
                                        <input
                                            v-model="form.english_name"
                                            placeholder="English Name"
                                            type="text"
                                            class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded-md" />
                                        <InputError :message="$page.props.errors.english_name" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700"> For Persons of </label>
                                        <input
                                            v-model="form.number_of_persons"
                                            placeholder="For how many persons"
                                            type="text"
                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                            class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded-md" />
                                        <InputError :message="$page.props.errors.number_of_persons" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700"> Description </label>
                                        <textarea
                                            v-model="form.description"
                                            placeholder="Description"
                                            rows="3"
                                            class="mt-1 block w-full px-4 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded-md"></textarea>
                                        <InputError :message="$page.props.errors.description" />
                                    </div>

                                    <div class="col-span-4 sm:col-span-4">
                                        <div class="relative flex items-start">
                                            <div class="flex items-center h-5">
                                                <input
                                                    type="checkbox"
                                                    id="vat-applicable"
                                                    placeholder="VAT Applicable"
                                                    v-model="form.vat_applicable"
                                                    class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer" />
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="vat-applicable" class="font-medium text-gray-700">VAT Applicable</label>
                                                <InputError :message="$page.props.errors.vat_applicable" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="is_platter">
                                <div class="relative my-8">
                                    <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-gray-300" /></div>
                                    <div class="relative flex justify-center">
                                        <span class="px-3 bg-white text-lg font-medium text-gray-900"> {{ string_change.product_s }} </span>
                                    </div>
                                </div>

                                <datalist id="products_list">
                                    <option v-for="product in products" :key="product.id" :value="product.name">{{ product.name }}</option>
                                </datalist>
                                <div class="max-w-4xl mx-auto">
                                    <table class="table-auto sm:table-fixed min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ string_change.product }}
                                                </th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                                <th scope="col" class="w-32 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ string_change.product }} Rate
                                                </th>
                                                <th scope="col" class="w-32 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ string_change.product }} Cost
                                                </th>
                                                <th scope="col" class="w-10 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="(platter_item, index) in form.platter_items" :key="index">
                                                <td class="px-1 py-2">
                                                    <input
                                                        list="products_list"
                                                        v-model="platter_item.item_name"
                                                        @change="platterCalculation(index)"
                                                        @keyup="platterCalculation(index)"
                                                        type="text"
                                                        class="block w-full px-2 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded-md" />
                                                </td>
                                                <td class="px-1 py-2">
                                                    <div class="relative">
                                                        <input
                                                            v-model="platter_item.quantity"
                                                            @change="platterCalculation(index)"
                                                            @keyup="platterCalculation(index)"
                                                            placeholder="Quantity"
                                                            type="text"
                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                            class="block w-full px-4 pr-12 focus:ring-primary-400 focus:border-primary-400 sm:text-sm border-gray-300 rounded-md" />
                                                        <span class="absolute py-2 top-0 right-0 m-px px-4 w-14 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">
                                                            pcs
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-1 py-2">
                                                    <input
                                                        :value="platter_item.rate"
                                                        readonly
                                                        placeholder="Rate"
                                                        type="text"
                                                        class="block w-full px-4 focus:ring-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded-md" />
                                                </td>
                                                <td class="px-1 py-2">
                                                    <input
                                                        :value="platter_item.cost"
                                                        readonly
                                                        placeholder="Cost"
                                                        type="text"
                                                        class="block w-full px-4 focus:ring-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded-md" />
                                                </td>
                                                <td class="px-1 py-2">
                                                    <button
                                                        v-show="form.platter_items.length > 1"
                                                        @click=";[removePlatter(index), platterCalculation()]"
                                                        type="button"
                                                        class="p-1 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                        <XMarkIcon class="h-4 w-4" aria-hidden="true" />
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="bg-white">
                                            <tr>
                                                <th colspan="1" class="text-left pt-3">
                                                    <button
                                                        @click="addPlatter()"
                                                        type="button"
                                                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                        Add Item
                                                        <PlusIcon class="ml-2 -mr-0.5 h-4 w-4" aria-hidden="true" />
                                                    </button>
                                                </th>
                                                <th colspan="2" class="py-2 px-2 text-right text-base font-medium text-primary-600">Regular Cost:</th>
                                                <th class="py-2 px-1 text-right text-lg font-medium">{{ form.production_cost }}</th>
                                                <th class="py-2 px-1 text-left text-lg font-medium">&#2547;</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="max-w-xl mx-auto mt-5">
                                    <div class="grid grid-cols-4 gap-4">
                                        <div class="col-span-4 sm:col-span-2">
                                            <label class="mb-1 block text-sm font-medium text-gray-700"> Regular Cost </label>
                                            <input
                                                :value="form.production_cost"
                                                placeholder="Production Cost"
                                                readonly
                                                type="text"
                                                class="mt-1 block w-full px-4 focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded-md" />
                                        </div>

                                        <div class="col-span-4 sm:col-span-2"></div>

                                        <div class="col-span-4 sm:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700"> Rate / Sale Price </label>
                                            <div class="relative">
                                                <input
                                                    v-model="form.rate"
                                                    @change="total"
                                                    @keyup="total"
                                                    placeholder="Rate"
                                                    type="text"
                                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                    autocomplete="off"
                                                    class="mt-1 block w-full px-4 pr-12 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded-md" />
                                                <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                            </div>
                                            <InputError :message="$page.props.errors.rate" />
                                        </div>

                                        <div class="col-span-4 sm:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700"> Discount </label>
                                            <div class="relative">
                                                <input
                                                    v-model="form.discount"
                                                    @change="total"
                                                    @keyup="total"
                                                    placeholder="Discount"
                                                    type="text"
                                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                    autocomplete="off"
                                                    class="mt-1 block w-full px-4 pr-12 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded-md" />
                                                <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                            </div>
                                            <InputError :message="$page.props.errors.discount" />
                                        </div>

                                        <div class="col-span-4 sm:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700"> Total </label>
                                            <div class="relative">
                                                <input
                                                    v-model="form.total"
                                                    placeholder="Total"
                                                    type="text"
                                                    readonly
                                                    class="mt-1 block w-full px-4 pr-12 focus:outline-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded-md" />
                                                <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                            </div>
                                            <InputError :message="$page.props.errors.total" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else>
                                <div class="relative my-8">
                                    <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-gray-300" /></div>
                                    <div class="relative flex justify-center">
                                        <span class="px-3 bg-white text-lg font-medium text-gray-900"> {{ string_change.item_s }} </span>
                                    </div>
                                </div>

                                <div class="max-w-2xl mx-auto my-4">
                                    <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-gray-200">
                                        <label class="block text-right text-sm font-medium text-gray-700 sm:mt-px sm:pt-2"> For </label>
                                        <div class="mt-1 sm:col-span-1 sm:mt-0">
                                            <div class="flex max-w-lg rounded-md shadow-sm">
                                                <input
                                                    v-model="form.ingredients_for"
                                                    @change="calculation()"
                                                    @keyup="calculation()"
                                                    placeholder="100"
                                                    type="text"
                                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                    class="block w-full min-w-0 flex-1 rounded-none rounded-l-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                                <span class="inline-flex items-center rounded-r-md border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-500 sm:text-sm"
                                                    >pcs</span
                                                >
                                            </div>
                                        </div>
                                        <p class="block text-left text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">{{ form.name }}</p>
                                    </div>
                                </div>

                                <datalist id="ingredients_list">
                                    <option v-for="item in items" :key="item.id" :value="item.name">{{ item.name }}</option>
                                </datalist>
                                <div class="max-w-4xl mx-auto">
                                    <table class="table-auto sm:table-fixed min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ string_change.item }}</th>
                                                <th scope="col" class="w-32 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ string_change.item }} Unit Rate
                                                </th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Per Piece</th>
                                                <th scope="col" class="w-32 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ string_change.item }} Cost
                                                </th>
                                                <th scope="col" class="w-10 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                            </tr>
                                        </thead>

                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="(group_item, index) in form.group_items" :key="index" class="divide-gray-200">
                                                <td class="px-1 py-2">
                                                    <input
                                                        list="ingredients_list"
                                                        v-model="group_item.item_name"
                                                        @change="calculation(index)"
                                                        @keyup="calculation(index)"
                                                        type="text"
                                                        class="block w-full px-2 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded-md" />
                                                </td>
                                                <td class="px-1 py-2">
                                                    <input
                                                        :value="group_item.avg_rate"
                                                        readonly
                                                        placeholder="Avg Rate"
                                                        type="text"
                                                        class="block w-full px-4 focus:ring-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded-md" />
                                                </td>
                                                <td class="px-1 py-2">
                                                    <div class="relative">
                                                        <input
                                                            v-model="group_item.quantity"
                                                            @change="calculation(index)"
                                                            @keyup="calculation(index)"
                                                            :readonly="!group_item.item_name"
                                                            placeholder="Quantity"
                                                            type="text"
                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                            class="block w-full px-4 pr-12 focus:ring-primary-400 focus:border-primary-400 sm:text-sm border-gray-300 rounded-md" />
                                                        <span class="absolute py-2 top-0 right-0 m-px px-4 w-14 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm"
                                                            >{{ group_item.unit ?? '-' }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-1 py-2">
                                                    <div class="relative">
                                                        <input
                                                            :value="group_item.quantity_use"
                                                            readonly
                                                            placeholder="Quantity"
                                                            type="text"
                                                            class="block w-full px-4 pr-12 focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded-md" />
                                                        <span class="absolute py-2 top-0 right-0 m-px px-4 w-14 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm"
                                                            >{{ group_item.unit_use ?? '-' }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-1 py-2">
                                                    <input
                                                        :value="group_item.cost"
                                                        readonly
                                                        placeholder="Cost"
                                                        type="text"
                                                        class="block w-full px-4 focus:ring-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded-md" />
                                                </td>
                                                <td class="px-1 py-2">
                                                    <button
                                                        v-show="form.group_items.length > 1"
                                                        @click=";[removeItem(index), calculation()]"
                                                        type="button"
                                                        class="p-1 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                        <XMarkIcon class="h-4 w-4" aria-hidden="true" />
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>

                                        <tfoot class="bg-white">
                                            <tr>
                                                <th colspan="1" class="text-left pt-3">
                                                    <button
                                                        @click="addItem()"
                                                        type="button"
                                                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                        Add Item
                                                        <PlusIcon class="ml-2 -mr-0.5 h-4 w-4" aria-hidden="true" />
                                                    </button>
                                                </th>
                                                <th colspan="3" class="py-2 px-2 text-right text-base font-medium text-indigo-600">Production Cost:</th>
                                                <th class="py-2 px-1 text-right text-lg font-medium">{{ form.production_cost }}</th>
                                                <th class="py-2 px-1 text-left text-lg font-medium">&#2547;</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="max-w-xl mx-auto mt-5">
                                    <div class="grid grid-cols-4 gap-4">
                                        <div class="col-span-4 sm:col-span-2">
                                            <label class="mb-1 block text-sm font-medium text-gray-700"> Margin Amount </label>
                                            <div class="relative">
                                                <input
                                                    v-model="form.margin_amount"
                                                    placeholder="Margin Amount"
                                                    type="text"
                                                    readonly
                                                    class="mt-1 block w-full px-4 pr-12 focus:outline-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded-md" />
                                                <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                            </div>
                                        </div>

                                        <div class="col-span-4 sm:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700"> Margin Percent </label>
                                            <div class="relative">
                                                <input
                                                    v-model="form.margin_percent"
                                                    placeholder="Margin Percent"
                                                    type="text"
                                                    readonly
                                                    class="mt-1 block w-full px-4 pr-12 focus:outline-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded-md" />
                                                <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#x25;</span>
                                            </div>
                                        </div>

                                        <div class="col-span-4 sm:col-span-2">
                                            <label class="mb-1 block text-sm font-medium text-gray-700"> Production Cost </label>
                                            <input
                                                :value="form.production_cost"
                                                placeholder="Production Cost"
                                                readonly
                                                type="text"
                                                class="mt-1 block w-full px-4 focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded-md" />
                                        </div>

                                        <div class="col-span-4 sm:col-span-2"></div>

                                        <div class="col-span-4 sm:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700"> Rate / Sale Price </label>
                                            <div class="relative">
                                                <input
                                                    v-model="form.rate"
                                                    @change="total"
                                                    @keyup="total"
                                                    placeholder="Rate"
                                                    type="text"
                                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                    autocomplete="off"
                                                    class="mt-1 block w-full px-4 pr-12 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded-md" />
                                                <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                            </div>
                                            <InputError :message="$page.props.errors.rate" />
                                        </div>

                                        <div class="col-span-4 sm:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700"> Discount </label>
                                            <div class="relative">
                                                <input
                                                    v-model="form.discount"
                                                    @change="total"
                                                    @keyup="total"
                                                    placeholder="Discount"
                                                    type="text"
                                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                    autocomplete="off"
                                                    class="mt-1 block w-full px-4 pr-12 focus:ring-indigo-400 focus:border-indigo-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded-md" />
                                                <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                            </div>
                                            <InputError :message="$page.props.errors.discount" />
                                        </div>

                                        <div class="col-span-4 sm:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700"> Total </label>
                                            <div class="relative">
                                                <input
                                                    v-model="form.total"
                                                    placeholder="Total"
                                                    type="text"
                                                    readonly
                                                    class="mt-1 block w-full px-4 pr-12 focus:outline-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded-md" />
                                                <span class="absolute py-2 top-0 right-0 m-px px-4 rounded-r-md border-l border-gray-300 bg-gray-100 sm:text-sm">&#2547;</span>
                                            </div>
                                            <InputError :message="$page.props.errors.total" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-gray-300" /></div>
                                <div class="relative flex justify-center ml-4"><span class="px-3 bg-white text-lg font-medium text-gray-900"></span></div>
                            </div>

                            <div class="max-w-xl mx-auto">
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
    </div>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { onMounted, reactive, ref, watch } from 'vue'

import Alert from '@/Components/Alert.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import InputError from '@/Components/InputError.vue'
import Listbox from '@/Components/Listbox.vue'
import StatusOptions from '@/Components/StatusOptions.vue'

import { PencilSquareIcon } from '@heroicons/vue/24/outline'
import { ArrowPathIcon, PlusIcon, XMarkIcon } from '@heroicons/vue/24/solid'
import { computed } from 'vue'

// layout
defineOptions({ layout: AuthenticatedLayout })

// props
const props = defineProps({
    string_change: Object,
    statuses: Object,
    product: Object,
    products: Array,
    categories: Array,
    items: Array,
    units: Array
})

// state
const is_platter = ref(false)
const initialLoadComplete = ref(false)

const breadcrumbs = [
    { name: props.string_change.product_s, href: route('product.index'), current: false },
    { name: 'Edit Page', href: '#', current: false }
]

const form = reactive({
    id: props.product.data.id,
    code: props.product.data.code,
    name: props.product.data.name,
    english_name: props.product.data.english_name,
    rate: props.product.data.rate,
    status: props.product.data.status,
    discount: props.product.data.discount,
    number_of_persons: props.product.data.number_of_persons,
    total: props.product.data.rate - props.product.data.discount,
    description: props.product.data.description,
    vat_applicable: props.product.data.vat_applicable,
    product_category_id: props.product.data.product_category_id,
    production_cost: props.product.data.production_cost,
    ingredients_for: 1,

    margin_amount: props.product.data.margin_amount,
    margin_percent: props.product.data.margin_percent,

    group_items: props.product.data.group_items.length
        ? props.product.data.group_items
        : [
              {
                  id: '',
                  item_id: '',
                  quantity_use: null,
                  unit_use: null,
                  avg_rate: null
              }
          ],

    platter_items: props.product.data.platter_items?.length
        ? props.product.data.platter_items
        : [
              {
                  item_id: null,
                  quantity: 1
              }
          ],

    image: props.product.data.image,
    image_default: props.product.data.image_default,
    image_removed: false
})

// methods
const onFileInput = (file) => {
    return URL.createObjectURL(file)
}

const removePlatter = (index) => {
    if (confirm('Are you sure you want to delete this element?')) {
        form.platter_items.splice(index, 1)
    }
}

const addPlatter = () => {
    form.platter_items.push({
        item_id: null,
        quantity: 1
    })
}

const removeItem = (index) => {
    if (confirm('Are you sure you want to delete this element?')) {
        form.group_items.splice(index, 1)
    }
}

const addItem = () => {
    form.group_items.push({
        id: '',
        item_id: '',
        quantity_use: null,
        unit_use: null,
        avg_rate: null
    })
}

const total = () => {
    let sale_price = Number(form.rate)
    let discount = Number(form.discount)
    let production_cost = Number(form.production_cost)
    let not_is_platter = !is_platter.value
    let sale_price_gt_zero = sale_price > 0

    form.total = (sale_price - discount).toFixed(2)

    let margin_amount = not_is_platter && sale_price_gt_zero ? (sale_price - production_cost).toFixed(2) : null

    form.margin_amount = margin_amount
    form.margin_percent = not_is_platter && sale_price_gt_zero ? ((margin_amount / sale_price) * 100).toFixed(2) : null
}

const calculation = (index) => {
    if (index != undefined) {
        let group_item = form.group_items[index]
        let selected_item = props.items.find((item) => item.name == group_item.item_name)
        console.log('Initial group_item:', group_item.quantity)

        group_item.item_id = selected_item?.id ?? null
        group_item.avg_rate = selected_item?.avg_rate ?? null
        group_item.unit = selected_item?.unit ?? null

        group_item.unit_use = null
        group_item.quantity_use = 0
        group_item.cost = 0

        if (selected_item && group_item.quantity > 0) {
            switch (group_item.unit) {
                case 'kg':
                    group_item.unit_use = 'g'
                    group_item.quantity_use = ((group_item.quantity * 1000) / form.ingredients_for).toFixed(3)
                    group_item.cost = ((selected_item.avg_rate / 1000) * group_item.quantity_use).toFixed(2)
                    break

                case 'l':
                    group_item.unit_use = 'ml'
                    group_item.quantity_use = ((group_item.quantity * 1000) / form.ingredients_for).toFixed(3)
                    group_item.cost = ((selected_item.avg_rate / 1000) * group_item.quantity_use).toFixed(2)
                    break

                case 'pcs':
                    group_item.unit_use = 'pcs'
                    group_item.quantity_use = group_item.quantity / form.ingredients_for
                    group_item.cost = (selected_item.avg_rate * group_item.quantity_use).toFixed(2)
                    break

                default:
                    group_item.unit_use = null
                    group_item.quantity_use = 0
                    group_item.cost = 0
                    break
            }
        }
    } else {
        form.group_items.forEach((group_item) => {
            let selected_item = props.items.find((item) => item.id == group_item.item_id)

            group_item.cost = selected_item
                ? group_item.unit_use != 'pcs'
                    ? ((selected_item.avg_rate / 1000) * group_item.quantity_use).toFixed(2)
                    : (selected_item.avg_rate * group_item.quantity_use).toFixed(2)
                : null
            let total_quantity =
                group_item.unit_use != 'pcs' ? ((group_item.quantity_use / 1000) * form.ingredients_for).toFixed(3) : (group_item.quantity_use * form.ingredients_for).toFixed(3)
            group_item.total_quantity = total_quantity > 0 ? Number(total_quantity) : null
        })
    }

    form.production_cost = form.group_items.reduce((carry, val) => carry + Number(val.cost || 0), 0)
    form.production_cost = form.production_cost.toFixed(2)
    total()
}

const platterCalculation = (index = undefined) => {
    if (index != undefined) {
        let platter_item = form.platter_items[index]
        let selected_product = props.products.find((product) => product.name == platter_item.item_name)
        platter_item.rate = selected_product?.rate ?? null
        platter_item.item_id = selected_product?.id ?? null

        platter_item.cost = selected_product ? ((platter_item.rate || 0) * (platter_item.quantity || 0)).toFixed(2) : null
    } else {
        form.platter_items.forEach((platter_item) => {
            let selected_product = props.products.find((product) => product.id == platter_item.item_id)
            platter_item.item_name = selected_product?.name ?? null
            platter_item.rate = selected_product?.rate ?? null
            platter_item.item_id = selected_product?.id ?? null
            platter_item.cost = selected_product ? ((selected_product.rate || 0) * (platter_item.quantity || 0)).toFixed(2) : null
        })
    }

    form.production_cost = form.platter_items.reduce((carry, val) => carry + Number(val.cost || 0), 0)
    form.production_cost = form.production_cost.toFixed(2)

    total()
}

const submit = () => {
    router.post(route('product.update', props.product.data.id), form)
}

// lifecycle
onMounted(() => {
    console.log(props.product.is_platter)
    if (props.product.is_platter) {
        platterCalculation()
    } else {
        calculation()
    }
})

// watchers
watch(
    () => form.product_category_id,
    () => {
        let category = props.categories.find((category) => category.id == form.product_category_id)
        is_platter.value = category?.is_platter
    }
)

watch(
    () => form.group_items,
    (newVal) => {
        if (!initialLoadComplete.value) {
            newVal.forEach((item, index) => {
                calculation(index)
            })
            initialLoadComplete.value = true
        }
    },
    { deep: true }
)

// computed
const status_of_product = computed(() => props.product.data.status)
const productId = computed(() => props.product.data.id)
</script>
