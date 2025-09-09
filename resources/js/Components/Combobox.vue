<script setup>
import { computed, ref, watch } from 'vue'
import { Combobox, ComboboxButton, ComboboxInput, ComboboxLabel, ComboboxOption, ComboboxOptions } from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
    placeholder: {
        type: String,
        default: 'Select ...'
    },
    disabled: {
        type: Boolean,
        default: false
    },
    items: {
        type: Array,
        default: []
    },
    label: {
        type: String,
        default: ''
    },
    modelValue: [Object, Number, String, Boolean],
    position: {
        default: 'bottom'
    }
})

const query = ref('')
const selectedItem = ref(props.items.find((item) => props.modelValue == item.id) ?? '')
const filteredItems = computed(() =>
    query.value === ''
        ? props.items
        : props.items.filter((item) => {
              console.log(item)
              return item.name.toLowerCase().includes(query.value.toLowerCase())
          })
)
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
    <Combobox as="div" v-model="selectedItem">
        <ComboboxLabel :class="['block text-sm font-medium text-gray-700', props.label.length == 0 ? 'sr-only' : '']"> {{ props.label }} </ComboboxLabel>
        <div class="relative w-full">
            <ComboboxInput
                :class="[
                    disabled ? 'bg-gray-100' : '',
                    'w-full rounded border border-gray-300 bg-white py-2 pl-3 pr-10 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 sm:text-sm'
                ]"
                @change="query = $event.target.value"
                :displayValue="(item) => item.name"
                :placeholder="placeholder"
                autocomplete="off"
                :disabled="disabled" />
            <ComboboxButton
                :class="[disabled ? 'cursor-default' : '', 'absolute inset-y-0 right-0 flex items-center rounded-r px-2 focus:outline-none']"
                :disabled="disabled">
                <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
            </ComboboxButton>

            <ComboboxOptions
                v-if="filteredItems.length > 0"
                :class="[
                    position == 'top' ? 'bottom-10 mb-1' : 'mt-1',
                    'absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm'
                ]">
                <ComboboxOption v-for="item in filteredItems" :key="item.id" :value="item" as="template" v-slot="{ active, selected }">
                    <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-primary-100 text-primary-900' : 'text-gray-900']">
                        <span :class="['block truncate', selected && 'font-semibold']">
                            {{ item.name ?? 'N/A' }}
                        </span>

                        <span v-if="selected" :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-primary-600' : 'text-primary-500']">
                            <CheckIcon class="h-5 w-5" aria-hidden="true" />
                        </span>
                    </li>
                </ComboboxOption>
            </ComboboxOptions>
        </div>
    </Combobox>
</template>
