<template>
	<div class="DaysCheck">
		<div
			v-for="day in 31"
			:key="day"
			class="DaysCheck-item ChatIcon-parent"
			:class="{'ChatIcon-active': value.includes(day)}"
			@click="onChange(day)"
		>
			<!-- ChatIconMassReaded -->
			<div class="DaysCheck-check">
				<ChatIconMassReaded v-if="value.includes(day)" />
			</div>
			<div class="DaysCheck-text">
				{{ day }}
			</div>
		</div>
	</div>
</template>

<script>
import {
	ChatIconMassReaded,
} from '@icons'
export default {
	name: 'DaysCheck',
	components: {
		ChatIconMassReaded,
	},
	props: {
		value: {
			type: Array,
			default: () => []
		},
		max: {
			type: Number,
			default: 31
		}
	},
	methods: {
		onChange(key){
			const value = this.value.slice()
			const index = value.indexOf(key)
			if(~index){
				value.splice(index, 1)
			}
			else{
				value.push(key)
			}
			this.$emit('input', value)
		}
	}
}
</script>

<style>
</style>
