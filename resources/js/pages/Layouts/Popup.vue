<template>
	<div
		class="overlay custom-scroll-y js-overlay"
		:class="{'active': open}"
		@click.self="$emit('close')"
		v-scroll-lock="open"
	>
		<div
			class="popup award js-popup"
			:style="`width:${width}`"
		>
			<div class="popup__content">
				<div class="popup-header">
					<a
						class="popup-close js-close-popup"
						href="#"
						@click="$emit('close')"
					>
						<img
							src="/images/dist/popup-close.svg"
							alt="Close icon"
						>
					</a>
					<div class="popup__header-content">
						<div class="popup__title">
							{{ title }}
						</div>
						<div class="popup__subtitle">
							{{ desc }}
						</div>
					</div>
				</div>
				<div class="popup__body">
					<slot />
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {

	name: 'LayoutsPopup',
	props: {
		title: {},
		desc: {},
		open: {},
		width: {},
	},

	data() {
		return {

		}
	}
}
</script>

<style lang="scss" scoped>

</style>
