<template></template>

<script setup>
import { computed, ref, watch } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { BanknotesIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  items: Array,
  open: Boolean
})

const emit = defineEmits(['update:close'])

watch(
  () => props.open,
  () => {
    console.log(props.open)
    emit('update:close', props.open)
  }
)

const total = computed(() => {
  return props.items.reduce((sum, item) => parseInt(sum) + (parseInt(item.amount) || 0), 0)
})
</script>
