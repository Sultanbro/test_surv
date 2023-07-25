<template>
	<!-- eslint-disable vue/no-mutating-props -->
	<div
		class="StructureItem structure-item"
		:class="[{'grouped' : card.is_group}, 'lvl' + level]"
		:style="{'--half-width' : halfWidth}"
		ref="structureItem"
	>
		<!-- Карточка -->
		<div
			class="structure-card"
			:id="'id-' + card.id"
			:style="{ backgroundColor: card.color }"
			:class="{'no-result' : !(children && children.length)}"
		>
			<div
				class="structure-card-header"
				:class="{'no-body': card.employeesCount === 0 && !manager}"
			>
				<p
					class="StructureItem-contrast department"
					:class="{'is-new': card.isNew}"
				>
					{{ name }}
				</p>

				<!-- кол-во сотрудников -->
				<p
					v-if="card.employeesCount > 0"
					class="StructureItem-contrast count"
				>
					{{ card.employeesCount }} сотрудников
				</p>
				<p
					v-else
					class="StructureItem-contrast count"
				>
					Нет сотрудников
				</p>

				<i
					v-if="isEditMode"
					class="fa fa-cog structure-edit"
					@click="openEditCard"
				/>
			</div>

			<!-- Список сотрудников -->
			<div
				v-if="manager || (card.users && card.users.length)"
				class="structure-card-body"
			>
				<template v-if="manager">
					<img
						:src="manager.avatar"
						alt="photo"
						class="director-photo"
					>
					<div class="additional-info">
						<img
							:src="manager.avatar"
							alt="photo"
							class="addi-director-photo"
						>
						<div class="addi-content">
							<div class="addi-fullname">
								{{ manager.name }} {{ manager.last_name }}
							</div>
							<p class="addi-item">
								<span>Дата рождения: </span>{{ $moment(manager.birthday).format('DD.MM.YYYY') }}
							</p>
							<p class="addi-item">
								<span>Телефон: </span> {{ manager.phone }}
							</p>
							<p class="addi-item addi-email">
								<span>E-mail: </span> {{ manager.email }}
							</p>
						</div>
					</div>
					<p
						v-if="position"
						class="StructureItem-contrast position"
					>
						{{ position.name }}
					</p>
					<p class="StructureItem-contrast full-name">
						{{ manager.name }} {{ manager.last_name }}
					</p>
				</template>
				<hr
					v-if="manager && users && users.length"
					class="divider-users"
				>
				<template v-if="users && users.length">
					<div class="users-group">
						<template v-for="(user, usrIdx) in users">
							<img
								v-if="usrIdx < 6"
								:key="usrIdx"
								:src="user.avatar"
								alt="photo"
								:title="`${user.name} ${user.last_name}`"
							>
						</template>
						<span
							v-if="users.length > 5"
							class="StructureItem-contrast user-group-more"
							@click="openUsersMore"
						>{{ users.length - 6 }}</span>
					</div>
					<StructureUsersMore
						v-if="usersMore && users.length > 5"
						:users="users"
					/>
				</template>
			</div>
			<i
				v-if="isEditMode"
				class="fa fa-plus-circle structure-add"
				:class="{'has-result': card.description}"
				@click="addNew"
			/>
			<StructureEditCard
				v-if="editCard"
				:card="card"
				:selected-users="users"
				:users="dictionaries.users"
				:positions="dictionaries.positions"
				:departments-list="dictionaries.profile_groups"
				:level="level"
				@close="closeEditCard"
			/>
		</div>

		<!-- Потомки -->
		<template v-if="children && children.length">
			<div
				class="structure-group"
				ref="group"
				:style="{
					'--start-line' : startLine,
					'--end-line' : endLine,
				}"
			>
				<StructureItem
					v-for="(child, index) in children"
					:key="index"
					:card="child"
					:level="level + 1"
					:dictionaries="dictionaries"
					:skip-users="localSkip"
					@updateLines="drawLines"
					@isOpenEditCard="isOpenEditCard"
				/>
			</div>
		</template>

		<!-- Результаты -->
		<div
			v-if="card.description"
			class="structure-card-result"
			:style="{ backgroundColor: card.color, width: resultWidth > 0 ? `${resultWidth}px` : null }"
		>
			<p class="StructureItem-contrast result-title">
				Результаты
			</p>
			<p class="StructureItem-contrast result-text">
				{{ card.description }}
			</p>
		</div>
		<div
			v-if="editCard"
			class="backdrop-structure-area"
			@click="closeEditCard"
		/>
		<div
			v-if="usersMore"
			class="backdrop-structure-area"
			@click="closeUsersMore"
		/>
	</div>
