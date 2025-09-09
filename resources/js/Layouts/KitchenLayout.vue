<script setup>
import { onMounted, onUpdated, reactive, ref } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { Bars3Icon, ChevronDownIcon, ShoppingCartIcon, BellIcon, XMarkIcon, ClockIcon } from '@heroicons/vue/24/outline'

const page = usePage()

const form = useForm({
    status: page.props.kitchen ? 'stop' : 'start'
})

const selected = reactive(page.props.order_productions.length > 0 ? page.props.order_productions[0] : { order_id: null })

const clockStartTime = ref(page.props.kitchen?.started_at)
const clockTimer = ref(null)
const isClocking = ref(false)
const isCook = ref(page.props.auth.user.is_cook)

onMounted(() => {
    countUp()
})

const kitchenInterval = ref(null)
kitchenInterval.value = setInterval(function () {
    window.location.reload()
}, 1 * 60 * 1000)

const clockSwitch = () => {
	page.props.alertMessage = {};

    form.post(route('kitchen.clock'), {
        onSuccess: () => {
            console.log(form)
            isClocking.value = false
            clockTimer.value = ''

            if (form.status == 'stop') {
                clockStartTime.value = null
                form.status = 'start'
            } else {
                clockStartTime.value = page.props.kitchen?.started_at
                form.status = 'stop'
            }
        },
        onFinish: () => {
            countUp()
        }
    })
}

function countUp() {
    console.log('count up')

    if (clockStartTime.value) {
        isClocking.value = setInterval(function () {
            clockTimer.value = getClockTimer(new Date(clockStartTime.value).getTime())
        }, 1000)
    } else {
        if (isClocking.value) {
            clearInterval(isClocking.value)
        }
    }
}

const getClockTimer = (startTime) => {
    var timeDifference = new Date().getTime() - startTime
    let days = Math.floor(timeDifference / (1000 * 60 * 60 * 24))
    let hours = Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
    let minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60))
    let seconds = Math.floor((timeDifference % (1000 * 60)) / 1000)

    let time = ''

    if (days) {
        time += days + "<span class='mr-2 text-xs text-gray-700 uppercase'>days</span>"
    }
    if (hours) {
        time += hours + "<span class='mr-2 text-xs text-gray-700 uppercase'>hrs</span>"
    }
    if (minutes) {
        time += minutes + "<span class='mr-2 text-xs text-gray-700 uppercase'>mins</span>"
    }
    if (seconds) {
        time += seconds + "<span class='mr-2 text-xs text-gray-700 uppercase'>secs</span>"
    }
    return time
}

const colors = {
    pending: {
        text: 'Pending',
        switchClass: 'bg-yellow-200 text-yellow-800 border-yellow-300'
    },
    view: {
        text: 'View',
        switchClass: 'bg-green-200 text-yellow-800 border-green-300'
    },
    accept: {
        text: 'Cooking',
        switchClass: 'bg-blue-200 text-yellow-800 border-blue-300'
    },
    complete: {
        text: 'Done',
        switchClass: 'bg-yellow-200 text-yellow-800 border-yellow-300'
    }
}

