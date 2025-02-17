<template>
	<div class="StructureEditCard">
		<div class="edit-card">
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
						v-b-toggle.collapse-director
						class="collapse-item"
					>
						<i class="fa fa-plus" /> Руководитель
					</p>
					<b-collapse id="collapse-director">
						<multiselect
							v-model="position"
							:options="posOptions"
							track-by="id"
							label="name"
							placeholder="Должность"
						/>
						<multiselect
							v-model="director"
							:options="usersAndBlank"
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
							v-if="director && director.id === 0"
							v-model="vacantDescription"
							class="mt-3"
							placeholder="Опишите условия на которых можно занять эту должность"
						/>
						<b-form-textarea
							v-model="result"
							class="mt-3"
							placeholder="Результат"
						/>
					</b-collapse>
				</div>
				<!-- Сотрудники -->
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
						v-b-toggle.collapse-users
						class="collapse-item"
					>
						<i class="fa fa-plus" /> Сотрудники
					</p>
					<b-collapse id="collapse-users">
						<AccessSelectFormControl
							:items="accessList"
							@click="isAccessSelect = true"
						/>
					</b-collapse>
				</div>
				<!-- Дополнительные настройки -->
				<div class="collapse-block">
					<p
						v-b-toggle.collapse-more
						class="collapse-item"
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
								id="input-group-4"
								class="custom-switch custom-switch-sm"
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

			<!-- footer -->
			<div class="edit-card-footer">
				<div class="d-flex align-items-center">
					<button
						:disabled="card.parent_id < 0"
						:title="card.parent_id < 0 ? 'Сохраните вышестоящую карточку' : ''"
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
					v-if="card.parent_id"
					class="btn btn-remove"
					@click="deleteDepartment"
				>
					<i class="fa fa-trash" />
				</button>
			</div>
		</div>

		<!-- fixed -->
		<JobtronOverlay
			v-if="isAccessSelect"
			:z="10003"
			@close="isAccessSelect = false"
		>
			<AccessSelect
				v-model="accessList"
				:tabs="['Сотрудники', 'Отделы', 'Должности']"
				:submit-button="''"
				:access-dictionaries="{
					users,
					profile_groups: departmentsList,
					positions
				}"
				absolute
			/>
		</JobtronOverlay>
		<a
			ref="createPosLink"
			class="hidden"
			target="_blank"
			href="/timetracking/settings?tab=2#nav-home"
		/>
		<div
			v-if="editedCard"
			class="backdrop-structure-area"
			@click="closeEditCard"
		/>
	</div>
</template>

<script>
import {mapState, mapActions} from 'pinia'
import {useStructureStore} from '@/stores/Structure.js'
import AccessSelect from '@ui/AccessSelect/AccessSelect.vue'
import AccessSelectFormControl from '@ui/AccessSelect/AccessSelectFormControl.vue'
import JobtronOverlay from '@ui/Overlay.vue'

const DESC_DIVIDER = '◕◕'

