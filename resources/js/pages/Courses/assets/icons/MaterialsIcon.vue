<template>
	<svg
		width="24"
		height="24"
		viewBox="0 0 24 24"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
	>
		<path
			opacity="0.4"
			d="M20.5 10.19H17.61C15.24 10.19 13.31 8.26 13.31 5.89V3C13.31 2.45 12.86 2 12.31 2H8.07C4.99 2 2.5 4 2.5 7.57V16.43C2.5 20 4.99 22 8.07 22H15.93C19.01 22 21.5 20 21.5 16.43V11.19C21.5 10.64 21.05 10.19 20.5 10.19Z"
			fill="#8DA0C1"
		/>
		<path
			d="M15.8002 2.21C15.3902 1.8 14.6802 2.08 14.6802 2.65V6.14C14.6802 7.6 15.9202 8.81 17.4302 8.81C18.3802 8.82 19.7002 8.82 20.8302 8.82C21.4002 8.82 21.7002 8.15 21.3002 7.75C19.8602 6.3 17.2802 3.69 15.8002 2.21Z"
			:fill="currentColor"
		/>
	</svg>
</template>

<script>
export default {
	name: 'MaterialsIcon',
	props: {
		currentColor: {
			type: String,
			default: '#8DA0C1'
		}
	}
}
</script>

<style scoped>

</style>
