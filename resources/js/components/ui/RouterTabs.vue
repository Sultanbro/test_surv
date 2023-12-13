<template>
	<div
		class="RouterTabs"
		:style="styleVars"
	>
		<div class="RouterTabs-tabs">
			<router-link
				v-for="tab, index in tabs"
				:key="index"
				:to="tab.value"
				class="RouterTabs-tab"
				:class="{
					'RouterTabs-tab_active': tab.value === value
				}"
			>
				{{ tab.title }}
			</router-link>
		</div>
		<div class="RouterTabs-content">
			<slot />
		</div>
	</div>
</template>

<script>
export default {
	name: 'RouterTabs',
	components: {},
	props: {
		tabs: {
			type: Array,
			default: () => [],
		},
		value: {
			type: String,
			default: '',
		},
		position: {
			type: String,
			default: 'top',
		},
		color: {
			type: String,
			default: '#156ae8',
		}
	},
	data(){
		return {}
	},
	computed: {
		styleVars(){
			return [
				`--highlight: ${this.color}`,
			].join(';')
		},
	},
	watch: {},
	created(){},
	mounted(){},
	methods: {},
}
</script>

<style lang="scss">
.RouterTabs{
	border-bottom: 1px solid #eaeaea;
	&-tabs{
		display: flex;
		flex-flow: row wrap;
		align-items: flex-end;
		gap: 20px;

		margin-bottom: -1px;
	}
	&-tab{
		padding: 0 0 20px;
		border-bottom: 2px solid transparent;

		font-size: 16px;
		font-weight: 600;
		color: #8da0c1;

		&:hover{
			color: var(--highlight);
		}

		&_active{
			border-bottom: 2px solid var(--highlight);
			color: var(--highlight);
		}
	}
	// &-content{}
}
</style>
