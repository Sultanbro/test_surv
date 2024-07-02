<template>
	<div class="AccessSelectTabs">
		<div
			v-for="tab, i in tabs"
			:key="i"
			class="AccessSelectTabs-tab"
			:class="{'AccessSelectTabs-tab_active': value === tab}"
			@click="$emit('input', tab)"
		>
			{{ tab }}
		</div>
	</div>
</template>

<script>
export default {
	name: 'AccessSelectTabs',
	components: {},
	props: {
		tabs: {
			type: Array,
			default: () => ['Сотрудники', 'Отделы', 'Должности', 'Все'],
		},
		value: {
			type: String,
			default: 'Сотрудники',
		}
	}
}
</script>

<style>
.AccessSelectTabs {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: flex-start;
	gap: 20px;
	padding-right: 20px;
	border-bottom: 1px solid #EBEBF9;
}
.AccessSelectTabs-tab {
	padding-bottom: 16px;
	font-family: "Inter", sans-serif;
	font-style: normal;
	font-weight: 600;
	font-size: 16px;
	line-height: 20px;
	letter-spacing: -0.02em;
	color: #8DA0C1;
	cursor: pointer;
}
.AccessSelectTabs-tab_active {
	margin-bottom: -1px;
	border-bottom: 1px solid #156AE8;
	color: #156AE8;
}
.AccessSelectTabs-tab:hover {
	color: #156AE8;
}
</style>
