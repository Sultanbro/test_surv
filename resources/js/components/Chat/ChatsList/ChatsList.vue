<template>
	<div ref="messengerChats">
		<ContextMenu
			:show="contextMenuVisible"
			:x="contextMenuX"
			:y="contextMenuY"
			:parent-element="$refs.messengerChats"
		>
			<template v-if="contextMenuChat">
				<a
					v-if="contextMenuChat.pinned"
					class="messenger__context-item"
					href="javascript:"
					@click="contextMenuVisible = false; unpinChat(contextMenuChat)"
				>Открепить чат</a>
				<a
					v-else
					class="messenger__context-item"
					href="javascript:"
					@click="contextMenuVisible = false; pinChat(contextMenuChat)"
				>Закрепить чат</a>
				<a
					v-if="contextMenuChat.owner_id === user.id"
					class="messenger__context-item"
					href="javascript:"
					@click="contextMenuVisible = false; remove(contextMenuChat)"
				>Удалить чат</a>
				<a
					v-else
					class="messenger__context-item"
					href="javascript:"
					@click="contextMenuVisible = false; leftChat(contextMenuChat)"
				>Покинуть чат</a>
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
				/>
			</div>
		</template>
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex'
import ContextMenu from '../ContextMenu/ContextMenu.vue'
import ContactItem from './ContactItem/ContactItem.vue'

export default {
	name: 'ChatsList',
	components: {
		ContextMenu,
		ContactItem,
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
		])
	},
	methods: {
		...mapActions([
			'loadChat',
			'toggleMessenger',
			'leftChat',
			'pinChat',
			'unpinChat',
			'removeChat',
			'setLoading'
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
			this.contextMenuVisible = true;
			this.contextMenuX = event.clientX;
			this.contextMenuY = event.clientY;
			this.contextMenuChat = chat;
		}
	}
}
</script>

<style>
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
