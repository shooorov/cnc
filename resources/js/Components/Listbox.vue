<script setup>
import { computed, ref, watch } from 'vue'
import { Listbox, ListboxLabel, ListboxButton, ListboxOptions, ListboxOption } from '@headlessui/vue'

import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
    placeholder: {
        type: String,
        default: 'Select ...'
    },
    items: {
        type: Array,
        default: []
    },
    modelValue: [String, Number, Object, Boolean],
    position: {
        default: 'bottom'
    },
    checkPosition: {
        default: 'right'
    },
    hideIcon: {
        type: Boolean,
        default: false
    }
})
const selectedItem = ref(props.items.find((item) => props.modelValue == item.id) ?? '')

const emit = defineEmits(['update:modelValue'])

watch(
    () => selectedItem.value,
    (newValue) => {
        emit('update:modelValue', newValue.id)
    }
)

emit('update:modelValue', selectedItem.value.id)
</script>
<template>
    <Listbox v-model="selectedItem">
        <div class="relative w-full">
            <ListboxButton
                class="w-full text-left rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:text-sm">
                <span class="block truncate">{{ selectedItem.name ?? props.placeholder }} </span>
                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                    <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                </span>
            </ListboxButton>

            <transition leave-active-class="transition duration-100 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <ListboxOptions
                    :class="[
                        position == 'top' ? 'bottom-10 mb-1' : 'mt-1',
                        'absolute z-10 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm'
                    ]">
                    <ListboxOption v-slot="{ active, selected }" v-for="item in items" :key="item.name" :value="item" as="template">
                        <li
                            :class="[
                                active ? 'bg-primary-100 text-primary-900' : 'text-gray-900 pl-4',
                                checkPosition == 'left' ? 'pl-10' : 'pl-4 pr-10',
                                'relative cursor-default select-none py-2 pr-4'
                            ]">
                            <span :class="[selected ? 'font-medium' : 'font-normal', 'block truncate']">{{ item.name }}</span>
                            <span
                                v-if="selected && !hideIcon"
                                :class="[checkPosition == 'left' ? 'left-0 pl-3' : 'right-0 pr-3', 'absolute inset-y-0 flex items-center text-primary-600']">
                                <CheckIcon class="h-5 w-5" aria-hidden="true" />
                            </span>
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>
