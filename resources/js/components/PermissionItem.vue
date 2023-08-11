<template>
	<!-- eslint-disable vue/no-mutating-props -->
	<b-tr>
		<b-td class="person">
			<superselect
				:values="item.targets"
				class="w-full single"
			/>
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

export default {
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
	},
	data() {
		return {
			local_groups: [],
			local_roles: [],
		}
	},
	watch: {
		item: {
			deep: true,
			handler () {
				this.$emit('updated');
			}
		},
	},
	created() {
		this.local_groups = this.groups;
		this.local_roles = this.roles;
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
