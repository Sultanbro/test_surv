<template>
	<div
		class="messenger__container-scroll"
		ref="messengerContainer"
		id="messenger_container"
		@click="contextMenuVisible = false"
		@scroll="onScroll"
	>
		<!-- Контекст -->
		<ContextMenu
			:show="contextMenuVisible"
			:x="contextMenuX"
			:y="contextMenuY"
			:parent-element="$refs.messengerContainer"
		>
			<div class="messenger__context-menu_reactions">
				<div
					v-for="reaction, key in reactions"
					:key="key"
					class="messenger__context-menu_reaction"
					@click="react(key)"
					v-html="reaction"
				/>
			</div>

			<div
				v-if="contextMenuMessage && user && contextMenuMessage.sender_id === user.id"
				class="ContextMenu-item"
				@click="startEditMessage(contextMenuMessage)"
			>
				Отредактировать
			</div>
			<div
				class="ContextMenu-item"
				@click="citeMessage(contextMenuMessage)"
			>
				Цитировать
			</div>
			<div
				class="ContextMenu-item"
				@click="pinMessage(contextMenuMessage)"
			>
				Закрепить
			</div>
			<div
				class="ContextMenu-item"
				@click="remove(contextMenuMessage)"
			>
				Удалить
			</div>
		</ContextMenu>

		<!-- Сообщения -->
		<div
			id="messenger__messages"
			class="messenger__messages-container"
		>
			<template v-for="(d, i) in messagesMap">
				<!-- дата -->
				<div
					:key="i"
					class="messenger__messages-date"
				>
					<span class="messenger__messages-date-block">{{ d[0].message.created_at | formatDate }}</span>
				</div>

				<!-- сообщения -->
				<template v-for="{message, renderHelper}, index in d">
					<!-- маккер непрочитанных -->
					<div
						v-if="renderHelper.isUnreadFirst"
						:key="'unread' + message.id"
						class="ConversationFeed-startUnread"
					>
						<div class="ConversationFeed-startUnread-text">
							Непрочитанные сообщения
						</div>
					</div>

					<!-- сообщение -->
					<div
						:key="message.id"
						:id="'messenger_message_' + message.id"
						class="messenger__message-wrapper"
						@contextmenu.prevent="!message.event && showChatContextMenu(message, ...arguments)"
					>
						<ConversationServiceMessage
							v-if="message.event"
							:message="message"
						/>
						<ConversationMessage
							v-else
							:message="message"
							@active="activeMessageId = message.id"
							:active="activeMessageId === message.id"
							:helper="renderHelper"
							:last="index === d.length - 1"
							@loadImage="index === d.length - 1 && scrollBottom"
						/>
					</div>

					<!-- Кто посмотрел -->
					<ConversationFeedReaders
						v-if="message.last && message.readers && message.readers.length"
						:key="'readers' + message.id"
						:message="message"
					/>
				</template>
			</template>
		</div>

		<!-- Loader -->
		<div
			class="messenger__loader"
			v-show="this.isLoading"
		>
			<div class="messenger__loader-spinner">
				<div class="messenger__loader-spinner-item" />
				<div class="messenger__loader-spinner-item" />
				<div class="messenger__loader-spinner-item" />
				<div class="messenger__loader-spinner-item" />
			</div>
		</div>
	</div>
</template>

<script>
import ConversationMessage from './ConversationMessage/ConversationMessage.vue';
import ConversationFeedReaders from './ConversationFeedReaders'
import {mapActions, mapGetters} from 'vuex';
import ContextMenu from '../../ContextMenu/ContextMenu.vue';
import ConversationServiceMessage from './ConversationServiceMessage/ConversationServiceMessage.vue';
import moment from 'moment/moment';

export default {
	name: 'ConversationFeed',
	components: {
		ConversationMessage,
		ConversationFeedReaders,
		ConversationServiceMessage,
		ContextMenu,
	},
	data() {
		return {
			contextMenuVisible: false,
			contextMenuTarget: null,
			contextMenuX: 0,
			contextMenuY: 0,
			contextMenuMessage: null,
			activeMessageId: 0,
			reactions: {
				1: '&#128077;',
				2: '&#128078;',
				3: '&#10004;',
				4: '&#10006;',
				5: '&#10067;',
			}
		}
	},
	computed: {
		...mapGetters([
			'messagesMap',
			'messages',
			'user',
			'messagesOldEndReached',
			'messagesNewEndReached',
			'scrollingPosition',
			'messagesLoading',
			'isLoading',
		]),
	},
	updated() {
		if (this.scrollingPosition !== -1) {
			this.scroll();
		}
	},
	methods: {
		...mapActions([
			'startEditMessage',
			'citeMessage',
			'deleteMessage',
			'pinMessage',
			'reactMessage',
			'loadMoreNewMessages',
			'loadMoreOldMessages',
			'requestScroll',
		]),
		scroll() {
			this.$nextTick(function () {
				let mc = document.getElementById('messenger_container');
				mc.scrollTop = mc.scrollHeight - this.scrollingPosition;
				this.requestScroll(-1);
			});
		},
		scrollToMessage(messageId) {
			this.$nextTick(function () {
				let mc = document.getElementById('messenger_container');
				let message = document.getElementById('messenger_message_' + messageId);
				if (message) {
					mc.scrollTop = message.offsetTop - 100;
				}
			});
		},
		scrollBottom() {
			this.requestScroll(0);
			this.scroll();
		},
		onScroll({target: {scrollTop, scrollHeight, clientHeight}}) {
			if (scrollTop === 0 && !this.messagesOldEndReached) {
				if (this.messages.length > 0) {
					this.loadMoreOldMessages();
					this.requestScroll(scrollHeight - scrollTop);
				}
			}
			if (scrollHeight - scrollTop - clientHeight < 10) {
				if (this.messages.length > 0 && !this.messagesNewEndReached) {
					this.loadMoreNewMessages();
				}
			}
		},
		showChatContextMenu(message, event) {
			this.contextMenuVisible = true;
			this.contextMenuX = event.clientX;
			this.contextMenuY = event.clientY;
			this.contextMenuMessage = message;
		},
		remove(message) {
			// ask for confirmation
			this.$root.$emit('messengerConfirm', {
				title: 'Удалить сообщение?',
				button: {
					yes: 'Удалить',
					no: 'Отмена'
				},
				callback: confirm => {
					if (confirm) {
						this.deleteMessage(message);
					}
				}
			});
		},
		react(emoji) {
			this.reactMessage({message: this.contextMenuMessage, emoji_id: emoji});
		},
	},
	filters: {
		formatDate(date) {
			// if today show only hour and minutes
			if (moment(date).isSame(moment(), 'day')) {
				// return moment(date).format('HH:mm');
				return 'Сегодня'
			}
			// if yesterday show only hour and minutes and yesterday
			if (moment(date).isSame(moment().subtract(1, 'day'), 'day')) {
				return 'Вчера';
			}
			// if older than yesterday show hour, minutes, day and month
			return moment(date).format('DD MMMM');
		},
	}
}
</script>

