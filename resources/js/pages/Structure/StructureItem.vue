<template>
	<div
		class="structure-item"
		:class="[{'grouped' : department.group}, 'lvl' + level]"
		:style="{'--half-width' : halfWidth}"
		ref="structureItem"
	>
		<div
			class="structure-card"
			:id="'id-' + department.id"
			:style="'background-color:' + bgColor"
			:class="{'no-result' : !department.hasOwnProperty('departmentChildren')}"
		>
			<div
				class="structure-card-header"
				:class="{'no-body': department.employeesCount === 0 && !department.director}"
			>
				<p
					class="department"
					:class="{'is-new': department.isNew}"
				>
					{{ department.department }}
				</p>
				<p
					class="count"
					v-if="department.employeesCount > 0"
				>
					{{ department.employeesCount }} сотрудников
				</p>
				<p
					class="count"
					v-else
				>
					Нет сотрудников
				</p>
				<i
					class="fa fa-cog structure-edit"
					v-if="editStructure"
					@click="openEditCard"
				/>
			</div>
			<div
				class="structure-card-body"
				v-if="department.director || department.users"
			>
				<template v-if="department.director">
					<img
						:src="department.director.photo"
						alt="photo"
						class="director-photo"
					>
					<div class="additional-info">
						<img
							:src="department.director.photo"
							alt="photo"
							class="addi-director-photo"
						>
						<div class="addi-content">
							<div class="addi-fullname">
								{{ department.director.fullName }}
							</div>
							<p class="addi-item">
								<span>Дата рождения: </span>{{ department.director.birthday }}
							</p>
							<p class="addi-item">
								<span>Телефон: </span> {{ department.director.phone }}
							</p>
							<p class="addi-item addi-email">
								<span>E-mail: </span> {{ department.director.email }}
							</p>
						</div>
					</div>
					<p class="position">
						{{ department.position }}
					</p>
					<p class="full-name">
						{{ department.director.fullName }}
					</p>
				</template>
				<hr
					class="divider-users"
					v-if="department.director && department.users && department.users.length"
				>
				<template v-if="department.users">
					<div class="users-group">
						<template v-for="(user, usrIdx) in department.users">
							<img
								:src="user.photo"
								alt="photo"
								v-if="usrIdx < 6"
								:key="usrIdx"
							>
						</template>
						<span
							class="user-group-more"
							v-if="department.users.length > 5"
							@click="openUsersMore"
						>{{ department.users.length - 6 }}</span>
					</div>
					<StructureUsersMore
						:users="department.users"
						v-if="usersMore && department.users.length > 5"
					/>
				</template>
			</div>
			<i
				v-if="editStructure"
				class="fa fa-plus-circle structure-add"
				:class="{'has-result': department.result}"
				@click="addNew"
			/>
			<StructureEditCard
				v-if="editCard"
				:users="users"
				:department="department"
				:positions="positions"
				:bgc="bgColor"
				:departments-list="departmentsList"
				@close="closeEditCard"
				@save="saveEditCard"
				@delete="deleteDepartment"
			/>
		</div>
		<template v-if="department.hasOwnProperty('departmentChildren')">
			<div
				class="structure-group"
				ref="group"
				:style="[{'--start-line' : startLine}, {'--end-line' : endLine}]"
			>
				<StructureItem
					v-for="(child, index) in department.departmentChildren"
					:department="child"
					:level="level + 1"
					:key="index"
					:bgc="bgColor"
					:users="users"
					:edit-structure="editStructure"
					:positions="positions"
					:departments-list="departmentsList"
					@updateLines="drawLines"
					@isOpenEditCard="isOpenEditCard"
				/>
			</div>
		</template>
		<div
			class="structure-card-result"
			v-if="department.hasOwnProperty('result') && department.result.length > 0"
			:style="{ backgroundColor: bgColor, width: resultWidth > 0 ? `${resultWidth}px` : null }"
		>
			<p class="result-title">
				Результаты
			</p>
			<p class="result-text">
				{{ department.result }}
			</p>
		</div>
		<div
			class="backdrop-structure-area"
			v-if="editCard"
			@click="closeEditCard"
		/>
		<div
			class="backdrop-structure-area"
			v-if="usersMore"
			@click="closeUsersMore"
		/>
	</div>
</template>

<script>
import StructureEditCard from './StructureEditCard';
import StructureUsersMore from './StructureUsersMore';

export default {
	name: 'StructureItem',
	components: {StructureEditCard, StructureUsersMore},
	props: {
		department: Object,
		level: Number,
		editStructure: Boolean,
		departmentsList: Array,
		positions: Array,
		users: Array,
		bgc: String
	},
	data() {
		return {
			startLine: '1px',
			endLine: '2px',
			resultWidth: '0',
			halfWidth: 0,
			structureAddTop: 0,
			usersMore: false,
			editCard: false,
			bgColor: ''
		}
	},
	mounted() {
		this.drawLines();
		this.bgColor = this.department.bgc ? this.department.bgc : this.bgc ? this.bgc : '';
	},
	watch: {
		bgc(val) {
			this.bgColor = val;
		},
	},
	methods: {
		deleteDepartment() {
			this.closeEditCard();
			const parent = this.$parent.department || this.$parent;
			const index = parent[parent.department ? 'departmentChildren' : 'departments'].findIndex(d => d.id === this.department.id);
			if (index !== -1) {
				parent[parent.department ? 'departmentChildren' : 'departments'].splice(index, 1);
			}
			this.$emit('updateLines');
		},
		saveEditCard() {
			this.closeEditCard();
			this.bgColor = this.department.bgc;
			this.$emit('updateLines');
			this.drawLines();
		},
		isOpenEditCard(bool) {
			this.$emit('isOpenEditCard', bool);
		},
		openEditCard() {
			this.editCard = true;
			this.isOpenEditCard(true);
		},
		openUsersMore() {
			this.usersMore = true;
			this.isOpenEditCard(true);
		},
		closeEditCard() {
			this.editCard = false;
			this.isOpenEditCard(false);
		},
		closeUsersMore() {
			this.usersMore = false;
			this.isOpenEditCard(false);
		},
		drawLines() {
			this.$nextTick(() => {
				if(this.$refs.group){
					const children = this.$refs.group.children;
					this.resultWidth = this.$refs.group.offsetWidth - (children.length * 5);
					this.startLine = `${(children[0].offsetWidth / 2) + 8}px`;
					this.endLine = `${(children[children.length - 1].offsetWidth / 2) + 8}px`;
				}
				this.halfWidth = `${this.$refs.structureItem.offsetWidth / 2}px`;
				this.$emit('updateLines');
			})
		},
		addNew() {
			this.showTest = true;
			const obj = {
				id: Math.floor(Math.random() * 10000),
				department: 'Новый департамент',
				employeesCount: 0,
				isNew: true
			};
			this.department.hasOwnProperty('departmentChildren') ? this.department.departmentChildren.push(obj) : this.department.departmentChildren = [obj];
			this.$forceUpdate();
		}
	}
}
</script>
