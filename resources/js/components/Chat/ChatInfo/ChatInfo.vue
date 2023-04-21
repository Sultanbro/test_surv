<template>
	<div class="ChatInfo">
		<ConversationHeaderMobile class="ChatInfo-header mb-4">
			<template #left>
				<div
					class="ConversationHeaderMobile-icon ChatIcon-parent"
					@click="isEdit ? toggleEdit() : toggleInfoPanel()"
				>
					<ChatIconBack />
				</div>
			</template>

			<template #right>
				<div
					v-if="isEdit"
					class="ConversationHeaderMobile-icon ChatIcon-parent"
					@click="toggleEdit"
				>
					<ChatIconSearchClose />
				</div>
				<div
					v-else-if="isAdmin"
					class="ConversationHeaderMobile-icon ChatIcon-parent"
					@click="toggleEdit"
				>
					<ChatIconEdit />
				</div>
				<div v-else />
			</template>

			<!-- Аватар -->
			<div class="ChatInfo-logo">
				<InputFile
					v-if="isEdit && !chat.private"
					accept="image/*"
					@change="onChangeAvatar"
				>
					<JobtronAvatar
						v-if="chat.image"
						:image="avatar"
						:title="chat.title"
						:size="120"
					/>
					<div
						v-else
						class="ChatInfo-nologo"
					>
						<IconChatNoImage />
					</div>
				</InputFile>
				<JobtronAvatar
					v-else
					:image="avatar"
					:title="chat.title"
					:size="120"
				/>
			</div>

			<!-- инфо о чате -->
			<template #after>
				<div
					v-if="!isEdit"
					class="ChatInfo-title"
				>
					{{ chat.title }}
				</div>
				<input
					v-else
					type="text"
					class="ChatInfo-titleInput"
					v-model="chatTitle"
				>
				<div
					v-if="!isEdit && !chat.private"
					class="ChatInfo-users"
				>
					{{ chat.users.length }} {{ pluralForm(chat.users.length, ['участник', 'участника', 'участников']) }}
				</div>
				<div
					v-if="false"
					class="ChatInfo-actions"
				>
					<div
						class="ChatInfo-action ChatIcon-parent"
						:class="{'ChatIcon-active': !chat.muted}"
						:title="chat.muted ? 'Включить уведомления' : 'Выключить уведомления'"
						@click="toggleNotifications"
					>
						<ChatIconBell />
					</div>
					<div
						class="ChatInfo-action ChatIcon-parent"
						:class="{'ChatIcon-active': chat.pinned}"
						:title="chat.pinned ? 'Открепить' : 'Закрепить'"
						@click="togglePinned"
					>
						<ChatIconGroupPin />
					</div>
					<div
						class="ChatInfo-action ChatIcon-parent"
						title="Архив сообщений"
						@click="gotoSearch"
					>
						<ChatIconSearchMessages />
					</div>
				</div>
			</template>
		</ConversationHeaderMobile>

		<!-- удаление пользователей -->
		<div
			v-if="isEdit"
			class="ChatInfo-content px-5"
		>
			<div
				v-if="!chat.private"
				class="ChatInfo-editHeader"
			>
				Участники группы
				<ChatIconUserAdd
					class="pointer"
					@click="onUserAdd"
				/>
			</div>
			<JobtronSearch
				v-model="editSearch"
				class="mb-4"
			/>
			<ChatUserList
				:users="editUsersList"
				:owner="isOwner"
				:actions="{ remove: true }"
				@remove="onRemove"
			/>
		</div>

		<!-- список пользователей или файлов -->
		<div
			v-else
			class="ChatInfo-content px-5"
		>
			<div
				v-if="!chat.private"
				class="ChatInfo-userAdd ChatIcon-parent"
				@click="onUserAdd"
			>
				<div class="ChatInfo-userAdd-icon">
					<ChatIconUserAdd />
				</div>
				<div class="ChatInfo-userAdd-text">
					Добавить участника
				</div>
			</div>
			<ChatUserList
				:users="chat.users"
				:owner="isOwner"
			/>
		</div>

		<!-- Действия с чатом -->
		<div
			v-if="isEdit && isOwner"
			class="ChatInfo-footer px-5 py-4"
		>
			<div
				v-if="isOwner"
				class="ChatInfo-delete ChatIcon-active_red ml-a"
				@click="updateChatTitle"
			>
				Удалить чат
				<ChatIconHistoryDelete />
			</div>
		</div>
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import ConversationHeaderMobile from '../MessengerConversation/ConversationHeader/ConversationHeaderMobile'
// import ConversationSearchFilter from '../MessengerConversation/ConversationSearch/ConversationSearchFilter'
import JobtronAvatar from '@ui/Avatar'
import JobtronSearch from '@ui/Search'
import InputFile from '@ui/InputFile'
import ChatUserList from './ChatUserList'
import { pluralForm } from '@/composables/pluralForm'
import {
	ChatIconSearchClose,
	ChatIconBack,
	ChatIconEdit,
	ChatIconBell,
	ChatIconGroupPin,
	ChatIconSearchMessages,
	ChatIconUserAdd,
	ChatIconHistoryDelete,
	IconChatNoImage,
} from '@icons'

