<template>
	<div
		class="ProfileStars"
		:class="{'ProfileStars_editable': editable}"
	>
		<div
			v-for="i in max"
			:key="i"
			class="ProfileStars-star"
			:class="{'ProfileStars-star_on': value >= ((max + 1) - i)}"
			@click="onClick((max + 1) - i)"
		/>
	</div>
</template>

<script>
export default {
	name: 'ProfileStars',
	components: {},
	props: {
		max: {
			type: Number,
			default: 10,
		},
		value: {
			type: Number,
			default: 0,
		},
		editable: {
			type: Boolean,
			default: false
		},
		off: {
			type: String,
			default: () => ''
		},
		on: {
			type: String,
			default: () => ''
		},
	},
	methods: {
		onClick(value){
			if(this.editable) this.$emit('input', value)
			alert(value)
		}
	}
}
</script>

<style lang="scss">
.ProfileStars{
	display: flex;
	flex-flow: row-reverse nowrap;
	gap: 0.8rem;
	&_editable{
		&:hover{
			.ProfileStars-star{
				cursor: pointer;
				&:hover{
					background-image: url(/images/dist/trainee-star-done.svg);
					& ~ .ProfileStars-star{
						background-image: url(/images/dist/trainee-star-done.svg);
					}
				}
			}
		}
	}
	&-star{
		width: 1.4rem;
		height: 1.4rem;
		background: url(/images/dist/trainee-star.svg) no-repeat;
		background-size: contain;
		&_on{
			background-image: url(/images/dist/trainee-star-done.svg);
		}
	}
}
</style>
