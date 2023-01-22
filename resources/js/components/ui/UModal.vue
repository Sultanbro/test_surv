<template>
	<div>
		<b-modal
			ref="my-modal"
			hide-footer
			:title="title"
			visible
		>
			<div class="d-block">
				<div
					v-for="item in items"
					:key="item"
					class="modal-p"
					v-html="item"
				/>
			</div>
		</b-modal>
	</div>
</template>

<script>
export default {

	name: 'UModal',
	props: {
		items: {
			default: null
		},
		title: {
			default: 'Модальное окно'
		}
	},
	created(){
		this.items = JSON.parse(this.items)
	},
	data() {
		return {

		}
	},
	methods: {

	}

}
</script>

<style lang="scss" scoped>
p.modal-p {
  text-align: left !important;
}
</style>
