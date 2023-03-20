<template>
	<div
		class="messenger__chat-header"
		:class="{'messenger__chat-header_search': isChatSearchMode}"
	>
		<div class="messenger__chat-wrapper">
			<div
				v-if="chat"
				class="messenger__info-wrapper"
			>
				<div
					class="messenger__info-wrapper_avatar"
					@click="changeAvatar"
				>
					<AlternativeAvatar
						:title="chat.title"
						:image="chat.image"
					/>
				</div>
				<div class="messenger__info-wrapper_head messenger__text-ellipsis messenger__clickable">
					<div class="messenger__chat-name">
						<span
							v-if="!editTitle"
							@click="changeTitle"
						>
							{{ chat.title }}
						</span>
						<div
							v-if="editTitle"
							@keyup.enter="saveTitle"
							class="messenger__chat-info-title-text"
						>
							<input
								v-model="chat.title"
								type="text"
								class="messenger__chat-info-title-input"
								placeholder="Введите название"
							>
							<div class="messenger__chat-info__button">
								<svg
									@click="saveTitle"
									xmlns="http://www.w3.org/2000/svg"
									width="14"
									height="14"
									viewBox="0 0 448 512"
								>
									<path
										d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V173.3c0-17-6.7-33.3-18.7-45.3L352 50.7C340 38.7 323.7 32 306.7 32H64zm0 96c0-17.7 14.3-32 32-32H288c17.7 0 32 14.3 32 32v64c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V128zM224 416c-35.3 0-64-28.7-64-64s28.7-64 64-64s64 28.7 64 64s-28.7 64-64 64z"
									/>
								</svg>
							</div>
						</div>
						<template v-if="chat.private">
							<span
								v-if="chat.isOnline"
								class="messenger__chat-name_online"
							/>
							<span
								v-else
								class="messenger__chat-name_offline"
							/>
							<div class="messenger__chat-name_position">
								{{ chat.position }}
							</div>
						</template>
						<div
							v-else
							class="messenger__chat-name_overlay"
							ref="messengerChatNameUsers"
						>
							<span
								v-for="member in firstTenUsers"
								:key="member.id"
								class="messenger__chat-name_members"
								@click="changeAdmin(member)"
							>
								<AlternativeAvatar
									:class="{
										'messenger__chat-name_member-admin': chat.users.find(u => u.id === member.id).pivot.is_admin
									}"
									:inline=" true"
									:title="member.name"
									:image="member.img_url"
								/>
								<template v-if="showMembersNames">
									{{ member.name }}
								</template>
							</span>
							<span
								v-if="members.length > firstTenUsers.length"
								class="messenger__chat-more__names"
							>
								еще {{ members.length - firstTenUsers.length }}+
							</span>
						</div>
					</div>
					<div
						class="messenger__chat-info"
						v-text="chat.role"
					/>
				</div>
				<div
					class="messenger__search-button"
					@click="toggleChatSearchMode"
				>
					<ChatIconSearchMessages />
				</div>
				<div class="messenger__chat-button-right">
					<ChatIconMore />
				</div>
			</div>
		</div>
		<ConversationPinned />
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import ConversationPinned from '../ConversationPinned/ConversationPinned.vue';
import AlternativeAvatar from '../../ChatsList/ContactItem/AlternativeAvatar/AlternativeAvatar.vue';
import { ChatIconMore, ChatIconSearchMessages } from '../../icons/chat-icons'

export default {
	name: 'ConversationHeader',
	components: {
		ConversationPinned,
		AlternativeAvatar,
		ChatIconMore,
		ChatIconSearchMessages,
	},
	data() {
		return {
			imageError: false,
			showMembersNames: false,
			editTitle: false
		};
	},
	computed: {
		...mapGetters(['chat', 'user']),
		members() {
			if (this.chat) {
				return this.chat.users;
			}
			return [];
		},
		isAdmin() {
			return this.chat.users.find(user => user.id === this.user.id).pivot.is_admin;
		},
		firstTenUsers(){
			if(this.members.length < 11) return this.members
			return this.members.slice(0, 10)
		}
	},
	watch: {
		// members() {
		// 	if (!this.chat.private) {
		// 		this.$nextTick(() => {
		// 			this.showMembersNames = this.chat.users.length * 100 < this.$refs.messengerChatNameUsers.clientWidth;
		// 		});
		// 	}
		// },
	},
	methods: {
		...mapActions([
			'setCurrentChatContacts',
			'toggleInfoPanel',
			'toggleChatSearchMode',
			'uploadChatAvatar',
			'setChatAdmin',
			'unsetChatAdmin',
			'editChatTitle',
			'isChatSearchMode',
		]),
		changeAvatar() {
			if (this.chat.private) {
				return;
			}
			if (this.isAdmin) {
				let input = document.createElement('input');
				input.type = 'file';
				input.accept = 'image/*';
				input.onchange = e => {
					let file = e.target.files[0];
					if (file) {
						this.uploadChatAvatar(file);
					}
				};
				input.click();
			} else {
				this.$root.$emit('messengerConfirm', {
					title: 'Недостаточно прав',
					message: 'Для изменения аватара чата необходимо быть администратором чата.',
				});
			}
		},
		changeTitle(event) {
			event.stopPropagation();
			if (this.chat.private) {
				return;
			}
			if (this.isAdmin) {
				this.editTitle = true;
				this.title = this.chat.title;
			} else {
				this.$root.$emit('messengerConfirm', {
					title: 'Недостаточно прав',
					message: 'Для изменения названия чата необходимо быть администратором чата.',
				});
			}
		},
		saveTitle(event) {
			event.stopPropagation();
			this.editChatTitle();
			this.editTitle = false;
		},
		changeAdmin(user) {
			if (user.id === this.user.id) {
				return;
			}
			if (this.isAdmin) {
				if (user.pivot.is_admin) {
					this.$root.$emit('messengerConfirm', {
						title: 'Забрать права администратора?',
						message: 'Вы уверены, что хотите забрать права администратора у пользователя ' + user.name + '?',
						button: {
							yes: 'Забрать',
							no: 'Отмена'
						},
						callback: confirm => {
							if (confirm) {
								this.unsetChatAdmin({chat: this.chat, user: user});
							}
						}
					});
				} else {
					this.$root.$emit('messengerConfirm', {
						title: 'Выдать права администратора?',
						message: 'Вы уверены, что хотите выдать права администратора пользователю ' + user.name + '?',
						button: {
							yes: 'Выдать',
							no: 'Отмена'
						},
						callback: confirm => {
							if (confirm) {
								this.setChatAdmin({chat: this.chat, user: user});
							}
						}
					});
				}
			} else {
				this.$root.$emit('messengerConfirm', {
					title: 'Недостаточно прав',
					message: 'Вы не можете изменять права администратора в этом чате'
				});
			}
		}
	}
}
</script>

