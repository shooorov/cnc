<script setup>
import { ref } from "vue"
import { useForm } from "@inertiajs/vue3"

import {
	ArrowPathIcon,
	XMarkIcon,
} from '@heroicons/vue/24/solid';


const props = defineProps({
	image: String,
	default_image: String,
	route: String,
})

const form = useForm({
	image_file: null,
	image_removed: false,
})

const currentImage = ref(props.image);

const onFileInput = (file) => {
	return URL.createObjectURL(file);
}

const submit = () => {
	form.post(props.route, {
		onFinish: () => {

		}
	})
}

</script>
<template>
	<div class="relative h-32 w-32">
		<div class="flex text-sm text-gray-700 border border-gray-400 rounded-md bg-gray-50">
			<img class="object-cover h-32 w-32" :src="currentImage" title="Click to upload image">
			<div class="absolute bottom-0 bg-gray-50 w-full text-center opacity-60 cursor-pointer"
				@click="$refs.image.click()"><span class="text-xs text-black">Click to upload</span></div>
			<input ref="image" type="file" class="hidden" @input="form.image_file = $event.target.files[0]"
				@change="[currentImage = onFileInput($event.target.files[0]), submit()]" accept=".png, .jpg, .jpeg" />
		</div>
		<div v-show="currentImage != default_image" class="absolute top-0 right-0 px-0 py-0 bg-gray-50 opacity-60"
			title="Remove Image">
			<XMarkIcon class="w-5 h-5 text-red-500 hover:text-red-700 cursor-pointer" aria-hidden="true"
				@click="[currentImage = default_image, $refs.image.value = '', form.image_removed = true, submit()]" />
		</div>
		<div v-show="currentImage != image" class="absolute top-0 left-0 px-0 py-0 bg-gray-50 opacity-60"
			title="Refresh Image">
			<ArrowPathIcon class="w-5 h-5 text-primary-500 hover:text-primary-700 cursor-pointer" aria-hidden="true"
				@click="currentImage = image; $refs.image.value = ''; form.image_removed = false" />
		</div>
	</div>
</template>