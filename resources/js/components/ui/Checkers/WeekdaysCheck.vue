<template>
	<div class="WeekdaysCheck">
		<div
			v-for="weekday, key in weekdays"
			:key="key"
			class="WeekdaysCheck-item ChatIcon-parent"
			:class="{'ChatIcon-active': value.includes(key)}"
			@click="onChange(key)"
		>
			<!-- ChatIconMassReaded -->
			<div class="WeekdaysCheck-check">
				<ChatIconMassReaded v-if="value.includes(key)" />
			</div>
			<div class="WeekdaysCheck-text">
				{{ weekday }}
			</div>
		</div>
	</div>
</template>

<script>
import {
	ChatIconMassReaded,
} from '@icons'
export default {
	name: 'WeekdaysCheck',
	components: {
		ChatIconMassReaded,
	},
	props: {
		value: {
			type: Array,
			default: () => []
		}
	},
	data(){
		return {
			// localValue: JSON.parse(JSON.stringify(this.value)),
			weekdays: [ 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс' ]
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

<style lang="scss">
.WeekdaysCheck{
	&-item{
		display: flex;
		align-items: center;
		justify-content: flex-start;
		gap: 8px;
	}
	&-check{
		display: flex;
		align-items: center;
		justify-content: center;

		width: 15px;
		height: 15px;
		border: 1px solid #777;
		border-radius: 3px;
	}
	&-text{
		font-weight: 600;
		font-size: 11px;
		line-height: 1.3;
		letter-spacing: -0.04em;

		color: #6181B8;
	}
}
.ChatIcon-parent:hover .WeekdaysCheck-text{
	color: #3361FF;
}
.ChatIcon-active .WeekdaysCheck-text{
	color: #3361FF;
}
</style>
