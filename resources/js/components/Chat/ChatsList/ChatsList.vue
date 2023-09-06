<template>
	<div
		ref="messengerChats"
		class="ChatsList"
		:class="{'ChatsList_full': fullscreen}"
	>
		<ContextMenu
			v-if="fullscreen"
			v-click-outside="onClickOutside"
			:show="true"
			:x="contextMenuX"
			:y="contextMenuY"
			:parent-element="$refs.messengerChats"
			:class="{
				'ContextMenu_visible': contextMenuVisible
			}"
		>
			<template v-if="contextMenuChat">
				<div
					class="ContextMenu-item wsnw ChatIcon-parent"
					@click="contextTogglePinned"
				>
					<ChatIconPinChat />
					{{ contextMenuChat.pinned ? 'Открепить чат' : 'Закрепить чат' }}
				</div>
				<!-- <div
					class="ContextMenu-item wsnw ChatIcon-parent"
					@click="contextViewProfile"
				>
					<ChatIconViewUser />
					Посмотреть профиль
				</div> -->
				<!-- <div
					v-if="isContextChatAdmin"
					class="ContextMenu-item wsnw ChatIcon-parent"
					@click="contextEdit"
				>
					<ChatIconEditChat />
					Редактировать
				</div> -->
				<div
					class="ContextMenu-item wsnw ChatIcon-parent"
					@click="contextToggleMuted"
				>
					<ChatIconMuteChat />
					{{ contextMenuChat.is_mute ? 'Включить уведомления' : 'Выключить уведомления' }}
				</div>
				<div
					v-if="isContextChatAdmin"
					class="ContextMenu-item ContextMenu-item_red wsnw "
					@click="contextMenuVisible = false; remove(contextMenuChat)"
				>
					<ChatIconDeleteChat />
					Удалить чат
				</div>
				<div
					v-else
					class="ContextMenu-item wsnw ChatIcon-parent"
					@click="contextMenuVisible = false; leftChat(contextMenuChat)"
				>
					<ChatIconHistoryBack />
					Покинуть чат
				</div>
			</template>
		</ContextMenu>
		<template v-if="!isSearchMode || !fullscreen">
			<div
				v-for="item in sortedChats"
				:key="item.id"
				class="messenger__chat-item"
				:class="{'messenger__chat-selected': chat && chat.id === item.id}"
				@click="openChat(item, $event)"
				@contextmenu.prevent="showChatContextMenu($event, item)"
			>
				<ContactItem
					:item="item"
					:fullscreen="fullscreen"
					:current-user-id="user.id"
				/>
			</div>
		</template>
		<template v-else-if="isSearchMode">
			<div
				v-for="item in contacts"
				:key="item.id"
				class="messenger__chat-item"
				:class="{'messenger__chat-selected': chat && chat.id === item.id}"
				:data-test-id="item.id"
				@click="openChat(item, $event)"
				@contextmenu.prevent="showChatContextMenu($event, item)"
			>
				<ContactItem
					:item="item"
					:fullscreen="fullscreen"
					:current-user-id="user.id"
				/>
			</div>
			<div
				v-for="(item, index) in searchMessagesChatsResults"
				:key="index"
				class="messenger__chat-item"
				@click="openChat(item, $event)"
			>
				<ContactItem
					:item="item"
					:fullscreen="fullscreen"
					:current-user-id="user.id"
				/>
			</div>
		</template>
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex'
import ContextMenu from '../ContextMenu/ContextMenu.vue'
import ContactItem from './ContactItem/ContactItem.vue'
import {
	ChatIconPinChat,
	ChatIconDeleteChat,
	ChatIconHistoryBack,
	// ChatIconViewUser,
	// ChatIconEditChat,
	ChatIconMuteChat,
} from '@icons'

export default {
	name: 'ChatsList',
	components: {
		ContextMenu,
		ContactItem,
		ChatIconPinChat,
		ChatIconDeleteChat,
		ChatIconHistoryBack,
		// ChatIconViewUser,
		// ChatIconEditChat,
		ChatIconMuteChat,
	},
	props: {
		fullscreen: {
			type: Boolean,
			default: false
		}
	},
	data() {
		return {
			contextMenuVisible: false,
			contextMenuX: 0,
			contextMenuY: 0,
			contextMenuChat: null
		}
	},
	computed: {
		...mapGetters([
			'sortedChats',
			'chat',
			'user',
			'contacts',
			'searchMessagesChatsResults',
			'isSearchMode',
			'isOpen',
		]),
		isContextChatAdmin(){
			return this.contextMenuChat?.owner_id === this.user.id
		}
	},
	methods: {
		...mapActions([
			'loadChat',
			'toggleMessenger',
			'leftChat',
			'pinChat',
			'unpinChat',
			'muteChat',
			'unmuteChat',
			'removeChat',
			'setLoading',
			'toggleInfoPanel',
		]),
		openChat(chat, event) {
			event.stopPropagation();
			this.contextMenuVisible = false;
			if (!this.chat || this.chat.id !== chat.id) {
				this.setLoading(true);
				this.loadChat({chatId: chat.id, callback: () => {
					this.setLoading(false);
				}});
			}
			if (!this.isOpen) {
				this.toggleMessenger();
			}
		},
		remove(chat) {
			this.$root.$emit('messengerConfirm', {
				title: 'Удалить чат?',
				message: 'Вы уверены, что хотите удалить чат ' + chat.title + '?',
				button: {
					yes: 'Удалить',
					no: 'Отмена'
				},
				callback: confirm => {
					if (confirm) {
						this.removeChat(chat);
					}
				}
			});
		},
		showChatContextMenu(event, chat) {
			this.contextMenuChat = chat;
			this.$nextTick(() => {
				this.contextMenuX = event.clientX;
				this.contextMenuY = event.clientY;
				this.contextMenuVisible = true;
			})
		},
		contextTogglePinned(){
			this.contextMenuVisible = false
			if(this.contextMenuChat.pinned) return this.unpinChat(this.contextMenuChat)
			this.pinChat(this.contextMenuChat)
		},
		contextViewProfile(){
			this.contextMenuVisible = false
			this.$toast('Функционал в разработке')
		},
		contextEdit(){
			this.contextMenuVisible = false
			this.toggleInfoPanel()
		},
		contextToggleMuted(){
			this.contextMenuVisible = false
			if(this.contextMenuChat.is_mute) return this.unmuteChat(this.contextMenuChat.id)
			this.muteChat(this.contextMenuChat.id)
		},
		onClickOutside(){
			this.contextMenuVisible = false
		}
	}
}
</script>

<style lang="scss">
.ChatsList{
	&_full{
		padding: 0 0.7rem;
	}
}

.messenger__chat-item:hover {
  background: #F5F8FC;
}

/*noinspection CssUnusedSymbol*/
.messenger__chat-selected {
  /* color: #fff !important;
  background: #5d8ce7 !important; */
	background: #F5F8FC;
}

/*noinspection CssUnusedSymbol*/
.messenger__chat-item {
  align-items: center;
  display: flex;
  flex: 1 1 100%;
  padding: 8px;
  position: relative;
  transition: background-color .3s cubic-bezier(.25, .8, .5, 1);
}
</style>
