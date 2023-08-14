<template>
	<div>
		<b-modal
			ref="my-modal"
			hide-footer
			:title="title"
			visible
		>
			<div class="d-block">
				<!-- eslint-disable -->
				<div
					v-for="item in items"
					:key="item"
					class="modal-p"
					v-html="item"
				/>
				<!-- eslint-enable -->
			</div>
		</b-modal>
	</div>
</template>

<script>
/* eslint-disable vue/no-mutating-props */
export default {

	name: 'UModal',
	props: {
		items: {
			type: Array,
			default: null
		},
		title: {
			type: String,
			default: 'Модальное окно'
		}
	},
	data() {
		return {

		}
	},
	created(){
		this.items = JSON.parse(this.items)
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
