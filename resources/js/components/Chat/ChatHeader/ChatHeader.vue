<template>
	<div class="messenger_header">
		<div class="messenger_header-text">
			Сообщения
		</div>
		<div
			class="messenger_header-button"
			@click="openAddMemberModal"
		>
			<ChatIconPlus class="pointer" />
		</div>
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import { ChatIconPlus } from '@icons'
export default {
	name: 'ChatHeader',
	components: {
		ChatIconPlus,
	},
	data(){
		return {
			showAddMemberModal: false,
			selectedTargets: []
		}
	},
	computed: {
		...mapGetters([
			'contacts',
			'newChatContacts',
			'chat',
			'user',
			'users',
			'profileGroups',
			'positions',
		]),
		positionMap(){
			return this.positions.reduce((result, pos) => {
				result[pos.id] = pos.position
				return result
			}, {})
		},
		accessDictionaries(){
			return {
				users: this.users.map(user => ({
					id: user.id,
					name: `${user.name} ${user.last_name}`,
					avatar: user.avatar,
					position: this.positionMap[user.position_id]
				})),
				profile_groups: this.profileGroups,
				positions: this.positions.map(pos => ({
					id: pos.id,
					name: pos.position
				})),
			}
		}
	},
	mounted(){
		this.loadCompany()
	},
	methods: {
		...mapActions([
			'createChat',
			'addMembers',
			'removeMembers',
			'loadCompany'
		]),
		openAddMemberModal(e) {
			e.stopPropagation();
			this.showAddMemberModal = true;
		},
		submitChat() {
			this.selectedTargets.push(this.user)
			const members = this.selectedTargets.filter(item => item.type === 1)
			const title = members.slice(0, 3).map(item => item.name).join(', ');
			this.createChat({
				title: title,
				description: '',
				members: members.map(member => member.id)
			});

			this.showAddMemberModal = false
		},
	}
}
</script>

<style lang="scss">
	.messenger_header{
		display: flex;

		align-items: center;
		justify-content: stretch;
		flex-flow: row nowrap;

		padding: 1.5rem 1.5rem 0.5rem;
		margin-bottom: 1.5rem;

		position: relative;
		z-index: 20;

		&-text{
			flex: 1 1 100%;
			font-weight: 600;
			font-size: 20px;
			line-height: 30px;
			letter-spacing: -0.02em;
		}
		// &-button{}
	}
</style>
