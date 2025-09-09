<template>
    <span :class="[
		'inline-flex items-center px-2 py-1 text-xs font-medium', 
		color,
	]"> {{ statusName }}</span>

</template>

<script>
export default {
    props: {
        name: String,
		rounded: {
			type: String,
			default: "rounded-full"
		},
        colors: {
			type: Object,
			default: {
				// pending: 'bg-gray-100 text-yellow-700',
				// active: 'bg-green-100 text-green-700',
				// inactive: 'bg-red-100 text-red-700',
			}
		},
    },
    setup(props) {

		const color = props.colors[props.name] ?? 'bg-slate-100 text-slate-700 rounded-full';
		const statusName = props.name.charAt(0).toUpperCase() + props.name.slice(1)

        return {
            color,
			statusName
        }
    },
}
</script>