</template>

<script>
import {mapState, mapActions} from 'pinia'
import StructureEditCard from './StructureEditCard'
import StructureUsersMore from './StructureUsersMore'
import {useStructureStore} from '@/stores/Structure.js'

export default {
	name: 'StructureItem',
	components: {
		StructureEditCard,
		StructureUsersMore,
	},
	props: {
		card: {
			type: Object,
			default: null
		},
		level: {
			type: Number,
			default: 0
		},
		editStructure: Boolean,
		dictionaries: {
			type: Object,
			default: () => ({
				users: [],
				profile_groups: [],
				positions: [],
			})
		},
		skipUsers: {
			type: Array,
			default: () => []
		}
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
		}
	},
	computed: {
		...mapState(useStructureStore, ['cards', 'isEditMode']),
		children(){
			return this.cards.reduce((result, child) => {
				if(child.parent_id === this.card.id){
					result.push(child)
				}
				return result
			}, [])
		},
		localSkip(){
			if(this.card.manager) return [...this.skipUsers, this.card.manager.user_id]
			return this.skipUsers
		},
		name(){
			if(!this.card) return ''
			if(!this.card.group_id) return this.card.name
			const group = this.dictionaries.profile_groups.find(group => group.id === this.card.group_id)
			if(group) return group.name
			return ''
		},
		manager(){
			if(!this.card) return null
			if(!this.card.manager) return null
			return this.dictionaries.users.find(user => user.id === this.card.manager.user_id)
		},
		position(){
			if(!this.card) return null
			if(!this.card.manager) return null
			return this.dictionaries.positions.find(pos => pos.id === this.card.manager.position_id)
		},
		users(){
			return this.card.users.reduce((result, userPivot) => {
				if(this.localSkip.includes(userPivot.id)) return result
				const user = this.dictionaries.users.find(u => u.id === userPivot.id)
				if(user) result.push(user)
				else{
					console.log('not found', userPivot.id)
				}
				return result
			}, [])
		}
	},
	mounted() {
		this.drawLines();
	},
	methods: {
		...mapActions(useStructureStore, ['addCard']),
		deleteDepartment() {
			this.closeEditCard();
			// const parent = this.$parent.department || this.$parent;
			// const index = parent[parent.department ? 'departmentChildren' : 'structure'].findIndex(d => d.id === this.department.id);
			// if (index !== -1) {
			// 	parent[parent.department ? 'departmentChildren' : 'structure'].splice(index, 1);
			// }
			this.$emit('updateLines');
			this.$forceUpdate();
		},
		saveEditCard() {
			this.closeEditCard();
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
				if(this.$refs.structureItem){
					this.halfWidth = `${this.$refs.structureItem.offsetWidth / 2}px`;
				}
				this.$emit('updateLines');
			})
		},
		addNew() {
			// const obj = {
			// 	id: Math.floor(Math.random() * 10000),
			// 	department: 'Новый департамент',
			// 	employeesCount: 0,
			// 	isNew: true
			// };
			// this.department.departmentChildren ? this.department.departmentChildren.push(obj) : this.department.departmentChildren = [obj];
			this.addCard(this.card.id)
			this.$forceUpdate()
		}
	}
}
</script>

<style lang="scss">
.StructureItem{
	&-contrast{
		mix-blend-mode: difference;
		color: gray !important;
	}
}
</style>
