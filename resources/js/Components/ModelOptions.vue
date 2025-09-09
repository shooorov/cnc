<template>
    <Menu as="div" class="ml-3 relative inline-block text-left">
        <div>
            <MenuButton class="p-2 rounded-full flex items-center border text-gray-400 hover:text-gray-600">
                <span class="sr-only">Open options</span>
                <EllipsisVerticalIcon class="h-5 w-5" aria-hidden="true" />
            </MenuButton>
        </div>

        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
            <MenuItems class="origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-10">
                <div class="py-1">
                    <MenuItem>
                        <Link :href="detailHref?? '#'" :class="[!detailHref ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'group flex items-center px-4 py-2 text-sm hover:bg-gray-100']">
                            <ArrowTopRightOnSquareIcon :class="['mr-3 h-5 w-5 text-gray-400', !detailHref? '' : 'group-hover:text-gray-500']" aria-hidden="true" />
                            Detail
                        </Link>
                    </MenuItem>
                    <MenuItem>
                        <Link :href="editHref?? '#'" :class="[!editHref ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'group flex items-center px-4 py-2 text-sm hover:bg-gray-100']">
                            <PencilSquareIcon :class="['mr-3 h-5 w-5 text-gray-400', !editHref? '' : 'group-hover:text-gray-500']" aria-hidden="true" />
                            Edit
                        </Link>
                    </MenuItem>
                </div>
                <div v-if="deleteHref" class="py-1">
                    <MenuItem>
                        <button type="button" @click="deleteRecord" :class="[!deleteHref ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'group flex items-center px-4 py-2 text-sm hover:bg-gray-100 w-full']">
                            <TrashIcon :class="['mr-3 h-5 w-5 text-gray-400', !deleteHref? '' : 'group-hover:text-gray-500']" aria-hidden="true" />
                            Delete
                        </button>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>

</template>

<script>
import { router, Link } from '@inertiajs/vue3';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';

import {
    ChevronUpDownIcon,
    EllipsisVerticalIcon,
    TrashIcon,
} from '@heroicons/vue/24/solid';

import {
    ArrowTopRightOnSquareIcon,
    PencilSquareIcon,
} from '@heroicons/vue/24/outline';

export default {
    components: {
        Link,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        ChevronUpDownIcon,
        EllipsisVerticalIcon,
        ArrowTopRightOnSquareIcon,
        PencilSquareIcon,
        TrashIcon,
    },

    props: ['detailHref', 'editHref', 'deleteHref', 'deleteMessage'],

    methods: {
        deleteRecord() {
            if(confirm(this.deleteMessage)) {
                router.delete(this.deleteHref);
            }
        }
    },

}
</script>
