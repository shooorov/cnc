<script setup>
import { onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import Receipt from '@/Components/Receipt.vue'

const props = defineProps({
	order: Object,
})

onMounted(() => {
    setTimeout(function () {
        window.print()
    }, 1000)
})
</script>
<template>
    <Head title="POS Receipt" />

    <div class="print">
        <Receipt :order="props.order" />
    </div>
</template>
