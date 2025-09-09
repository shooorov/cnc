<script setup>
import { reactive, ref } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'

import { Dialog, DialogOverlay, Menu, MenuButton, MenuItem, MenuItems, TransitionChild, TransitionRoot } from '@headlessui/vue'

import {
    AdjustmentsVerticalIcon,
    BriefcaseIcon,
    BookmarkSquareIcon,
    CurrencyBangladeshiIcon,
    NewspaperIcon,
    CreditCardIcon,
    CircleStackIcon,
    PresentationChartBarIcon,
    HomeIcon,
    Bars3CenterLeftIcon,
    ShoppingCartIcon,
    ShoppingBagIcon,
    TruckIcon,
    ShieldExclamationIcon,
    XMarkIcon,
    ChevronDownIcon,
    ChevronUpDownIcon
} from '@heroicons/vue/24/outline'

const page = usePage()

const branchForm = useForm({
    branch_id: page.props.branch?.id
})

const sidebarOpen = ref(false)

const setBranch = (id) => {
    branchForm.branch_id = id
	page.props.alertMessage = {};

    branchForm.post(route('index'), {
        onFinish: () => {}
    })
}

const menuIcons = reactive({
    Dashboard: HomeIcon,
    Reporting: PresentationChartBarIcon,
    POS: ShoppingCartIcon,
    Kitchen: ShoppingBagIcon,
    'Order Bill': BriefcaseIcon,
    Delivery: TruckIcon,
    product_inventory: CircleStackIcon,
    item_inventory: CircleStackIcon,
    Event: BookmarkSquareIcon,
    'Other Sale': CreditCardIcon,
    Expense: CurrencyBangladeshiIcon,
    Requisition: NewspaperIcon,
    'Product Requisition': NewspaperIcon,
    'Kitchen Delivery': NewspaperIcon,
    Manage: ShieldExclamationIcon,
    System: AdjustmentsVerticalIcon
})