const userNavigation = [
    { name: 'Kitchen Clock Logs', href: route('kitchen.index') },
    { name: 'Yesterday Kitchen', href: route('production.index', { date: 'yesterday' }) },
    // { name: 'Your Account', href: route('profile.index') },
    // { name: 'Change Password', href: route('profile.password') },
    { name: 'Logout', href: route('logout'), as: 'button', method: 'POST' }
]
</script>
<template>
    <div class="min-h-full h-screen">
        <!-- Navbar -->
        <Disclosure as="nav" class="bg-yellow-50 border-b border-yellow-100" v-slot="{ open }">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="relative flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <Link href="/" class="flex-shrink-0">
                            <img v-if="page.props.app.logo" class="h-16 w-auto" :src="page.props.app.logo" alt="logo" />
                            <span v-else class="text-secondary-500 px-3 text-xl font-fancy leading-6 font-medium">{{ page.props.app.name }}</span>
                        </Link>

                        <Link
                            v-if="page.props.navigation.routes.includes('pos.create')"
                            :href="route('pos.create')"
                            class="ml-6 inline-flex items-center rounded border border-transparent bg-yellow-500 px-4 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">
                            <ShoppingCartIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                            POS
                        </Link>

                        <!-- Links section -->
                        <!-- <div class="hidden lg:ml-10 lg:flex">
                            <div class="flex items-center space-x-4">
                                <h3 class="px-3 py-2 rounded-md text-lg leading-7 font-medium text-slate-700 space-x-3">
                                    <span class="uppercase">Target:</span><span>{{ target_time }}</span>
                                </h3>
                                <h4
                                    class="inline-flex items-center justify-center border border-dashed border-slate-500 px-3 py-1 text-base font-medium text-slate-700 space-x-3">
                                    <span class="uppercase">Avg Time:</span><span>{{ page.props.today_average_time }}</span>
                                </h4>
                                <h4 class="inline-flex items-center justify-center text-lg font-medium text-slate-900 capitalize">
                                    {{ page.props.auth.user.role_name }}
                                </h4>
                            </div>

                            <div class="flex space-x-4">
                                <a
                                    v-for="item in navigation"
                                    :key="item.name"
                                    :href="item.href"
                                    :class="[item.current ? 'bg-slate-100' : 'hover:text-slate-700', 'px-3 py-2 rounded-md text-sm font-medium text-slate-900']"
                                    :aria-current="item.current ? 'page' : undefined">
                                    {{ item.name }}
                                </a>
                            </div>
                        </div> -->
                    </div>

                    <div class="flex flex-1 justify-center px-2 lg:ml-6 lg:justify-end">
                        <!-- <div v-for="(quantity, status) in production_statuses" :key="status" class="">
                            <div
                                :class="[
                                    colors[status].switchClass,
                                    'relative ml-3 h-9 flex items-center justify-center border border-r-0 rounded-full font-medium space-x-2 cursor-default'
                                ]">
                                <span class="ml-3 text-sm uppercase">{{ colors[status].text }}</span>
                                <div class="w-9 h-9 flex items-center justify-center rounded-full bg-white border border-inherit">
                                    <span class="">{{ quantity }}</span>
                                </div>
                            </div>
                        </div> -->

                        <div v-if="isClocking" class="flex items-center space-x-2">
                            <span class="sr-only">Kitchen Running</span>
                            <p class="truncate text-lg font-medium text-red-500" v-html="clockTimer ?? '&nbsp;'"></p>
                        </div>
                        <button
                            type="button"
                            v-show="isCook"
                            @click="clockSwitch"
                            :class="[
                                'inline-flex items-center rounded border border-transparent px-4 py-1.5 text-sm font-medium text-white shadow-sm  focus:outline-none focus:ring-2  focus:ring-offset-2',
                                isClocking ? 'bg-red-500 hover:bg-red-700 focus:ring-red-500' : 'bg-green-500 hover:bg-green-700 focus:ring-green-500'
                            ]">
                            <ClockIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                            {{ isClocking ? 'OFF' : 'ON' }}
                        </button>
                    </div>

                    <div class="flex lg:hidden">
                        <!-- Mobile menu button -->
                        <DisclosureButton
                            class="inline-flex items-center justify-center rounded-md bg-yellow-50 p-2 text-yellow-400 hover:bg-yellow-100 hover:text-yellow-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-yellow-50">
                            <span class="sr-only">Open main menu</span>
                            <Bars3Icon v-if="!open" class="block h-6 w-6" aria-hidden="true" />
                            <XMarkIcon v-else class="block h-6 w-6" aria-hidden="true" />
                        </DisclosureButton>
                    </div>

                    <!-- Actions section -->
                    <div class="hidden lg:ml-4 lg:block">
                        <div class="flex items-center">
                            <!-- Profile dropdown -->
                            <Menu as="div" class="relative ml-3 flex-shrink-0">
                                <div>
                                    <MenuButton
                                        class="max-w-xs bg-inherit rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 lg:p-2 lg:rounded-md">
                                        <img
                                            class="object-cover h-8 w-8 rounded-full"
                                            :src="page.props.auth.user.image_url"
                                            alt="Image path" />
                                        <span class="hidden ml-3 text-gray-700 text-sm font-medium lg:block">
                                            <span class="sr-only">Open user menu for </span> {{ page.props.auth.user.name }}
                                        </span>
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
                                        class="absolute right-0 z-20 mt-2 w-64 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                        <div class="mx-4 mt-3 px-3 py-1 flex items-center justify-between border border-dashed border-slate-400 space-x-2">
                                            <p class="text-sm">Avg Target</p>
                                            <p class="truncate">{{ page.props.average_time }}</p>
                                        </div>
                                        <div class="py-1">
                                            <MenuItem v-for="item in userNavigation" :key="item.name" v-slot="{ active }">
                                                <Link
                                                    :href="item.href"
                                                    :as="item.as"
                                                    :method="item.method"
                                                    :class="[active ? 'bg-slate-100' : '', 'block w-full text-left py-2 px-4 text-sm text-slate-700']">
                                                    {{ item.name }}
                                                </Link>
                                            </MenuItem>
                                        </div>
                                    </MenuItems>
                                </transition>
                            </Menu>
                        </div>
                    </div>
                </div>
            </div>

            <DisclosurePanel class="border-b border-slate-200 bg-slate-50 lg:hidden">
                <!-- <div class="space-y-1 px-2 pt-2 pb-3">
                    <DisclosureButton
                        v-for="item in navigation"
                        :key="item.name"
                        as="a"
                        :href="item.href"
                        :class="[item.current ? 'bg-slate-100' : 'hover:bg-slate-100', 'block px-3 py-2 rounded-md font-medium text-slate-900']"
                        :aria-current="item.current ? 'page' : undefined">
                        {{ item.name }}
                    </DisclosureButton>
                </div> -->
                <div class="border-t border-slate-200 pt-4 pb-3">
                    <div class="flex items-center px-5">
                        <div class="flex-shrink-0">
                            <img class="h-10 w-10 rounded-full" :src="page.props.auth.user.image_url" alt="" />
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-slate-800">
                                {{ page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-slate-500">
                                {{ page.props.auth.user.email }}
                            </div>
                        </div>
                        <button
                            type="button"
                            class="ml-auto flex-shrink-0 rounded-full bg-slate-50 p-1 text-slate-400 hover:text-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-slate-50">
                            <span class="sr-only">View notifications</span>
                            <BellIcon class="h-6 w-6" aria-hidden="true" />
                        </button>
                    </div>
                    <div class="mx-4 mt-3 px-3 py-1 flex items-center justify-between border border-dashed border-slate-400 space-x-2">
                        <p class="text-sm">Avg Target</p>
                        <p class="truncate">{{ page.props.average_time }}</p>
                    </div>

                    <div class="py-1">
                        <Link
                            v-for="item in userNavigation"
                            :key="item.name"
                            :href="item.href"
                            :as="item.as"
                            :method="item.method"
                            :class="[active ? 'bg-slate-100' : '', 'block w-full text-left py-2 px-4 text-sm text-slate-700']">
                            {{ item.name }}
                        </Link>
                    </div>
                    <!-- <div class="mt-3 space-y-1 px-2">
                        <DisclosureButton
                            v-for="item in userNavigation"
                            :key="item.name"
                            :as="item.as"
                            :method="item.method"
                            :href="item.href"
                            class="block rounded-md py-2 px-3 text-base font-medium text-slate-900 hover:bg-slate-100">
                            {{ item.name }}
                        </DisclosureButton>
                    </div> -->
                </div>
            </DisclosurePanel>
        </Disclosure>

        <header class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="relative z-10 flex py-3 sm:h-16 flex-shrink-0 bg-white overflow-x-auto">
                <div class="px-2 w-full flex flex-row sm:items-center">
                    <ul role="list" class="relative w-full max-h-40 flex gap-2.5 sm:flex-wrap z-0 overflow-x-auto sm:overflow-y-auto px-1 py-3">
                        <li v-for="(pending_order, index) in page.props.pending_orders" :key="index">
                            <div class="flex-1 min-w-0 flex justify-center items-center">
                                <Link
                                    :href="route('production.index', { order_id: pending_order.id })"
                                    :class="[
                                        'inline-flex items-center gap-x-1.5 rounded-md bg-indigo-50 px-2 py-1.5 text-sm font-medium text-indigo-600 shadow-sm hover:bg-indigo-100 border',
                                        pending_order.status == 'pending'
                                            ? 'bg-yellow-100 border-yellow-200 text-yellow-900 hover:bg-yellow-200'
                                            : 'border-indigo-200',
                                        selected.order_id == pending_order.id ? 'ring-2 ring-secondary-500 ring-offset-2' : ''
                                    ]">
                                    <p :class="['text-sm leading-5 font-medium capitalize']">
                                        {{ [pending_order.invoice_number.substr(6), pending_order.table_name].filter((n) => n).join(' - ') }}
                                    </p>
                                </Link>
                                <Link
                                    v-show="selected.order_id == pending_order.id"
                                    :href="route('production.index')"
                                    class="ml-3 focus:ring-2 focus:ring-offset-1 focus:ring-orange-500">
                                    <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                                </Link>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="">
            <slot />
        </main>
    </div>
</template>
