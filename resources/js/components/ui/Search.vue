<template>
	<label class="JobtronSearch">
		<span class="JobtronSearch-icon">
			<ChatIconSearch
				width="23"
				height="24"
			/>
		</span>
		<input
			class="JobtronSearch-input"
			type="text"
			:placeholder="placeholder"
			:value="value"
			@input="$emit('input', $event.target.value)"
		>
		<span
			v-show="value"
			class="JobtronSearch-icon ChatIcon-parent"
			@click="$emit('input', '')"
		>
			<ChatIconSearchClose />
		</span>
	</label>
</template>

<script>
import {
	ChatIconSearch,
	ChatIconSearchClose,
} from '@icons'

export default {
	name: 'JobtronSearch',
	components: {
		ChatIconSearch,
		ChatIconSearchClose,
	},
	props: {
		value: {
			type: String,
			default: ''
		},
		placeholder: {
			type: String,
			default: 'Поиск'
		},
	},
}
</script>

<style lang="scss">
.JobtronSearch{
	display: flex;
	gap: 1rem;
	align-items: center;

	padding: 1rem;
	margin-bottom: 1rem;
	border-bottom: 0.75px solid #E3EAF6;
	&-input{
		flex: 1;
		background: none;
		border: none;
		outline: none;
	}
	&-icon{
		display: inline-flex;
		align-items: center;
		justify-content: center;

		padding: 0.5rem;
	}
}
</style>
