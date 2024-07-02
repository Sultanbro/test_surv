<template>
	<div class="ChatNewChat">
		<div class="ChatNewChat-header mb-4">
			<div class="ChatNewChat-title">
				Создание нового чата
			</div>
			<div
				class="ChatNewChat-close ChatIcon-parent ml-a"
				@click="toggleNewChatDialog"
			>
				<ChatIconSearchClose />
			</div>
		</div>

		<input
			v-model="title"
			type="text"
			class="ChatNewChat-input mb-3"
			placeholder="Назовите эту группу"
		>

		<div class="ChatNewChat-content">
			<AccessSelect
				v-model="selectedTargets"
				:tabs="['Сотрудники', 'Отделы', 'Должности']"
				submit-button="Создать группу"
				:submit-disabled="requestProcess"
				:search-position="'beforeTabs'"
				:access-dictionaries="accessDictionaries"
				class="ChatNewChat-select"
				@submit="submitGroup"
			/>
		</div>
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex'
import AccessSelect from '@ui/AccessSelect/AccessSelect'
import {
	ChatIconSearchClose,
} from '@icons'

export default {
	name: 'ChatNewChat',
	components: {
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
			'user',
			'users',
			'positions',
			'profileGroups',
			'accessDictionaries',
		]),
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

				return result.filter((user, index, array) => {
					return array.findIndex(u => u.id === user.id) === index;
				})
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
				title: this.title || 'Новый чат',
				description: '',
				members: this.selectedPrivate.map(member => member.id),
			})
			this.requestProcess = false
			this.toggleNewChatDialog()
		},
		async submitGroup(){
			this.requestProcess = true
			await this.createChat({
				title: this.title || 'Новый чат',
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
		// color: #A6B7D4;

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
