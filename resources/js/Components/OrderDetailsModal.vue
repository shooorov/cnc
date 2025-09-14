<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="$emit('close')">
        <div class="bg-white rounded-xl shadow-2xl w-11/12 sm:w-10/12 md:w-96 max-h-[90vh] flex flex-col overflow-hidden relative">
            <!-- Sticky Header -->
            <div class="sticky top-0 bg-white z-20 p-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Order Details</h2>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-700 transition" aria-label="Close Modal">✕</button>
            </div>

            <!-- Product List: scrollable -->
            <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                <ul class="space-y-2 text-gray-700 text-sm">
                    <li v-for="(item, index) in products" :key="index" class="grid grid-cols-7 gap-2 px-2 py-1 border-b border-gray-200">
                        <span class="font-mono col-span-3 truncate" :title="item.name">{{ index + 1 }}. {{ item.name }}</span>
                        <span class="font-mono text-xs text-gray-500 text-right col-span-2"> ({{ formatNumber(item.quantity) }} pc × {{ formatNumber(item.rate) }}) </span>
                        <span class="font-mono text-right col-span-2"> ৳ {{ formatNumber(item.quantity * item.rate) }} </span>
                    </li>
                </ul>
            </div>

            <!-- Sticky Footer: totals and button -->
            <div class="sticky bottom-0 bg-white z-20 p-4 border-t border-gray-200">
                <div class="grid grid-cols-2 gap-2 font-bold text-gray-800 mb-3">
                    <span class="font-mono">Subtotal</span>
                    <span class="font-mono text-right">৳ {{ formatNumber(subTotal) }}</span>

                    <span class="font-mono">VAT</span>
                    <span class="font-mono text-right">৳ {{ formatNumber(vat) }}</span>

                    <span class="font-mono">Grand Total</span>
                    <span class="font-mono text-right">৳ {{ formatNumber(grandTotal) }}</span>
                </div>
                <div class="flex justify-end">
                    <button @click="$emit('close')" class="px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    show: { type: Boolean, default: false },
    products: { type: Array, default: () => [] }, // [{ name, quantity, rate }]
    vat: { type: Number, default: 0 }
})

// Subtotal
const subTotal = computed(() => props.products.reduce((sum, item) => sum + item.quantity * item.rate, 0))

// Grand total
const grandTotal = computed(() => subTotal.value + props.vat)

// Format numbers (no decimals, commas)
const formatNumber = (num) => Number(num).toLocaleString('en-US', { maximumFractionDigits: 0 })
</script>

<style scoped>
/* Thin, subtle scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.15);
    border-radius: 9999px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(0, 0, 0, 0.25);
}

/* Firefox */
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 0, 0, 0.15) transparent;
}
</style>