<style lang="scss">

.messenger__chat-header {
	display: flex;
	align-items: center;
	flex-wrap: wrap;
	width: 100%;
	z-index: 10;
	margin-right: 1px;
	background: #fff;
	border-top-right-radius: 4px;
	border-bottom: 1px solid #c6c6c6;
	.access-modal__search{
		margin-top: 0;
		margin-bottom: 2.5rem;
	}
}

.messenger__chat-wrapper {
	display: flex;
	align-items: center;
	min-width: 0;
	height: 64px;
	width: 100%;
	padding: 0 16px;
	background-color: #fff;
	text-overflow: ellipsis;
	overflow: hidden;
	white-space: nowrap;
}

.messenger__chat-header_search{
	border-radius: 1.2rem 0 0 0;
}
.messenger__chat-header_search .messenger__chat-wrapper{

}

.messenger__info-wrapper {
	display: flex;
	align-items: center;
	min-width: 0;
	width: 100%;
	height: 100%;
}

.messenger__info-wrapper_avatar {
	cursor: pointer;
}

.messenger__chat-info {
	font-size: 13px;
	line-height: 18px;
	color: #9ca6af;
}

.messenger__info-wrapper_head {
	display: flex;
	flex-direction: column;
	min-width: 0;
	width: 100%;
}

.messenger__clickable {
	cursor: pointer;
}

.messenger__chat-name {
	margin-bottom: 2px;
	color: #3f4144;
	font-weight: 600;
	font-size: 14px;
	line-height: 16px;
	letter-spacing: -0.02em;
}

// .messenger__chat-selected .messenger__chat-name {
// 	color: #fff;
// }

.messenger__chat-name_overlay {
	overflow-x: auto;
}

.messenger__chat-name_online,
.messenger__chat-name_offline {
	display: inline-block;
	width: 1rem;
	height: 1rem;
	border-radius: 2rem;
	margin-left: 5px;
}
.messenger__chat-name_online {
	background-color: #3361FF;
}
.messenger__chat-name_offline {
	background-color: #8BABD8;
}

.messenger__chat-name_position {
	margin-left: 5px;
	color: #a7a7a7;
	font-weight: 400;
	font-size: 11px;
	line-height: 14px;
}

.messenger__chat-name_members {
	margin-right: -15px;
}
.messenger__chat-name_members
.messenger__avatar_container--small{
	border: 1px solid #fff;
}
.messenger__chat-more__names{
	margin-left: 25px;
	color: #3361FF;
	font-weight: 500;
	font-size: 11px;
	line-height: 14px;
	letter-spacing: -0.03em;
}

.messenger__chat-name_member-admin {
	border: 1px solid #5ebee9;
}

.messenger__chat-button-right {
	display: flex;
	align-items: center;
	justify-content: center;
	height: 32px;
	width: 32px;
	min-height: 32px;
	min-width: 32px;
	border-radius: 50%;
	cursor: pointer;
	margin-left: auto;
}

.messenger__search-button {
	cursor: pointer;
	margin-left: 8px;
}

.messenger__search-button:hover {
	opacity: 0.8;
}

.messenger__chat-info-title-text {
	font-size: 14px;
	line-height: 22px;
	color: #3f4144;
	margin-bottom: 2px;
}

.messenger__chat-info-title-input {
	display: inline;
	font-size: 14px;
	line-height: 22px;
	color: #3f4144;
	margin-bottom: 2px;
	border: none;
	outline: none;
}

.messenger__chat-info-title-input:focus {
	border: none;
	outline: none;
}

.messenger__chat-info__button {
	display: inline;
	cursor: pointer;
	margin-left: 8px;
}

</style>
