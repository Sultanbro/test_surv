<template>
	<div class="ChatNewChat">
		<div class="ChatNewChat-header mb-4">
			<div class="ChatNewChat-title">
				Новый чат
			</div>
			<div
				class="ChatNewChat-close ChatIcon-parent ml-a"
				@click="toggleNewChatDialog"
			>
				<ChatIconSearchClose />
			</div>
		</div>

		<input
			type="text"
			v-model="title"
			class="ChatNewChat-input mb-5"
			placeholder="Название группы"
		>

		<JobtronSearch
			class="mb-5"
			v-model="search"
		/>

		<div class="ChatNewChat-content">
			<AccessSelect
				v-model="selectedTargets"
				:tabs="['Сотрудники', 'Отделы', 'Должности']"
				submit-button="Создать группу"
				:submit-disabled="requestProcess"
				:search-position="''"
				:access-dictionaries="accessDictionaries"
				:search="search"
				@submit="submitGroup"
				class="ChatNewChat-select"
			/>
		</div>
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex'
import JobtronSearch from '@ui/Search'
import AccessSelect from '@ui/AccessSelect/AccessSelect'
import {
	ChatIconSearchClose,
} from '@icons'

export default {
	name: 'ChatNewChat',
	components: {
		JobtronSearch,
		AccessSelect,
		ChatIconSearchClose,
	},
	data(){
		return {
			title: '',
			selectedPrivate: [],
			selectedTargets: [],
			requestProcess: false,
			tab: 'chat',
			search: '',
		}
	},
	computed: {
		...mapGetters([
			'users',
			'positions',
			'profileGroups',
		]),
		positionMap(){
			return this.positions.reduce((result, pos) => {
				result[pos.id] = pos.position
				return result
			}, {})
		},
		accessDictionaries(){
			return {
				users: this.users.reduce((users, user) => {
					if(user.deleted_at) return users
					users.push({
						id: user.id,
						name: `${user.name} ${user.last_name}`,
						avatar: `/users_img/${user.img_url}`,
						position: this.positionMap[user.position_id]
					})
					return users
				}, []),
				profile_groups: this.profileGroups.filter(group => group.active),
				positions: this.positions.filter(pos => !pos.deleted_at).map(pos => ({
					id: pos.id,
					name: pos.position
				})),
			}
		},
		actualUsers(){
			return this.selectedTargets.reduce((result, target) => {
				let group
				switch(target.type) {
				case 1:
					result.push(target)
					break
				case 2:
					group = this.profileGroups.find(group => group.id === target.id)
					if(group?.activeUsers) result.push(...group.activeUsers.map(id => ({id})))
					break
				case 3:
					result.push(...this.users.filter(user => user.position === target.id))
					break
				}
				return result
			}, [])
		}
	},
	mounted(){
		if(!this.users.length) this.loadCompany()
	},
	methods: {
		...mapActions([
			'createChat',
			// 'addMembers',
			// 'removeMembers',
			'loadCompany',
			'toggleNewChatDialog',
		]),
		async submitChat(){
			this.requestProcess = true
			await this.createChat({
				title: this.title,
				description: '',
				members: this.selectedPrivate.map(member => member.id),
			})
			this.requestProcess = false
			this.toggleNewChatDialog()
		},
		async submitGroup(){
			this.requestProcess = true
			await this.createChat({
				title: this.title,
				description: '',
				members: this.actualUsers.map(member => member.id),
			})
			this.requestProcess = false
			this.toggleNewChatDialog()
		}
	}
}
</script>

<style lang="scss">
.ChatNewChat{
	display: flex;
	flex-flow: column nowrap;

	width: 414px;
	height: 720px;
	max-width: 414px;
	max-height: 90vh;
	min-height: 0;
	padding: 20px;

	position: fixed;
	z-index: 20;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);

	background-color: #fff;
	border-radius: 20px;
	box-shadow: 0px 1px 3px rgba(12, 26, 75, 0.05);

	overflow: hidden;
	&-header{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		justify-content: flex-start;
		gap: 15px;
		margin-bottom: 10px;
	}
	&-title{
		font-weight: 500;
		font-size: 17px;
		line-height: 16px;

		letter-spacing: -0.02em;

		color: #152136;
	}
	&-limit{
		font-weight: 500;
		font-size: 13px;
		line-height: 16px;

		letter-spacing: -0.02em;

		color: #A6B7D4;
	}
	// &-close{}
	&-select{
		flex: 0 1 auto;
	}

	&-content{
		display: flex;
		flex-flow: column nowrap;
		flex: 1;
		min-height: 0;
		overflow: scroll;
		overflow: hidden;
	}

	&-input{
		display: block;
		border: none;
		border-bottom: 2px solid #E3EAF7;

		width: 100%;

		padding: 10px;

		font-weight: 400;
		font-size: 14px;
		line-height: 20px;
		color: #A6B7D4;

		text-align: center;
		letter-spacing: -0.02em;
		&:focus{
			border-bottom: 2px solid #3361FF;
		}
	}
}
@media only screen and (max-width: 670px) {
	.ChatNewChat{
		width: 100vw;
		height: 100vh;
		max-width: none;
		max-height: none;
	}
}
</style>
