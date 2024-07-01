<template>
	<svg
		width="20"
		height="20"
		viewBox="0 0 20 20"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
	>
		<g opacity="0.7">
			<path
				d="M10.0003 18.3333C14.5837 18.3333 18.3337 14.5833 18.3337 10C18.3337 5.41667 14.5837 1.66667 10.0003 1.66667C5.41699 1.66667 1.66699 5.41667 1.66699 10C1.66699 14.5833 5.41699 18.3333 10.0003 18.3333Z"
				:stroke="currentcolor"
				stroke-linecap="round"
				stroke-linejoin="round"
			/>
			<path
				d="M10 6.66667V10.8333"
				:stroke="currentcolor"
				stroke-linecap="round"
				stroke-linejoin="round"
			/>
			<path
				d="M9.99512 13.3333H10.0026"
				:stroke="currentcolor"
				stroke-linecap="round"
				stroke-linejoin="round"
			/>
		</g>
	</svg>
</template>

<script>
export default {
	name: 'InfoIcon',
	props:{
		currentcolor:{
			type: String,
			default: '#8DA0C1'
		}
	}
}
</script>

<style scoped>

</style>
