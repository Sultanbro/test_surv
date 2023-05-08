<template>
	<div
		class="IntegrationsPageItem"
		@click="$emit('click', $event)"
	>
		{{ title }}
		<span
			class="IntegrationsPageItem-status"
			:class="[status ? 'IntegrationsPageItem-status_enabled' : 'IntegrationsPageItem-status_disabled']"
		>
			{{ status ? 'Подклбючен' : 'Не настроен' }}
		</span>
	</div>
</template>

<script>
export default {
	name: 'IntegrationsPageItem',
	components: {},
	props: {
		title: {
			type: String,
			required: true
		},
		name: {
			type: String,
			required: true
		},
		status: {
			type: Boolean,
			default: false
		}
	}
}
</script>

<style lang="scss">
.IntegrationsPageItem{
	display: flex;
	flex-flow: column nowrap;
	align-items: center;
	justify-content: center;

	width: 150px;
	height: 120px;
	padding: 7.5px 10px;
	border: 1px solid #daecf5;

	cursor: pointer;
	background: #f8fcfe;
	&-status{
		font-size: 8px;
		&_enabled{
			color: green;
		}
		&_disabled{
			color: red;
		}
	}
}
</style>
