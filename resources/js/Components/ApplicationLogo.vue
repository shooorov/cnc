<script setup>
import { usePage } from '@inertiajs/vue3'
const page = usePage()
</script>

<template>
    <span class="flex items-center shrink-0 px-4">
		<img v-if="page.props.app.logo" class="h-16 w-auto" :src="page.props.app.logo" alt="logo" />
		<span v-else class="text-secondary-500 px-3 text-2xl font-fancy leading-6 font-medium">{{ page.props.app.name }}</span>
    </span>
</template>
