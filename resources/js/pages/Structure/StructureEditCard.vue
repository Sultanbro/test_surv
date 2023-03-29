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
				:options="departmentsList"
				placeholder="Отдел \ департамент \ подразделение"
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
						placeholder="Должность"
					/>
					<multiselect
						:options="users"
						v-model="director"
						placeholder="Выберите руководителя"
						label="fullName"
						track-by="fullName"
						class="multiselect-users mt-3"
					>
						<template
							slot="option"
							slot-scope="props"
						>
							<img
								class="user-image"
								:src="props.option.photo"
								alt="photo"
							>
							<div class="user-full-name">
								{{ props.option.fullName }}
							</div>
						</template>
					</multiselect>
					<b-form-textarea
						class="mt-3"
						v-model="result"
					/>
				</b-collapse>
			</div>
			<div class="collapse-block">
				<p
					class="collapse-item"
					v-b-toggle.collapse-users
				>
					<i class="fa fa-plus" /> Сотрудники
				</p>
				<b-collapse id="collapse-users">
					<multiselect
						:options="users"
						v-model="usersList"
						placeholder="Выберите сотрудников"
						label="fullName"
						track-by="fullName"
						:close-on-select="false"
						class="multiselect-users"
						:multiple="true"
					>
						<template
							slot="option"
							slot-scope="props"
						>
							<img
								class="user-image"
								:src="props.option.photo"
								alt="photo"
							>
							<div class="user-full-name">
								{{ props.option.fullName }}
							</div>
						</template>
						<template
							slot="selection"
							slot-scope="{ values, isOpen }"
						>
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
							<span class="circle-picker"><input
								type="color"
								v-model="bgColor"
							></span>
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
export default {
	name: 'StructureEditCard',
	props: {
		users: Array,
		department: Object,
		departmentsList: Array,
		positions: Array,
		bgc: String
	},
	data() {
		return {
			departmentName: this.department.department ? this.department.department : '',
			director: this.department.director ? this.department.director : '',
			usersList: this.department.users ? this.department.users : [],
			result: this.department.result ? this.department.result : '',
			position: this.department.position ? this.department.position : '',
			group: this.department.group ? this.department.group : false,
			bgColor: this.bgc ? this.bgc : ''
		}
	},
	mounted() {
		const cardRect = this.$refs.editCard.getBoundingClientRect();
		const topDiff = Math.max(0, 628 - (window.innerHeight - cardRect.top)) + 20;
		const leftDiff = Math.max(0, 50 - cardRect.left) + 60;

		this.$refs.editCard.style.top = `-${topDiff}px`;
		this.$refs.editCard.style.right = `-${leftDiff}px`;
	},
	methods: {
		saveDepartment() {
			this.department.department = this.departmentName;
			this.department.director = this.director;
			this.department.users = this.usersList;
			this.department.result = this.result;
			this.department.position = this.position;
			this.department.group = this.group;
			this.department.bgc = this.bgColor;
			this.department.employeesCount = this.usersList.length;
			this.department.isNew = false;

			this.$emit('save');
		},
		deleteDepartment() {
			this.$emit('delete');
		},
	}
}
</script>
