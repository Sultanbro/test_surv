<template>
	<div
		class="IntegrationsPageItem"
		@click="$emit('click', $event)"
	>
		<img
			v-if="icon"
			:src="icon"
			alt=""
			class="IntegrationsPageItem-icon"
		>
		{{ title }}
		<span
			class="IntegrationsPageItem-status"
			:class="[status ? 'IntegrationsPageItem-status_enabled' : 'IntegrationsPageItem-status_disabled']"
		>
			{{ status ? 'Подключена' : 'Не настроена' }}
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
		},
		icon: {
			type: String,
			required: true
		},
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
	height: 150px;
	padding: 7.5px 10px 30px;
	border: 1px solid #daecf5;

	position: relative;

	text-align: center;
	cursor: pointer;
	background: #f8fcfe;
	border-radius: 4px;

	&-status{
		padding: 5px 10px;
		position: absolute;
		bottom: 10px;
		left: -10px;
		font-size: 8px;
		font-weight: 700;
		color: #fff;
		&_enabled{
			background-color: #8bab00;
		}
		&_disabled{
			background-color: #e84f71;
		}
	}

	&-icon{
		width: 48px;
		height: auto;
		margin-bottom: 10px;
	}
}
</style>