function menuIcon(name) {
    return menuIcons[name]
}
</script>
<template>
    <div class="relative h-screen flex overflow-hidden bg-gray-100">
        <!-- Static sidebar for mobile -->
        <TransitionRoot as="template" :show="sidebarOpen">
            <Dialog as="div" class="fixed inset-0 flex z-40 lg:hidden" @close="sidebarOpen = false">
                <TransitionChild
                    as="template"
                    enter="transition-opacity ease-linear duration-300"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="transition-opacity ease-linear duration-300"
                    leave-from="opacity-100"
                    leave-to="opacity-0">
                    <DialogOverlay class="fixed inset-0 bg-gray-600 bg-opacity-75" />
                </TransitionChild>

                <TransitionChild
                    as="template"
                    enter="transition ease-in-out duration-300 transform"
                    enter-from="-translate-x-full"
                    enter-to="translate-x-0"
                    leave="transition ease-in-out duration-300 transform"
                    leave-from="translate-x-0"
                    leave-to="-translate-x-full">
                    <div class="relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-primary-700">
                        <TransitionChild
                            as="template"
                            enter="ease-in-out duration-300"
                            enter-from="opacity-0"
                            enter-to="opacity-100"
                            leave="ease-in-out duration-300"
                            leave-from="opacity-100"
                            leave-to="opacity-0">
                            <div class="absolute top-0 right-0 -mr-12 pt-2">
                                <button
                                    type="button"
                                    class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                                    @click="sidebarOpen = false">
                                    <span class="sr-only">Close sidebar</span>
                                    <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
                                </button>
                            </div>
                        </TransitionChild>

                        <Link :href="route('index')" class="shrink-0 flex items-center px-4">
                            <img v-if="page.props.app.logo" class="h-16 w-auto" :src="page.props.app.logo" alt="logo" />
                            <span v-else class="text-secondary-500 px-3 text-xl font-fancy leading-6 font-medium">{{ page.props.app.name }}</span>
                        </Link>

                        <nav class="mt-5 flex-1 flex flex-col divide-y divide-primary-800 overflow-y-auto" aria-label="Sidebar">
                            <div
                                v-for="(sub_menu, index) in page.props.navigation.menu"
                                :key="index"
                                v-show="sub_menu.visible"
                                :class="{ 'mt-6 pt-6': index > 0 }">
                                <div class="px-2 space-y-1">
                                    <Menu
                                        as="div"
                                        v-slot="{ open }"
                                        class="relative"
                                        v-for="(heading, key) in sub_menu.items"
                                        :key="key"
                                        v-show="heading.visible">
                                        <Link
                                            v-if="!heading.items && heading.visible"
                                            :href="route(heading.route_name)"
                                            :class="[
                                                heading.current ? 'bg-primary-800 text-white' : 'text-primary-100 hover:text-white hover:bg-primary-600',
                                                'group flex items-center px-2 py-2 text-sm leading-6 font-medium rounded-md'
                                            ]"
                                            aria-current="page">
                                            <component
                                                :is="menuIcon(heading.name)"
                                                class="mr-4 shrink-0 h-6 w-6 text-primary-200"
                                                aria-hidden="true"></component>
                                            {{ page.props.string_change[heading.name] ?? heading.name }}
                                        </Link>

                                        <div v-if="heading.items">
                                            <MenuButton
                                                :class="[
                                                    heading.is_open ? 'bg-primary-800 text-white' : 'text-primary-100 hover:text-white hover:bg-primary-600',
                                                    'group flex items-start text-left w-full px-2 py-2 text-sm leading-6 font-medium rounded-md'
                                                ]">
                                                <component
                                                    :is="menuIcon(heading.name)"
                                                    class="mr-4 shrink-0 h-6 w-6 text-primary-200"
                                                    aria-hidden="true"></component>
                                                <span class="flex-1">{{ page.props.string_change[heading.name] ?? heading.name }}</span>
                                                <ChevronDownIcon
                                                    :class="[open || heading.is_open ? 'rotate-0' : 'rotate-90', 'transform shrink-0 ml-1 h-5 w-5']"
                                                    aria-hidden="true" />
                                            </MenuButton>

                                            <transition
                                                enter-active-class="transition ease-out duration-100"
                                                enter-from-class="transform opacity-0 scale-95"
                                                enter-to-class="transform opacity-100 scale-100"
                                                leave-active-class="transition ease-in duration-75"
                                                leave-from-class="transform opacity-100 scale-100"
                                                leave-to-class="transform opacity-0 scale-95">
                                                <MenuItems :static="heading.is_open" class="py-1">
                                                    <MenuItem v-for="(sub_heading, sub_key) in heading.items" :key="sub_key" v-show="sub_heading.visible">
                                                        <Link
                                                            :href="route(sub_heading.route_name)"
                                                            :class="[
                                                                sub_heading.current
                                                                    ? 'bg-primary-800 text-white'
                                                                    : 'text-primary-100 hover:text-white hover:bg-primary-600',
                                                                'text-white hover:text-white hover:bg-primary-700  group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium rounded-md'
                                                            ]">
                                                            {{ page.props.string_change[sub_heading.name] ?? sub_heading.name }}
                                                        </Link>
                                                    </MenuItem>
                                                </MenuItems>
                                            </transition>
                                        </div>
                                    </Menu>
                                </div>
                            </div>
                        </nav>
                    </div>
                </TransitionChild>

                <div class="shrink-0 w-14" aria-hidden="true">
                    <!-- Dummy element to force sidebar to shrink to fit close icon -->
                </div>
            </Dialog>
        </TransitionRoot>

        <!-- Static sidebar for desktop -->
        <div class="hidden lg:flex lg:shrink-0">
            <div class="flex flex-col w-64">
                <!-- Sidebar component, swap this element with another sidebar if you like -->
                <div class="flex flex-col flex-grow bg-primary-700 pt-5 pb-4 overflow-y-auto">
                    <Link :href="route('index')" class="flex justify-center items-center shrink-0 px-5">
                        <img v-if="page.props.app.logo" class="h-16 w-auto" :src="page.props.app.logo" alt="logo" />
                        <span v-else class="text-secondary-500 px-3 text-xl font-fancy leading-6 font-medium">{{ page.props.app.name }}</span>
                    </Link>
                    <nav class="flex-1 px-2 py-4 flex flex-col divide-y divide-primary-800 overflow-y-auto" aria-label="Sidebar">
                        <div v-for="(sub_menu, index) in page.props.navigation.menu" :key="index" v-show="sub_menu.visible" :class="{ 'mt-6 pt-6': index > 0 }">
                            <div class="space-y-1">
                                <Menu as="div" v-slot="{ open }" v-for="(heading, key) in sub_menu.items" :key="key" v-show="heading.visible" class="relative">
                                    <Link
                                        v-if="!heading.items && heading.visible"
                                        :href="route(heading.route_name)"
                                        :class="[
                                            heading.current ? 'bg-primary-800 text-white' : 'text-primary-100 hover:text-white hover:bg-primary-600',
                                            'group flex items-center px-2 py-2 text-sm leading-6 font-medium rounded-md'
                                        ]"
                                        aria-current="page">
                                        <component :is="menuIcon(heading.name)" class="mr-4 shrink-0 h-6 w-6 text-primary-200" aria-hidden="true"></component>
                                        {{ page.props.string_change[heading.name] ?? heading.name }}
                                    </Link>

                                    <div v-if="heading.items">
                                        <MenuButton
                                            :class="[
                                                heading.is_open
                                                    ? 'bg-primary-800 text-white'
                                                    : 'text-primary-100 hover:text-white hover:bg-primary-600 border-transparent',
                                                'group flex items-start text-left w-full px-2 py-2 text-sm leading-6 font-medium rounded-md'
                                            ]">
                                            <component
                                                :is="menuIcon(heading.name)"
                                                class="mr-4 shrink-0 h-6 w-6 text-primary-200"
                                                aria-hidden="true"></component>
                                            <span class="flex-1">{{ page.props.string_change[heading.name] ?? heading.name }}</span>
                                            <ChevronDownIcon
                                                :class="[
                                                    open || heading.is_open ? 'rotate-0' : 'rotate-90',
                                                    'transition duration-150 ease-out transform shrink-0 ml-1 h-5 w-5'
                                                ]"
                                                aria-hidden="true" />
                                        </MenuButton>

                                        <transition
                                            enter-active-class="transition ease-out duration-100"
                                            enter-from-class="transform opacity-0 scale-95"
                                            enter-to-class="transform opacity-100 scale-100"
                                            leave-active-class="transition ease-in duration-75"
                                            leave-from-class="transform opacity-100 scale-100"
                                            leave-to-class="transform opacity-0 scale-95">
                                            <MenuItems :static="heading.is_open" class="py-1">
                                                <MenuItem v-for="(sub_heading, sub_key) in heading.items" :key="sub_key" v-show="sub_heading.visible">
                                                    <Link
                                                        :href="route(sub_heading.route_name)"
                                                        :class="[
                                                            sub_heading.current
                                                                ? 'bg-primary-800 text-white'
                                                                : 'text-primary-100 hover:text-white hover:bg-primary-600',
                                                            'group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium rounded-md'
                                                        ]">
                                                        {{ sub_heading.name }}
                                                    </Link>
                                                </MenuItem>
                                            </MenuItems>
                                        </transition>
                                    </div>
                                </Menu>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-auto focus:outline-none">
            <!-- z-10 -->
            <div class="relative shrink-0 flex h-16 bg-white border-b border-gray-200 lg:border-none">
                <button
                    type="button"
                    class="px-4 border-r border-gray-200 text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500 lg:hidden"
                    @click="sidebarOpen = true">
                    <span class="sr-only">Open sidebar</span>
                    <Bars3CenterLeftIcon class="h-6 w-6" aria-hidden="true" />
                </button>
                <!-- Search bar -->
                <div class="flex-1 px-4 flex justify-between sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                    <div class="flex-1 flex">
                        <div class="w-full flex md:ml-0">
                            <div class="relative w-full text-gray-400 focus-within:text-gray-600 py-4 lg:py-3.5 space-x-1 lg:space-x-3">
                                <Menu as="div" class="relative inline-block">
                                    <div>
                                        <MenuButton
                                            class="max-w-xs bg-white rounded-md border border-gray-300 flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 py-1 lg:p-2 lg:rounded-md lg:hover:bg-gray-50">
                                            <span class="ml-2 text-gray-700 text-xs lg:text-sm font-medium lg:block"
                                                ><span class="sr-only">Open branch menu for </span>{{ page.props.branch?.name ?? 'All Branch' }}</span
                                            >
                                            <ChevronUpDownIcon class="shrink-0 ml-1 h-5 w-5 text-gray-400 lg:block" aria-hidden="true" />
                                        </MenuButton>
                                    </div>
                                    <transition
                                        enter-active-class="transition ease-out duration-100"
                                        enter-from-class="transform opacity-0 scale-95"
                                        enter-to-class="transform opacity-100 scale-100"
                                        leave-active-class="transition ease-in duration-75"
                                        leave-from-class="transform opacity-100 scale-100"
                                        leave-to-class="transform opacity-0 scale-95">
                                        <MenuItems
                                            class="z-10 origin-top-left absolute left-0 mt-2 w-48 lg:w-64 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                                            <MenuItem v-if="page.props.branches.length > 1" v-slot="{ active, close }">
                                                <button
                                                    type="button"
                                                    @click=";[close, setBranch(null)]"
                                                    :class="[
                                                        active || !branchForm.branch_id ? 'bg-gray-100' : '',
                                                        'block px-4 py-2 text-sm text-gray-700 w-full text-left'
                                                    ]">
                                                    All Branch
                                                </button>
                                            </MenuItem>

                                            <MenuItem v-for="branch_item in page.props.branches" :key="branch_item.id" v-slot="{ active, close }">
                                                <button
                                                    type="button"
                                                    @click=";[close, setBranch(branch_item.id)]"
                                                    :class="[
                                                        active || branch_item.id == branchForm.branch_id ? 'bg-gray-100' : '',
                                                        'block px-4 py-2 text-sm text-gray-700 w-full text-left'
                                                    ]">
                                                    {{ branch_item.name }}
                                                </button>
                                            </MenuItem>
                                        </MenuItems>
                                    </transition>
                                </Menu>
                            </div>
                        </div>
                    </div>
                    <div class="ml-4 flex items-center md:ml-6">
                        <!-- <button type="button" class="bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <span class="sr-only">View notifications</span>
                            <BellIcon class="h-6 w-6" aria-hidden="true" />
                        </button> -->

                        <!-- Profile dropdown -->
                        <Menu as="div" class="ml-3 relative">
                            <div>
                                <MenuButton
                                    class="max-w-xs bg-white rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 lg:p-2 lg:rounded-md lg:hover:bg-gray-50">
                                    <img
                                        class="object-cover h-8 w-8 rounded-full"
                                        :src="page.props.auth.user?.image_url"
                                        alt="Image path" />
                                    <span class="hidden ml-3 text-gray-700 text-sm font-medium lg:block"
                                        ><span class="sr-only">Open user menu for </span>{{ page.props.auth.user?.name }}</span
                                    >
                                    <ChevronDownIcon class="hidden shrink-0 ml-1 h-5 w-5 text-gray-400 lg:block" aria-hidden="true" />
                                </MenuButton>
                            </div>
                            <transition
                                enter-active-class="transition ease-out duration-100"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95">
                                <MenuItems
                                    class="absolute right-0 mt-2 w-48 z-10 origin-top-right divide-y divide-gray-100 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <div class="px-1 py-1">
                                        <MenuItem v-slot="{ active, close }">
                                            <Link
                                                :href="route('profile.index')"
                                                @click="close"
                                                :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700 w-full text-left']">
                                                Your Account
                                            </Link>
                                        </MenuItem>
                                        <MenuItem v-slot="{ active, close }">
                                            <Link
                                                :href="route('profile.password')"
                                                @click="close"
                                                :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700 w-full text-left']">
                                                Change Password
                                            </Link>
                                        </MenuItem>
                                        <MenuItem v-show="page.props.navigation.routes.includes('branch.access')" v-slot="{ active, close }">
                                            <Link
                                                :href="route('branch.access')"
                                                @click="close"
                                                :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700 w-full text-left']">
                                                User Branch Access
                                            </Link>
                                        </MenuItem>
                                        <MenuItem v-show="page.props.navigation.routes.includes('product_category.specification')" v-slot="{ active, close }">
                                            <Link
                                                :href="route('product_category.specification')"
                                                @click="close"
                                                :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700 w-full text-left']">
                                                Recipe Specification
                                            </Link>
                                        </MenuItem>
                                        <MenuItem v-show="page.props.navigation.routes.includes('setting.edit')" v-slot="{ active, close }">
                                            <Link
                                                :href="route('setting.edit')"
                                                @click="close"
                                                :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700 w-full text-left']">
                                                Setting
                                            </Link>
                                        </MenuItem>

                                        <MenuItem v-slot="{ active, close }">
                                            <Link
                                                :href="route('logout')"
                                                method="post"
                                                as="button"
                                                @click="close"
                                                :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700 w-full text-left']">
                                                Logout
                                            </Link>
                                        </MenuItem>
                                    </div>
                                    <div class="px-1 py-1">
                                        <MenuItem v-show="page.props.auth.user.is_admin" v-slot="{ active, close }">
                                            <a
                                                :href="route('log-viewer.index')"
                                                target="_blank"
                                                @click="close"
                                                :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700 w-full text-left']">
                                                System Log
                                            </a>
                                        </MenuItem>
                                    </div>
                                </MenuItems>
                            </transition>
                        </Menu>
                    </div>
                </div>
            </div>
            <main class="flex-1 relative pb-8 z-0 overflow-y-auto">
                <slot />
            </main>
        </div>
    </div>
</template>
