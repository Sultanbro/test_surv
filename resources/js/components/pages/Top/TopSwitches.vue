<template>
	<div class="TopSwitches">
		<label
			v-for="item, index in items"
			:key="index"
			class="TopSwitches-row"
		>
			<JobtronSwitch
				class="TopSwitches-input"
				:value="item.value"
				@input="$emit('change', {id: item.id, value: $event})"
			/>
			<span class="TopSwitches-label">
				{{ item.name }}
			</span>
		</label>
	</div>
</template>

<script>
import JobtronSwitch from '@ui/Switch'
export default {
	name: 'TopSwitches',
	components: {
		JobtronSwitch,
	},
	props: {
		items: {
			type: Array,
			default: () => []
		}
	},
}
</script>

<style lang="scss">
.TopSwitches{
	&-row{
		display: flex;
		align-items: center;
		gap: 1rem;
	}
}
</style>
