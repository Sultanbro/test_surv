<template>
	<div class="AccessSelect">
		<slot
			name="before"
			:search="accessSearch"
			:selected-tab="selectedTab"
		/>
		<AccessSelectSearch
			v-if="searchPosition === 'beforeTabs'"
			v-model="accessSearch"
			class="AccessSelect-search"
		/>
		<AccessSelectTabs
			v-if="tabs && tabs.length && !accessSearch"
			:tabs="tabs"
			v-model="selectedTab"
			class="AccessSelect-tabs"
		/>
		<AccessSelectSearch
			v-if="searchPosition === 'afterTabs'"
			v-model="accessSearch"
			class="AccessSelect-search"
		/>

		<div class="AccessSelect-list">
			<slot
				:search="accessSearch"
				:selected-tab="selectedTab"
			>
				<AccessSelectList
					v-if="accessSearch"
					:key="99"
					:search="''"
					:selected="accessList"
					:items="searchResults"
					:type="0"
					@change="changeAccessList($event)"
				/>
				<template v-else>
					<slot
						name="users"
						:search="accessSearch"
					>
						<AccessSelectList
							v-if="selectedTab === 'Сотрудники'"
							:key="1"
							:search="accessSearch"
							:selected="accessList"
							:items="accessDictionaries.users"
							:avatar="true"
							:position="true"
							:type="1"
							@change="changeAccessList($event)"
						/>
					</slot>
					<AccessSelectList
						v-if="selectedTab === 'Отделы'"
						:key="2"
						:search="accessSearch"
						:selected="accessList"
						:items="accessDictionaries.profile_groups"
						:type="2"
						@change="changeAccessList($event)"
					/>
					<AccessSelectList
						v-if="selectedTab === 'Должности'"
						:key="3"
						:search="accessSearch"
						:selected="accessList"
						:items="accessDictionaries.positions"
						:type="3"
						@change="changeAccessList($event)"
					/>
				</template>
			</slot>
		</div>

		<AccessSelectFooter
			:count="accessList.length"
			:submit-button="submitButton"
			:submit-disabled="submitDisabled"
			@submit="$emit('submit', accessList)"
			class="AccessSelect-footer"
		/>
		<slot
			name="after"
			:search="accessSearch"
			:selected-tab="selectedTab"
		/>
	</div>
</template>

<script>
import AccessSelectTabs from './AccessSelectTabs.vue'
import AccessSelectSearch from './AccessSelectSearch.vue'
import AccessSelectFooter from './AccessSelectFooter.vue'
import AccessSelectList from './AccessSelectList.vue'

export default {
	name: 'AccessSelect',
	components: {
		AccessSelectTabs,
		AccessSelectSearch,
		AccessSelectFooter,
		AccessSelectList,
	},
	props: {
		tabs: {
			type: Array,
			default: () => ['Сотрудники', 'Отделы', 'Должности', 'Все']
		},
		value: {
			type: [Array, String],
			default: () => [],
		},
		submitButton: {
			type: String,
			default: 'Пригласить сотрудника'
		},
		searchPosition: {
			type: String, // beforeTabs, afterTabs
			default: 'afterTabs'
		},
		accessDictionaries: {
			type: Object,
			default: () => ({
				users: [],
				profile_groups: [],
				positions: [],
			})
		},
		submitDisabled: {
			type: Boolean,
			default: false
		},
		single: {
			type: Boolean,
			default: false
		},
		search: {
			type: String,
			default: ''
		}
	},
	data(){
		return {
			selectedTab: 'Сотрудники',
			accessList: JSON.parse(JSON.stringify(this.value)),
			accessSearch: '',
		}
	},
	computed: {
		searchResults(){
			const lowerSearch = this.accessSearch.toLowerCase()
			const result = []

			// Пользователи
			this.accessDictionaries.users.forEach(user => {
				if(user.name?.toLowerCase().includes(lowerSearch)) return result.push({
					...user,
					type: 1
				})
				if(user.position?.toLowerCase().includes(lowerSearch)) return result.push({
					...user,
					type: 1
				})
			})

			// Отделы
			this.accessDictionaries.profile_groups.forEach(group => {
				if(group.name?.toLowerCase().includes(lowerSearch)) return result.push({
					...group,
					type: 2
				})
			})

			// Должности
			this.accessDictionaries.positions.forEach(position => {
				if(position.name?.toLowerCase().includes(lowerSearch)) return result.push({
					...position,
					type: 3
				})
			})
			return result
		}
	},
	watch: {
		value(value){
			this.accessList = typeof value === 'string' ? value : JSON.parse(JSON.stringify(value))
		},
		search(value){
			this.accessSearch = value
		},
		selectedTab(value){
			if(value === 'Все') return this.$emit('input', 'all')
			if(typeof this.accessList === 'string') this.accessList = []
		}
	},
	methods: {
		changeAccessList({id, name, type, image = null}) {
			if(typeof this.accessList === 'string') this.accessList = []
			const filtered = this.accessList.filter(item => (item.id == id) && (item.type == type))

			if (filtered.length) {
				const el = filtered[0]
				this.accessList.splice(this.accessList.indexOf(el), 1)
			}
			else{
				if(this.single){
					this.accessList = [{
						id,
						name,
						image,
						type,
					}]
				}
				else{
					this.accessList.push({
						id,
						name,
						image,
						type,
					})
				}
			}

			this.$emit('input', this.accessList)
		},
	},
}
</script>

<style lang="scss">
.AccessSelect {
	display: flex;
	flex-flow: column nowrap;

	overflow: hidden;
	&-icon {
		filter: invert(99%) sepia(1%) saturate(1439%) hue-rotate(238deg) brightness(107%) contrast(69%);
		cursor: pointer;
		&:hover {
			filter: invert(27%) sepia(73%) saturate(2928%) hue-rotate(209deg) brightness(96%) contrast(89%);
		}
	}
	&-search,
	&-tabs,
	&-list{

	}
	&-list {
		display: flex;
		flex-flow: column nowrap;
		overflow: hidden;
	}
}
</style>