export default {
	name: 'ChatInfo',
	components: {
		ConversationHeaderMobile,
		// ConversationSearchFilter,
		ChatIconSearchClose,
		ChatIconBack,
		ChatIconEdit,
		ChatIconBell,
		ChatIconGroupPin,
		ChatIconSearchMessages,
		ChatIconUserAdd,
		ChatIconHistoryDelete,
		IconChatNoImage,
		JobtronAvatar,
		JobtronSearch,
		InputFile,
		ChatUserList,
	},
	data(){
		return {
			isEdit: false,
			searchFilesFilter: 'users',
			editSearch: '',
			chatTitle: this.chat?.title || '',
			editTitleAntispamTimer: null,
		}
	},
	computed: {
		...mapGetters([
			'chat',
			'user',
			'isChatSearchMode',
		]),
		editUsersList(){
			if(!this.editSearch) return this.chat.users
			const query = this.editSearch.toLowerCase()
			return this.chat.users.filter(user => {
				return ~`${user.name} ${user.last_name}`.toLowerCase().indexOf(query)
			})
		},
		isAdmin() {
			return this.chat.users.find(user => user.id === this.user.id).pivot.is_admin;
		},
		isOwner() {
			return this.chat.owner_id === this.user.id
		},
		avatar(){
			if(this.chat.private) return '/users_img/' + this.chat.second_user.img_url
			return this.chat.image?.replace('/storage', '')
		}
	},
	watch: {
		chat(){
			this.chatTitle = this.chat?.title || ''
		},
		chatTitle(){
			clearTimeout(this.editTitleAntispamTimer)
			this.editTitleAntispamTimer = setTimeout(() => {
				this.updateChatTitle()
			}, 3000)
		}
	},
	methods: {
		...mapActions([
			'toggleInfoPanel',
			'pinChat',
			'unpinChat',
			'toggleChatSearchMode',
			'editChatTitle',
			'removeMembers',
			'uploadChatAvatar',
			'toggleAddUserDialog',
		]),
		toggleEdit(){
			if(!this.isEdit) this.chatTitle = this.chat?.title || ''
			this.isEdit = !this.isEdit
		},
		pluralForm,
		toggleNotifications(){
			this.$toast('Функционал в разработке')
		},
		togglePinned(){
			if(this.chat.pinned) return this.unpinChat(this.chat)
			this.pinChat(this.chat)
		},
		gotoSearch(){
			this.toggleInfoPanel()
			if(!this.isChatSearchMode) this.toggleChatSearchMode()
		},
		updateChatTitle(){
			if(this.chatTitle === this.chat.title) return
			this.chat.title = this.chatTitle
			this.editChatTitle()
		},
		onRemove(userId){
			this.removeMembers([
				{id: userId}
			])
		},
		onUserAdd(){
			this.toggleAddUserDialog()
		},
		onChangeAvatar(files){
			if(files && files[0]){
				this.uploadChatAvatar(files[0])
			}
		}
	}
}
</script>

<style lang="scss">
.ChatInfo{
	display: flex;
	flex-flow: column nowrap;

	width: 414px;
	max-width: 414px;
	height: 720px;
	max-height: 90vh;

	position: fixed;
	z-index: 20;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);

	background-color: #fff;
	border-radius: 20px;
	box-shadow: 0px 1px 3px rgba(12, 26, 75, 0.05);
	&-header{
		flex: 0;
		border-radius: 20px 20px 0 0;
	}
	&-logo{
		margin-top: 60px;
		margin-bottom: 30px;
	}
	&-nologo{
		display: inline-flex;
		align-items: center;
		justify-content: center;

		width: 120px;
		height: 120px;

		border-radius: 50%;

		position: relative;
		background-color: #E1EAF8;
	}
	&-title{
		margin-bottom: 15px;
		font-weight: 600;
		font-size: 20px;
		line-height: 16px;
		text-align: center;
		letter-spacing: -0.01em;

		color: #13223F;
	}
	&-titleInput{
		display: block;

		width: 100%;
		padding: 0px 0px 20px;
		margin-bottom: 10px;
		border-bottom: 1px solid #3361FF;

		text-align: center;
		font-weight: 500;
		font-size: 20px;
		line-height: 16px;
		letter-spacing: -0.02em;

		color: #152136;
		background-color: #f5f8fc;
	}
	&-editHeader{
		display: flex;
		align-items: center;
		justify-content: space-between;

		margin-bottom: 1rem;

		font-weight: 500;
		font-size: 17px;
		line-height: 16px;

		letter-spacing: -0.01em;

		color: #152136;
	}
	&-users{
		margin-bottom: 25px;
		font-weight: 400;
		font-size: 13px;
		line-height: 14px;
		text-align: center;
		letter-spacing: -0.02em;

		color: #8DA0C1;
	}
	&-actions{
		display: flex;
		flex-flow: row nowrap;
		gap: 5px;
		align-items: stretch;
		justify-content: stretch;
	}
	&-action{
		display: flex;
		align-items: center;
		justify-content: center;
		flex: 1;

		padding: 12px 23px;

		cursor: pointer;
		border-radius: 4px;
		background: #FFFFFF;
		box-shadow: 0px 1px 3px rgba(12, 26, 75, 0.05);
	}
	&-content{
		flex: 1;
		min-height: 200px;
		overflow-y: auto;
	}
	&-userAdd{
		display: flex;
		align-items: center;
		justify-content: flex-start;
		gap: 15px;

		margin-bottom: 20px;

		cursor: pointer;
		user-select: none;

		&-icon{
			display: flex;
			align-items: center;
			justify-content: center;

			width: 40px;
			height: 40px;
		}
		&-text{
			margin-top: -4px;

			font-weight: 600;
			font-size: 14px;
			line-height: 16px;

			letter-spacing: -0.02em;

			color: #BFD2F3;
		}
		&:hover{
			.ChatInfo-userAdd-text{
				color: #3361FF;
			}
		}
	}

	&-footer{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		justify-content: space-between;

		border-top: 0.5px solid #A6B7D4;
	}
	// &-save{}
	&-delete{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		gap: 20px;
		color: #FF2C52;
		cursor: pointer;
	}
}
@media only screen and (max-width: 670px) {
	.ChatInfo{
		width: 100vw;
		height: 100vh;
		max-width: none;
		max-height: none;
	}
}
</style>
