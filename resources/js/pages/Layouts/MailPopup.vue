<template>
	<div
		class="popup__con"
		:class="{'v-loading': loading}"
	>
		????????
	</div>
</template>

<script>
export default {
	name: 'MailPopup',
	props: {},
	data: function () {
		return {
			data: [],
			loading: false
		};
	},
	created(){
		// this.fetchData()
	},
	methods: {
		fetchData() {
			this.loading = true

			this.axios.post('/checklist', {})
				.then((response) => {
					this.data = response.data
					this.loading = false
				});
		},
	}
};
</script>