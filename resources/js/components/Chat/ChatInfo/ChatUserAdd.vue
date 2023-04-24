<template>
	<div class="ChatUserAdd">
		<div class="ChatUserAdd-header">
			<div class="ChatUserAdd-title">
				Добавить в группу
			</div>
			<div class="ChatUserAdd-limit">
				{{ chat.users.length + actualUsers.length }}/100
			</div>
			<div
				class="ChatUserAdd-close ChatIcon-parent ml-a"
				@click="toggleAddUserDialog"
			>
				<ChatIconSearchClose />
			</div>
		</div>
		<AccessSelect
			v-model="selectedTargets"
			:tabs="['Сотрудники', 'Отделы', 'Должности']"
			:submit-button="''"
			:access-dictionaries="accessDictionaries"
			@input="submitChat"
			class="ChatUserAdd-select"
		/>
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex'
import AccessSelect from '@ui/AccessSelect/AccessSelect'
import {
	ChatIconSearchClose,
} from '@icons'

export default {
	name: 'ChatUserAdd',
	components: {
		AccessSelect,
		ChatIconSearchClose,
	},
	data(){
		return {
			selectedTargets: [],
			requestProcess: false,
		}
	},
	computed: {
		...mapGetters([
			'users',
			'positions',
			'profileGroups',
			'chat',
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
					if(~this.chat.users.findIndex(u => u.id === user.id)) return users
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
			// 'createChat',
			'addMembers',
			// 'removeMembers',
			'loadCompany',
			'toggleAddUserDialog',
		]),
		async submitChat(){
			await this.addMembers(this.actualUsers)
			this.selectedTargets = []
			// this.toggleAddUserDialog()
		}
	}
}
</script>

<style lang="scss">
.ChatUserAdd{
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
}
@media only screen and (max-width: 670px) {
	.ChatUserAdd{
		width: 100vw;
		height: 100vh;
		max-width: none;
		max-height: none;
	}
}
</style>
