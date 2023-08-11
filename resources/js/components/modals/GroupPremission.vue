<template>
	<div>
		<b-modal
			v-model="openPremissionModal"
			title="Настройка доступа"
			size="lg"
			class="modalle"
			@ok="savePremission"
		>
			Выберите сотрудников, которым будет разрешено редактировать время

			<multiselect
				v-model="group_editors"
				:options="users"
				:multiple="true"
				:close-on-select="false"
				:clear-on-select="false"
				:preserve-search="true"
				placeholder="Выберите"
				label="email"
				track-by="email"
			>
				<template #selection="{ values, isOpen }">
					<span
						v-if="values.length && !isOpen"
						class="multiselect__single"
					>{{ values.length }} выбрано</span>
				</template>
			</multiselect>
		</b-modal>

		<div class="text-right mb-3">
			<a
				href="#"
				class="btn btn-primary rounded"
				@click.prevent="openPremissionModal = true"
			>
				<i class="fa fa-cogs" /> Доступ</a>
		</div>
	</div>
</template>

<script>
/* eslint-disable camelcase */

import {bus} from '../../bus';

export default {
	name: 'GroupPremission',
	props: {
		currentGroup: {
			type: Number,
			default: 0
		},
		page: {
			type: String,
			default: ''
		}
	},
	data() {
		return {
			openPremissionModal: false,
			users: [],
			group_editors: []
		}
	},
	watch: {
		currentGroup() {
			this.loadEditors()
		}
	},
	mounted() {
		bus.$on('checkPremissions', this.checkPremissions)
	},
	created() {
		this.loadEditors()
		this.axios.post('/timetracking/users', {})
			.then(response => {
				this.users = response.data.users
			})

	},
	methods: {
		loadEditors() {
			this.axios.post('/timetracking/reports/get-editors', {
				group_id: this.currentGroup,
				page: this.page
			}).then(response => {
				this.group_editors = response.data
			})
		},
		savePremission() {
			this.axios.post('/timetracking/reports/add-editors', {
				users: this.group_editors,
				group_id: this.currentGroup,
				page: this.page
			}).then(() => {}).catch(error => {
				console.error(error)
			});
			this.openPremissionModal = false
		},
		checkPremissions(activeuserid) {
			let premission = false
			this.group_editors.forEach(editor => {
				if (editor.id == parseInt(activeuserid)) premission = true
			})
			return premission;
		}
	}
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="scss" />
