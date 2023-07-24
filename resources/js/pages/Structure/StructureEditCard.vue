<template>
	<div
		class="edit-card"
		ref="editCard"
	>
		<div class="edit-card-header">
			<p class="edit-card-title">
				Редактировать блок
			</p>
			<span
				class="edit-card-close"
				@click="$emit('close', false)"
			>x</span>
		</div>
		<div class="edit-card-body">
			<multiselect
				v-model="departmentName"
				:options="groupOptions"
				taggable
				placeholder="Отдел \ департамент \ подразделение"
				track-by="id"
				label="name"
				@tag="tagName"
				@select="selectName"
			/>
			<div class="collapse-block">
				<p
					class="collapse-item"
					v-b-toggle.collapse-director
				>
					<i class="fa fa-plus" /> Руководитель
				</p>
				<b-collapse id="collapse-director">
					<multiselect
						v-model="position"
						:options="positions"
						track-by="id"
						label="name"
						placeholder="Должность"
					/>
					<multiselect
						v-model="director"
						:options="users"
						label="name"
						track-by="id"
						placeholder="Выберите руководителя"
						class="multiselect-users mt-3"
					>
						<template #singleLabel>
							{{ director.name }} {{ director.last_name }}
						</template>
						<template #option="props">
							<img
								:src="props.option.avatar"
								class="user-image"
								alt="photo"
							>
							<div class="user-full-name">
								{{ props.option.name }} {{ props.option.last_name }}
							</div>
						</template>
					</multiselect>
					<b-form-textarea
						v-model="result"
						class="mt-3"
					/>
				</b-collapse>
			</div>
			<b-form-checkbox
				v-model="autoUsers"
				switch
			>
				Автоматически подтягивать сотрудников
			</b-form-checkbox>
			<div
				v-if="!autoUsers"
				class="collapse-block"
			>
				<p
					class="collapse-item"
					v-b-toggle.collapse-users
				>
					<i class="fa fa-plus" /> Сотрудники
				</p>
				<b-collapse id="collapse-users">
					<multiselect
						v-model="usersList"
						:options="users"
						label="id"
						track-by="name"
						placeholder="Выберите сотрудников"
						:close-on-select="false"
						class="multiselect-users"
						:multiple="true"
					>
						<template #option="props">
							<img
								class="user-image"
								:src="props.option.avatar"
								alt="photo"
							>
							<div class="user-full-name">
								{{ props.option.name }}
							</div>
						</template>
						<template #selection="{ values, isOpen }">
							<span
								class="multiselect__single"
								v-if="values.length"
								v-show="!isOpen"
							>Выбрано сотрудников: {{ values.length }}</span>
						</template>
					</multiselect>
				</b-collapse>
			</div>
			<div class="collapse-block">
				<p
					class="collapse-item"
					v-b-toggle.collapse-more
				>
					<i class="fa fa-plus" /> Дополнительные настройки
				</p>
				<b-collapse id="collapse-more">
					<div class="d-flex justify-content-between aic">
						<label class="select-color">
							<span class="label">Цвет блока</span>
							<span class="circle-picker">
								<input
									v-model="bgColor"
									type="color"
								>
							</span>
						</label>
						<b-form-group
							class="custom-switch custom-switch-sm"
							id="input-group-4"
						>
							<b-form-checkbox
								v-model="group"
								switch
							>
								Группировать отделы
							</b-form-checkbox>
						</b-form-group>
					</div>
				</b-collapse>
			</div>
		</div>
		<div class="edit-card-footer">
			<div class="d-flex align-items-center">
				<button
					class="btn btn-primary"
					@click="saveDepartment"
				>
					Сохранить
				</button>
				<button
					class="btn btn-light ml-2"
					@click="$emit('close', false)"
				>
					Отмена
				</button>
			</div>
			<button
				class="btn btn-remove"
				@click="deleteDepartment"
			>
				<i class="fa fa-trash" />
			</button>
		</div>
	</div>
</template>

<script>
import {mapState, mapActions} from 'pinia'
import {useStructureStore} from '@/stores/Structure.js'

export default {
	name: 'StructureEditCard',
	props: {
		card: {
			type: Object,
			default: null
		},
		selectedUsers: {
			type: Array,
			default: () => []
		},
		users: {
			type: Array,
			default: () => []
		},
		departmentsList: {
			type: Array,
			default: () => []
		},
		positions: {
			type: Array,
			default: () => []
		},
	},
	data() {
		return {
			departmentName: this.card.group_id ? this.departmentsList.find(opt => opt.id === this.card.group_id) : [{
				id: null,
				name: this.card.name,
			}],
			director: this.card.manager ? this.users.find(user => user.id === this.card.manager.user_id) : '',
			usersList: this.selectedUsers,
			result: this.card.description || '',
			position: this.card.manager ? this.positions.find(pos => pos.id === this.card.manager.position_id) : '',
			group: !!this.card.is_group || false,
			autoUsers: !!this.card.status || false,
			bgColor: this.card.color || '#d0def5',
			nameTag: this.card.group_id ? [] : [{
				id: null,
				name: this.card.name,
			}],
		}
	},
	computed: {
		...mapState(useStructureStore, []),
		groupOptions(){
			return [
				...this.nameTag,
				...this.departmentsList,
			]
		}
	},
	mounted() {
		const cardRect = this.$refs.editCard.getBoundingClientRect()
		const topDiff = Math.max(0, 628 - (window.innerHeight - cardRect.top)) + 20
		const leftDiff = Math.max(0, 50 - cardRect.left) + 60

		this.$refs.editCard.style.top = `-${topDiff}px`
		this.$refs.editCard.style.right = `-${leftDiff}px`
	},
	methods: {
		...mapActions(useStructureStore, ['createCard', 'updateCard', 'deleteCard']),
		async saveDepartment() {
			const isGroup = this.departmentName && this.departmentName.id
			const hasName = this.nameTag.length
			const hasPosition = this.position && this.position.id
			const hasManager = this.director && this.director.id

			if(!(isGroup || hasName)) return this.$toast.error('Укажите отдел или название департамента')
			if(!hasManager) return this.$toast.error('Укажите руководителя')
			if(!hasPosition) return this.$toast.error('Укажите должность руководителя')

			const saveData = {
				id: this.card.id > 0 ? this.card.id : 0,
				parent_id: this.card.parent_id,
				group_id: isGroup ? this.departmentName.id : null,
				description: this.result,
				color: this.bgColor,
				user_ids: [this.director.id, ...this.usersList.map(user => user.id)],
				position_id: this.position.id,
				manager_id: this.director.id,
				status: this.autoUsers,
				is_group: this.group,
			}

			if(!isGroup && hasName){
				saveData.name = this.nameTag[0].name
			}

			try {
				const data = await this[this.card.id > 0 ? 'updateCard' : 'createCard'](saveData)
				if(data) this.$toast.success('Карточка сохранена')

				this.$emit('close')
			}
			catch (error) {
				this.$toast.error('Карточка не сохранена')
			}
		},
		deleteDepartment() {
			try {
				const data = this.deleteCard(this.card.id)
				if(data) this.$toast.success('Карточка удалена')

				this.$emit('close')
			}
			catch (error) {
				this.$toast.error('Карточка не удалена')
			}
		},
		tagName(search){
			this.nameTag = [{
				id: null,
				name: search,
			}]
			this.departmentName = {
				id: null,
				name: search,
			}
		},
		selectName(option){
			if(!option.id) return
			this.nameTag = []
		}
	}
}
</script>