<style lang="scss" scoped>
.ConversationFeed{
	&-startUnread{
		display: flex;
		flex-flow: row nowrap;
		justify-content: stretch;
		align-items: center;
		align-self: stretch;
		gap: 10px;

		margin-bottom: 5px;

		&:before,
		&:after{
			content: '';
			flex: 1;
			border: 0.5px solid rgba(170, 192, 222, 0.5);
		}

		&-text{
			flex: 0 0 content;
			padding: 10px 25px;
			background: #FFFFFF;
			opacity: 0.75;
			box-shadow: 0px 0px 1px rgba(12, 26, 75, 0.05), 0px 10px 16px rgba(20, 37, 63, 0.05);
			border-radius: 30px;

			font-weight: 500;
			font-size: 11px;
			line-height: 14px;
			letter-spacing: -0.02em;

			color: #6F82A3;
		}
	}
}
#messenger_container{
	align-self: stretch;
	background: url('../../../../../assets/chat/bg.jpg') repeat, #F7F8FA;
	background-size: cover;
}
.messenger__container-scroll {
	display: flex;
	flex: 1;
	overflow-y: auto;
	margin-right: 1px;
	-webkit-overflow-scrolling: touch;
}

.messenger__message-wrapper{
	margin-bottom: 5px;
}

.messenger__messages-container {
	padding: 0 5px 5px;
	flex: 1;
}

/* total width */
.messenger__container-scroll::-webkit-scrollbar {
	background-color: #fff;
	width: 8px;
}

/* background of the scrollbar except button or resizer */
.messenger__container-scroll::-webkit-scrollbar-track {
	background-color: #bcbcbd;
	border-radius: 24px;
}

/* scrollbar itself */
.messenger__container-scroll::-webkit-scrollbar-thumb {
	background-color: #7e7e81;
	border-radius: 24px;
}

/* set button(top and bottom of the scrollbar) */
.messenger__container-scroll::-webkit-scrollbar-button {
	display: none;
}
.messenger__context-menu_reactions{
	display: flex;
	flex-flow: row nowrap;
	justify-content: space-between;
	gap: 10px;
	padding: 0 10px;
}
.messenger__context-menu_reaction {
	color: #3361FF;
	display: inline-block;
	padding: 10px;
	cursor: pointer;
}
.messenger__context-menu_reaction:hover {
	background-color: #F6F8FF;
}

.messenger__messages-date {
	display: block;
	text-align: center;
	overflow: hidden;
	white-space: nowrap;
}
.messenger__messages-date-block{
	display: inline-block;
	padding: 0.8rem 2rem;
	margin: 3rem auto;
	border-radius: 25rem;
	position: relative;
	color: #3361FF;
	background-color: #fff;
	box-shadow: 0px 0px 1px rgba(12, 26, 75, 0.05), 0px 10px 16px rgba(20, 37, 63, 0.05);
	font-size: 1.1rem;
	line-height: 1.4rem;
	font-weight: 500;
	letter-spacing: -0.03em;
}

// .messenger__messages-date > span {
// 	position: relative;
// 	display: inline-block;
// 	color: #a0a0a4;
// }
/*
.messenger__messages-date > span:before,
.messenger__messages-date > span:after {
	content: "";
	position: absolute;
	top: 50%;
	width: 100vw;
	height: 1px;
	background: #e7e7ea;
}

.messenger__messages-date > span:before {
	right: 100%;
	margin-right: 15px;
}

.messenger__messages-date > span:after {
	left: 100%;
	margin-left: 15px;
} */

.messenger__loader {
	display: flex;
	justify-content: center;
	align-items: center;
	width: 100%;
	height: 100%;
	position: absolute;
	background: #f4f6fa;
}

.messenger__loader > div {
	width: 20px;
	height: 20px;
	border-radius: 50%;
	background-color: #7e7e81;
	animation: messenger__loader 1s infinite ease-in-out;
}

.messenger__loader > div:nth-child(2) {
	animation-delay: -0.5s;
}

@keyframes messenger__loader {
	0%, 100% {
		transform: scale(0);
	}
	50% {
		transform: scale(1);
	}
}

</style>
