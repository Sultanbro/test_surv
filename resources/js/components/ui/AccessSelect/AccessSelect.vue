<template>
	<div
		class="AccessSelect-bg"
		@click.self="$emit('close')"
		v-scroll-lock="open"
	>
		<div class="AccessSelect">
			<slot
				name="before"
				:search="accessSearch"
				:selected-tab="selectedTab"
			/>
			<AccessSelectSearch
				v-if="searchFirst"
				v-model="accessSearch"
			/>
			<AccessSelectTabs
				:tabs="tabs"
				v-model="selectedTab"
			/>
			<AccessSelectSearch
				v-if="!searchFirst"
				v-model="accessSearch"
			/>

			<div class="AccessSelect-list">
				<slot
					:search="accessSearch"
					:selected-tab="selectedTab"
				>
					<AccessSelectList
						v-if="selectedTab === 'Сотрудники'"
						:key="1"
						:search="accessSearch"
						:selected="value"
						:items="accessDictionaries.users"
						:avatar="true"
						:position="true"
						:type="1"
						@change="changeAccessList($event)"
					/>
					<AccessSelectList
						v-if="selectedTab === 'Отделы'"
						:key="2"
						:search="accessSearch"
						:selected="value"
						:items="accessDictionaries.profile_groups"
						:type="2"
						@change="changeAccessList($event)"
					/>
					<AccessSelectList
						v-if="selectedTab === 'Должности'"
						:key="3"
						:search="accessSearch"
						:selected="value"
						:items="accessDictionaries.positions"
						:type="3"
						@change="changeAccessList($event)"
					/>
				</slot>
			</div>

			<AccessSelectFooter
				:count="accessList.length"
				:submit="submit"
				@submit="$emit('submit')"
			/>
			<slot
				name="after"
				:search="accessSearch"
				:selected-tab="selectedTab"
			/>
		</div>
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
		submit: {
			type: String,
			default: 'Пригласить сотрудника'
		},
		open: {
			type: Boolean,
			default: false
		},
		searchFirst: {
			type: Boolean,
			default: false
		},
		accessDictionaries: {
			type: Object,
			default: () => ({
				users: [],
				profile_groups: [],
				positions: [],
			})
		}
	},
	data(){
		return {
			selectedTab: 'Сотрудники',
			accessList: this.value,
			accessSearch: '',
		}
	},
	watch: {
		value(value){
			this.accessList = typeof value === 'string' ? value : JSON.parse(JSON.stringify(value))
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
				this.accessList.push({
					id,
					name,
					image,
					type,
				})
			}

			this.$emit('input', this.accessList)
		},
	},
}
</script>

<style>
.AccessSelect-bg {
	position: fixed;
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
	z-index: 10;
	background-color: rgba(255, 255, 255, 0.3);
}

.AccessSelect {
	position: absolute;
	/* top: 25%; */
	/* left: 40%; */
	bottom: 0;
	left: 270px;
	padding: 25px 20px 20px 20px;
	background-color: #FFFFFF;
	box-shadow: 0 0 3px rgba(0, 0, 0, 0.05), 0 15px 60px -40px rgba(45, 50, 90, 0.2);
	border-radius: 12px;
}

.AccessSelect-icon {
	filter: invert(99%) sepia(1%) saturate(1439%) hue-rotate(238deg) brightness(107%) contrast(69%);
	cursor: pointer;
}
.AccessSelect-icon:hover {
	filter: invert(27%) sepia(73%) saturate(2928%) hue-rotate(209deg) brightness(96%) contrast(89%);
}



.AccessSelect-list {
	padding: 25px 0;
}

</style>
