<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="$emit('close')">
        <div class="bg-white rounded-xl shadow-2xl w-11/12 max-w-lg max-h-[80vh] p-6 relative flex flex-col overflow-hidden">
            <!-- Close Button: fixed inside modal -->
            <button @click="$emit('close')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 transition z-10" aria-label="Close Modal">✕</button>

            <!-- Content Wrapper: scrollable -->
            <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
                <!-- Header -->
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Order Details</h2>

                <!-- Products List -->
                <ul class="space-y-2 text-gray-700 text-sm">
                    <li v-for="(item, index) in products" :key="index" class="grid grid-cols-4 gap-2 px-2 py-1 border-b border-gray-200">
                        <span class="font-medium col-span-3 flex flex-col">
                            <span>{{ index + 1 }}. {{ item.name }}</span>
                            <span class="text-xs text-gray-500 self-end"> ({{ formatNumber(item.quantity) }}pc × {{ formatNumber(item.rate) }}) </span>
                        </span>
                        <span class="font-semibold text-right"> ৳ {{ formatNumber(item.quantity * item.rate) }} </span>
                    </li>
                </ul>

                <!-- Totals -->
                <div class="mt-4 border-t pt-3 grid grid-cols-2 gap-2 font-bold text-gray-800">
                    <span>Subtotal</span>
                    <span class="font-semibold text-right mr-4">৳ {{ formatNumber(subTotal) }}</span>

                    <span>VAT</span>
                    <span class="font-semibold text-right mr-4">৳ {{ formatNumber(vat) }}</span>

                    <span>Grand Total</span>
                    <span class="font-semibold text-right mr-4">৳ {{ formatNumber(grandTotal) }}</span>
                </div>

                <!-- Footer -->
                <div class="mt-5 flex justify-end">
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
    vat: { type: Number, default: 0 } // VAT amount sent from backend
})

// Subtotal = sum of product totals
const subTotal = computed(() => {
    return props.products.reduce((sum, item) => sum + item.quantity * item.rate, 0)
})

// Grand total = subtotal + VAT
const grandTotal = computed(() => {
    return subTotal.value + props.vat
})

// Helper for clean number formatting (no decimals, commas)
const formatNumber = (num) => {
    return Number(num).toLocaleString('en-US', { maximumFractionDigits: 0 })
}
</script>

<style scoped>
/* Custom scrollbar for product list */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px; /* very thin */
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent; /* make track invisible */
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.15); /* very light gray */
    border-radius: 9999px; /* rounded */
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(0, 0, 0, 0.25); /* slightly darker on hover */
}

/* For Firefox */
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 0, 0, 0.15) transparent;
}
</style>
