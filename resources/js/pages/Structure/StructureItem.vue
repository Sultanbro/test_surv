<template>
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
				:class="{'no-body': employeesCount === 0 && !manager}"
			>
				<p
					class="StructureItem-contrast department"
					:class="{'is-new': card.isNew}"
				>
					{{ name }}
				</p>

				<!-- кол-во сотрудников -->
				<p
					v-if="employeesCount > 0"
					class="StructureItem-contrast count"
				>
					{{ employeesCount }} сотрудников
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
					@click="editCard(card)"
				/>
			</div>

			<!-- Список сотрудников -->
			<div
				v-if="manager || isVacant || (card.users && card.users.length)"
				class="structure-card-body"
			>
				<template v-if="isVacant">
					<img
						src="/user.png"
						alt="photo"
						class="director-photo"
					>
					<StructureInfo v-if="description[1]">
						<template #default>
							{{ description[1] }}
						</template>
					</StructureInfo>
					<p
						v-if="position"
						class="StructureItem-contrast position"
					>
						{{ position.name }}
					</p>
					<p class="StructureItem-contrast full-name">
						Вакантная позиция
					</p>
				</template>
				<template v-else-if="manager">
					<img
						:src="manager.avatar"
						alt="photo"
						class="director-photo"
					>
					<StructureInfo
						:info="{
							avatar: manager.avatar,
							name: manager.name,
							last_name: manager.last_name,
							birthday: manager.birthday,
							phone: card.parent_id ? manager.phone : '',
							email: manager.email,
						}"
					/>
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
					v-if="(manager || isVacant) && users && users.length"
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
								class="StructureItem-userAvatar"
							>
							<StructureInfo
								:key="'i' + usrIdx"
								:info="{
									avatar: user.avatar,
									name: user.name,
									last_name: user.last_name,
									birthday: user.birthday,
									position: user.position_name,
									email: user.email,
								}"
							/>
						</template>
						<span
							v-if="users.length > 5"
							class="user-group-more"
							@click="showMoreUsers(users)"
						>{{ users.length - 6 }}</span>
					</div>
				</template>
			</div>

			<i
				v-if="isEditMode"
				class="fa fa-plus-circle structure-add"
				:class="{'has-result': description[0]}"
				@click="addNew"
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
				/>
			</div>
		</template>

		<!-- Результаты -->
		<div
			v-if="description[0]"
			class="structure-card-result"
			:style="{ backgroundColor: card.color, width: resultWidth > 0 ? `${resultWidth}px` : null }"
		>
			<p class="StructureItem-contrast result-title">
				Результаты
			</p>
			<p class="StructureItem-contrast result-text">
				{{ description[0] }}
			</p>
		</div>
	</div>
</template>

<script>
import {mapState, mapActions} from 'pinia'
import StructureInfo from './StructureInfo'
import {useStructureStore} from '@/stores/Structure.js'

const DESC_DIVIDER = '◕◕'

export default {
	name: 'StructureItem',
	components: {
		StructureInfo,
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
		}
	},
	computed: {
		...mapState(useStructureStore, [
			'cards',
			'isEditMode',
			'isDemo',
			'demo',
		]),
		children(){
			if(this.isDemo){
				return this.demo.structure.reduce((result, child) => {
					if(child.parent_id === this.card.id){
						result.push(child)
					}
					return result
				}, [])
			}
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
			const manager = this.dictionaries.users.find(user => user.id === this.card.manager.user_id)
			return manager || {
				id: 0,
				name: 'Вакантная',
				last_name: 'позиция',
				avatar: '/user.png',
			}
		},
		position(){
			if(!this.card) return null
			if(!this.card.manager) return null
			return this.dictionaries.positions.find(pos => pos.id === this.card.manager.position_id)
		},
		users(){
			if(this.card.status && this.card.group_id){
				return this.dictionaries.users.filter(user => {
					if(this.localSkip.includes(user.id)) return false
					return !!user.profile_group?.find(group => group.id === this.card.group_id)
				})
			}
			return this.card.users.reduce((result, userPivot) => {
				if(this.localSkip.includes(userPivot.id)) return result
				const user = this.dictionaries.users.find(u => u.id === userPivot.id)
				if(user) result.push(user)
				return result
			}, [])
		},
		employeesCount(){
			return this.children.length + this.users.length
		},
		description(){
			return (this.card.description || DESC_DIVIDER).split(DESC_DIVIDER)
		},
		isVacant(){
			return this.card.is_vacant || (this.manager && this.manager.id === 0)
		},
	},
	mounted() {
		this.drawLines();
	},
	methods: {
		...mapActions(useStructureStore, [
			'addCard',
			'editCard',
			'showMoreUsers'
		]),
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
		color: #ddd !important;
	}
	&-userAvatar{
		&:hover{
			+ .StructureInfo{
				right: -290px !important;
				opacity: 1 !important;
				visibility: visible !important;
			}
		}
		+ .StructureInfo{
			right: -270px;
			opacity: 0;
			visibility: hidden;
		}
	}
}
</style>
