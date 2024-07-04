<template>
	<main>
		{{ currentId }}
	</main>
</template>

<script>
export default {
	name: 'ArticlesContent',
	data() {
		return {
			currentId: ''
		}
	},
	mounted() {
		this.currentId = this.$route.path
	},
}
</script>
