<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="$emit('close')">
        <div class="bg-white rounded-xl shadow-2xl w-11/12 max-w-lg max-h-[80vh] p-6 relative overflow-hidden">
            <!-- Close Button -->
            <button @click="$emit('close')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 transition">✕</button>

            <!-- Header -->
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Details with Menus</h2>

            <!-- Products List -->
            <div class="overflow-y-auto max-h-[60vh] pr-2 custom-scrollbar">
                <ul class="space-y-2 text-gray-700 text-sm">
                    <li v-for="(item, index) in productList" :key="index" class="grid grid-cols-4 gap-2 px-2 py-1 border-b border-gray-200">
                        <span class="font-medium col-span-3"> {{ index + 1 }}. {{ item.name }} ({{ item.quantity }} pc × {{ item.rate.toLocaleString('en-US') }}) </span>
                        <span class="font-semibold text-right"> ৳ {{ bengaliToEnglishNumber((item.quantity * item.rate).toLocaleString('bn-US')) }} </span>
                    </li>
                </ul>
            </div>

            <!-- Grand Total -->
            <div class="mt-4 border-t pt-3 grid grid-cols-2 gap-2 font-bold text-gray-800">
                <span>Grand Total</span>
                <span class="font-semibold text-right mr-4"> ৳ {{ bengaliToEnglishNumber(grandTotal.toLocaleString('bn-US')) }}</span>
            </div>

            <!-- Footer -->
            <div class="mt-5 flex justify-end">
                <button @click="$emit('close')" class="px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">Close</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    show: { type: Boolean, default: false },
    products: { type: Array, default: () => [] }
})

function bengaliToEnglishNumber(bengaliStr) {
    const bnNums = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯']
    const enNums = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']
    return bengaliStr.replace(/[০-৯]/g, (d) => enNums[bnNums.indexOf(d)])
}

const grandTotal = computed(() => {
    return props.products.reduce((sum, item) => sum + item.quantity * item.rate, 0)
})
</script>
