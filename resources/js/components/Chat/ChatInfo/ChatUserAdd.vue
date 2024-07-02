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
				@click="requestProcess ? () => {} : toggleAddUserDialog()"
			>
				<ChatIconSearchClose />
			</div>
		</div>
		<AccessSelect
			v-model="selectedTargets"
			:tabs="['Сотрудники', 'Отделы', 'Должности']"
			:submit-button="'Добавить в группу'"
			:submit-disabled="requestProcess"
			:access-dictionaries="notInChat"
			:search-position="'beforeTabs'"
			class="ChatUserAdd-select"
			@submit="submitChat"
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
			'user',
			'users',
			'positions',
			'profileGroups',
			'chat',
			'accessDictionaries',
		]),
		notInChat(){
			return {
				...this.accessDictionaries,
				users: this.accessDictionaries.users.filter(user => !~this.chat.users.findIndex(u => u.id === user.id))
			}
		},
		actualUsers(){
			return this.selectedTargets.reduce((result, target) => {
				let group
				switch(target.type) {
				case 1:
					if(target.id !== this.user.id) result.push(target)
					break
				case 2:
					group = this.profileGroups.find(group => group.id === target.id)
					if(group?.users) result.push(...group.users.map(id => ({id})))
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
			this.requestProcess = true
			this.toggleAddUserDialog()
			await this.addMembers(this.actualUsers)
			this.selectedTargets = []
			this.requestProcess = false
		}
	}
}
</script>

<style lang="scss">
.ChatUserAdd{
	display: flex;
	flex-flow: column nowrap;

	width: 414px;
	// height: 720px;
	max-width: 414px;
	max-height: min(90vh, 720px);
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