export default {
	name: 'StructureEditCard',
	components: {
		AccessSelect,
		AccessSelectFormControl,
		JobtronOverlay,
	},
	props: {
		card: {
			type: Object,
			default: null
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
			director: this.card.is_vacant
				? {
					id: 0,
					name: 'Вакантная',
					/* eslint-disable-next-line camelcase */
					last_name: 'позиция',
					avatar: '/static/img/user.png',
				}
				: this.card.manager
					? this.users.find(user => user.id === this.card.manager.user_id)
					: '',
			result: (this.card.description || '').split(DESC_DIVIDER)[0],
			position: this.card.manager && this.card.manager.position_id ? this.positions.find(pos => pos.id === this.card.manager.position_id) : '',
			group: !!this.card.is_group || false,
			autoUsers: !!this.card.status || false,
			bgColor: this.card.color || '#d0def5',
			nameTag: this.card.group_id ? [] : [{
				id: null,
				name: this.card.name,
			}],
			accessList: this.card.users.reduce((result, u) => {
				if(this.card.manager && this.card.manager.user_id === u.id) return result
				const user = this.users.find(user => user.id === u.id)
				if(user) result.push({...user, type: 1, name: `${user.name} ${user.last_name}`})
				return result
			}, []),
			isAccessSelect: false,
			vacantDescription: (this.card.description || DESC_DIVIDER).split(DESC_DIVIDER)[1] || '',
		}
	},
	computed: {
		...mapState(useStructureStore, ['editedCard']),
		groupOptions(){
			return [
				...this.nameTag,
				...this.departmentsList,
			]
		},
		posOptions(){
			return [
				...this.positions,
				{
					id: 0,
					name: '+ Создать новую должность'
				}
			]
		},
		usersAndBlank(){
			return [
				{
					id: 0,
					name: 'Вакантная',
					/* eslint-disable-next-line camelcase */
					last_name: 'позиция',
					avatar: '/static/img/user.png',
				},
				...this.users,
			]
		}
	},
	watch: {
		position(){
			if(this.position && this.position.id === 0){
				this.$refs.createPosLink.click()
			}
		},
		departmentName(value){
			if(value?.id) this.autoManager()
		}
	},
	mounted() {},
	beforeDestroy() {},
	methods: {
		...mapActions(useStructureStore, [
			'createCard',
			'updateCard',
			'deleteCard',
			'closeEditCard',
		]),
		autoManager(){
			if(!this.users) return
			const manager = this.users.find(user => {
				if(!user.profile_group) return false
				const group = user.profile_group.find(group => group.id === this.departmentName.id)
				if(group) return group.is_head
				return false
			})
			if(manager) {
				this.director = manager
				this.position = this.positions.find(pos => pos.id === manager.position_id)
			}
		},
		async saveDepartment() {
			const isGroup = this.departmentName && this.departmentName.id
			const hasName = this.nameTag.length
			// const hasPosition = this.position && this.position.id

			if(!(isGroup || hasName)) return this.$toast.error('Укажите отдел или название департамента')
			// if(!hasPosition) return this.$toast.error('Укажите должность руководителя')

			/* eslint-disable camelcase */
			const saveData = {
				id: this.card.id,
				parent_id: this.card.parent_id,
				group_id: isGroup ? this.departmentName.id : null,
				description: this.result + DESC_DIVIDER + this.vacantDescription,
				color: this.bgColor,
				users: [
					...this.getUsers()
				].filter(this.unique),
				status: this.autoUsers,
				is_group: this.group,
				manager: {
					user_id: null,
					position_id: this.position?.id,
				},
				is_vacant: !!(this.director && this.director.id === 0)
			}

			if(this.director?.id){
				saveData.users.push({id: this.director.id})
				saveData.manager.user_id = this.director.id
			}
			else if (this.director){
				saveData.is_vacant = true
			}
			/* eslint-enable camelcase */

			if(!isGroup && hasName){
				saveData.name = this.nameTag[0].name
			}

			this.closeEditCard()
			try {
				const data = await this[this.card.id > 0 ? 'updateCard' : 'createCard'](saveData)
				if(data) this.$toast.success('Карточка сохранена')
			}
			catch (error) {
				this.$toast.error('Карточка не сохранена')
			}
		},
		async deleteDepartment() {
			if(!confirm('Удалить?')) return
			try {
				const data = await this.deleteCard(this.card.id)
				if(data) this.$toast.success('Карточка удалена')

				this.closeEditCard()
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
		},
		getUsers(){
			return this.accessList.reduce((result, item) => {
				switch(item.type){
				case 1:
					result.push({ id: item.id })
					break
				case 2:
					this.users.forEach(user => {
						if(!user.profile_group) return
						const group = user.profile_group.find(group => group.id === item.id)
						if(group) result.push({ id: user.id })
					})
					break
				case 3:
					this.users.forEach(user => {
						if(user.position_id === item.id) result.push({ id: user.id })
					})
					break
				}
				return result
			}, [])
		},
		unique(value, index, array){
			return array.findIndex(item => item.id === value.id) === index;
		}
	}
}
</script>
