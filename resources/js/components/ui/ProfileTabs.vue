<template>
	<div
		v-if="tabs && tabs.length"
		class="ProfileTabs"
		:class="{
			'custom-scroll': scroll,
			'ProfileTabs_bottom': bottom,
		}"
	>
		<!-- tabs -->
		<slot name="tabs">
			<div class="ProfileTabs-wrapper">
				<div
					v-for="tab, tab_index in tabs"
					:key="tab_index"
					class="ProfileTabs-tab tab__item"
					:class="{'ProfileTabs-tab_active': localValue === tab_index}"
					@click="switchTabs(tab_index)"
				>
					{{ tab }}
				</div>
				<slot name="tabsAfter" />
			</div>
		</slot>

		<!-- tabs -->
		<slot name="select">
			<select
				v-model="localValue"
				class="ProfileTabs-select select-css"
			>
				<option
					v-for="tab, tab_index in tabs"
					:key="tab_index"
					:value="tab_index"
				>
					{{ tab }}
				</option>
			</select>
		</slot>

		<!-- tabs -->
		<div class="ProfileTabs-content">
			<div
				v-for="tab, tab_index in tabs"
				:key="tab_index"
				class="ProfileTabs-body"
				:class="{'ProfileTabs-body_active': localValue === tab_index}"
			>
				<slot :name="`tab(${tab_index})`" />
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'ProfileTabs',
	components: {},
	props: {
		tabs: {
			type: Array,
			default: () => []
		},
		scroll: {
			type: Boolean,
			default: false
		},
		value: {
			type: Number,
			default: 0
		},
		headOnly: {
			type: Boolean,
			default: false
		},
		bottom: {
			type: Boolean,
			default: false
		}
	},
	data(){
		return {
			localValue: this.value || 0,
		}
	},
	watch: {
		value(){
			this.localValue = this.value
		},
		localValue(){
			this.$emit('input', this.localValue)
		}
	},
	methods: {
		switchTabs(index){
			this.localValue = index
		}
	}
}
</script>

<style lang="scss">
.ProfileTabs{
	overflow-x: auto;
	padding-bottom: 2.7rem;

	&-wrapper{
		display: flex;
		gap: 4rem;

		width: 100%;
		border-top: 1px solid #ededed;
	}
	&-tab{
		padding-top: 1.5rem;
		margin-top: 0.1rem;
		border-top: 4px solid transparent;

		color: #8D8D8D;
		font-size: 1.7rem;
		font-family: "Open Sans", sans-serif;
		line-height: 1.1em;
		font-weight: 600;

		transition: color 0.5s, border-color 0.5s;
		cursor: pointer;

		&_active{
			border-top: 4px solid #ED2353;
			color: #ED2353;
		}
	}

	&-select.select-css{
		display: none;
	}

	&-body{
		display: none;
		&_active{
			display: block;
		}
	}


	&_bottom{
		.ProfileTabs{
			&-wrapper{
				border-top: none;
				border-bottom: 1px solid #ededed;
			}
			&-tab{
				padding-top: 0;
				padding-bottom: 1.5rem;
				border-top: none;
				border-bottom: 4px solid transparent;
				&_active{
					border-top: none;
					border-bottom: 4px solid #ED2353;
					color: #ED2353;
				}
			}
		}
	}
}

@media(max-width:440px){
	.ProfileTabs{
		&-select.select-css{
			display: block;
		}
		&-wrapper{
			display: none;
		}
	}
}
</style>
