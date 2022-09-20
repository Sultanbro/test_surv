<template>
	<sidebar
        id="award-user-sidebar"
        title="Наградить пользователя"
		v-if="open"
        :open="open"
        @close="open = false"
        width="40%"
    >
	</sidebar>
</template>

<script>
export default {
	name: 'AwardUserSidebar',
	data () {
		return {
			open: false
		}
	},
	mounted () {
		document.addEventListener('award-user-sidebar', (e) => {
			this.open = true
			console.log('USER ID:', e.detail)
		})
	}
}
</script>