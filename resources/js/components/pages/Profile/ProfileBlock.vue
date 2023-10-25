<template>
	<div
		:id="id"
		class="ProfileBlock index block _anim _anim-no-hide"
	>
		<slot
			v-if="!innerTitle"
			name="header"
		>
			<div class="ProfileBlock-header">
				<div class="ProfileBlock-title">
					{{ title }}
				</div>
				<div
					v-if="subtitle"
					class="ProfileBlock-subtitle"
				>
					{{ subtitle }}
				</div>
			</div>
		</slot>
		<div class="ProfileBlock-body">
			<slot
				v-if="innerTitle"
				name="header"
			>
				<div class="ProfileBlock-header">
					<div class="ProfileBlock-title">
						{{ title }}
					</div>
					<div
						v-if="subtitle"
						class="ProfileBlock-subtitle"
					>
						{{ subtitle }}
					</div>
				</div>
			</slot>
			<slot />
		</div>
	</div>
</template>

<script>
export default {
	name: 'ProfileBlock',
	components: {},
	props: {
		id: {
			type: String,
			required: true
		},
		title: {
			type: String,
			required: true
		},
		subtitle: {
			type: String,
			default: ''
		},
		innerTitle: Boolean,
	},
	data(){
		return {
			observer: null,
		}
	},
	mounted(){},
	beforeUnmount(){
		this.observer && this.observer.disconnect()
		this.observer = null
	},
	methods: {
		initObserver(){
			if(this.observer) return
			this.$nextTick(() => {
				const width = this.$viewportSize.width
				if(width > 900){
					this.observer = new IntersectionObserver(this.onIntersect, {
						threshold: 0.1
					})
					this.$el instanceof Element && this.intersectionObserver.observe(this.$el)
					return
				}
				this.$emit('intersect', this.id)
			})
		},
		onIntersect(entries){
			entries.forEach(entry => {
				if(entry.isIntersecting) this.$emit('intersect', this.id)
			})
		}
	},
}
</script>

<style lang="scss">
.ProfileBlock{
	&-header{
		margin-top: 3rem;
	}
	&-body{
		padding: 3rem 4rem;
		background: #f8f9fd;

		border-radius: 1.5rem 1.5rem 0 0;

		.ProfileBlock{
			&-header{
				margin-top: 0;
			}
		}
	}
	&-title{
		font-size: 3.1rem;
		font-weight: 600;
		margin-bottom: .4rem;
		text-transform: uppercase;
	}
	&-subtitle{
		font-size: 1.6rem;
		margin-bottom: 2rem;
	}
}
</style>
