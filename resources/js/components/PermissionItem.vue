<template>
	<!-- eslint-disable vue/no-mutating-props -->
	<b-tr
		v-if="actualTargets.length || !item.id"
		class="PermissionItem"
	>
		<b-td class="person">
			<!-- <superselect
				:values="item.targets"
				class="w-full single"
			/> -->
			<AccessSelectFormControl
				:items="actualTargets"
				@click="isTargetSelect = true"
			/>
			<JobtronOverlay
				v-if="isTargetSelect"
				@close="isTargetSelect = false"
			>
				<AccessSelect
					v-model="localTargets"
					:tabs="['Сотрудники', 'Отделы', 'Должности']"
					:access-dictionaries="accessDictionaries"
					search-position="beforeTabs"
					submit-button=""
					absolute
				/>
			</JobtronOverlay>
		</b-td>

		<b-td class="role">
			<multiselect
				ref="role_select"
				v-model="item.roles"
				:options="local_roles"
				:multiple="true"
				:preserve-search="true"
				:hide-selected="true"
				placeholder="Выберите"
				label="name"
				track-by="name"
			/>
		</b-td>
		<b-td class="groups">
			<multiselect
				ref="group_select"
				v-model="item.groups"
				:options="local_groups"
				:multiple="true"
				:close-on-select="false"
				:clear-on-select="false"
				:preserve-search="true"
				:hide-selected="true"
				placeholder="Выберите"
				label="name"
				track-by="name"
				@select="onSelect"
				@remove="onRemove"
			/>
		</b-td>
		<b-td class="actions">
			<div class="d-flex align-items-center">
				<button
					class="btn btn-success btn-icon"
					@click="$emit('updateItem')"
				>
					<i class="fa fa-save" />
				</button>
				<button
					class="btn btn-danger btn-icon"
					@click="$emit('deleteItem')"
				>
					<i class="fa fa-times" />
				</button>
			</div>
		</b-td>
	</b-tr>
</template>

<script>
/* eslint-disable camelcase */
/* eslint-disable vue/no-mutating-props */
import JobtronOverlay from '@ui/Overlay.vue'
import AccessSelect from '@ui/AccessSelect/AccessSelect.vue'
import AccessSelectFormControl from '@ui/AccessSelect/AccessSelectFormControl.vue'

const types = [
	'all',
	'users',
	'profile_groups',
	'positions',
]

export default {
	name: 'PermissionItem',
	components: {
		JobtronOverlay,
		AccessSelect,
		AccessSelectFormControl,
	},
	props: {
		item: {
			type: Object,
			default: null
		},
		groups: {
			type: Array,
			default: null
		},
		users: {
			type: Array,
			default: null
		},
		roles: {
			type: Array,
			default: null
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
	data() {
		return {
			local_groups: [],
			local_roles: [],
			localTargets: [],
			isTargetSelect: false,
		}
	},
	computed: {
		actualTargets(){
			return this.localTargets.slice().filter(target => {
				return ~this.accessDictionaries[types[target.type]].findIndex(item => item.id === target.id)
			})
		}
	},
	watch: {
		item: {
			deep: true,
			handler () {
				this.$emit('updated');
			}
		},
		localTargets: {
			deep: true,
			handler () {
				this.item.targets = this.localTargets
			}
		}
	},
	created() {
		this.local_groups = this.groups;
		this.local_roles = this.roles;
		this.localTargets = this.item.targets
		if(this.item.groups_all) {
			this.local_groups = [];
			this.item.groups.splice(0,this.item.groups.length + 1)
			this.item.groups.push({
				id: 0,
				name: 'Все отделы'
			});
			this.item.groups_all = true;
		}
	},
	methods: {
		onSelect(selectedOption) {
			if(selectedOption.id == 0) {
				this.selectAll();
				//this.$refs.group_select.close();
			}
		},
		selectAll() {
			this.local_groups = [];
			// this.item.groups.splice(0,this.item.groups.length + 1)
			// this.item.groups.push({
			//     id: 0,
			//     name: 'Все отделы'
			// });
			this.item.groups_all = true;
		},
		onRemove(removedOption) {
			if(removedOption.id == 0) {
				this.local_groups = this.groups;
				this.item.groups_all = false;
			}
		},

	}

}
</script>

<style lang="scss">
.PermissionItem{}
</style>
