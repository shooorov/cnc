<script setup>
import { Link } from '@inertiajs/vue3';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';

import {
    ChevronUpIcon,
    ChevronUpDownIcon,
} from '@heroicons/vue/24/solid';

const props = defineProps({
	as: String,
	href: String,
	status: String,
	items: Object,
	position : {
		type: String,
		default: 'bottom',
	}
})

const statusStyles = {
	inactive: 'text-white bg-rose-500 border-rose-500 hover:bg-rose-600 focus:ring-rose-400',
	draft: 'text-white bg-gray-500 border-gray-500 hover:bg-gray-600 focus:ring-gray-400',
	pending: 'text-white bg-yellow-500 border-yellow-500 hover:bg-yellow-600 focus:ring-yellow-400',
	ready: 'text-white bg-primary-500 border-primary-500 hover:bg-primary-600 focus:ring-primary-400',
	active: 'text-white bg-green-500 border-green-500 hover:bg-green-600 focus:ring-green-400',
	accepted: 'text-white bg-green-500 border-green-500 hover:bg-green-600 focus:ring-green-400',
	done: 'text-white bg-primary-500 border-primary-500 hover:bg-primary-600 focus:ring-primary-400',
	received: 'text-white bg-primary-500 border-primary-500 hover:bg-primary-600 focus:ring-primary-400',
	sent: 'text-white bg-red-500 border-red-500 hover:bg-red-600 focus:ring-red-400',
	approved: 'text-white bg-green-500 border-green-500 hover:bg-green-600 focus:ring-green-400',
	delivered: 'text-white bg-gray-500 border-gray-500 hover:bg-gray-600 focus:ring-gray-400',
	processing: 'text-white bg-yellow-500 border-yellow-500 hover:bg-yellow-600 focus:ring-yellow-400',
	served: 'text-white bg-gray-500 border-gray-500 hover:bg-gray-600 focus:ring-gray-400',
	completed: 'text-white bg-primary-500 border-primary-500 hover:bg-primary-600 focus:ring-primary-400',
	accept: 'text-white bg-blue-500 border-blue-500 hover:bg-blue-600 focus:ring-blue-400',
	unpaid: 'text-white bg-yellow-500 border-yellow-500 hover:bg-yellow-600 focus:ring-yellow-400',
	archived: 'text-white bg-rose-500 border-rose-500 hover:bg-rose-600 focus:ring-rose-400',
	paid: 'text-white bg-green-500 border-green-500 hover:bg-green-600 focus:ring-green-400',
};

</script>
<template>
    <Menu as="div" class="relative inline-block">
        <div>
            <MenuButton :class="[statusStyles[status], as == 'button' ? 'shadow-sm text-sm px-4 py-2 focus:ring-2 focus:ring-offset-2' : 'text-sm px-2 py-1 focus:ring-1 focus:ring-offset-1', 'inline-flex justify-center w-full rounded-md border font-medium focus:outline-none']">
                <!-- {{ items[status] }} -->
				{{ items[status] }}

                <ChevronUpIcon v-if="position == 'top'" class="-mr-1 ml-2 h-5 w-5" aria-hidden="true" />
                <ChevronUpDownIcon v-else class="-mr-1 ml-2 h-5 w-5" aria-hidden="true" />
            </MenuButton>
        </div>

        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
            <MenuItems :class="[position == 'top' ? 'bottom-10 mb-2 left-0' : 'mt-2 right-0', 'origin-top-right absolute w-40 rounded-md shadow-lg bg-white z-10 ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none']">
                <div class="py-1">
                    <MenuItem v-for="(title, item) in items" :key="item">
                        <Link :href="href+ '/' + item" method="patch" as="button" class="text-gray-700 group flex items-center w-full px-4 py-2 text-sm hover:bg-gray-100">
                            {{ title }}
                        </Link>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>