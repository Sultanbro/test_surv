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
					<JobtronAvatar
						:title="chat.title"
						:image="avatar"
						:size="50"
					/>
				</div>
				<div class="messenger__info-wrapper_head messenger__text-ellipsis messenger__clickable">
					<div class="messenger__chat-name">
						<span class="ConversationHeader-title">
							{{ chat.title }}
						</span>
						<!-- <div
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
						</div> -->
						<div
							v-if="chat.private"
							class="ConversationHeader-status"
						>
							<span
								v-if="chat.isOnline"
								class="messenger__chat-name_online"
							/>
							<span
								v-else
								class="messenger__chat-name_offline"
							/>
							<span class="messenger__chat-name_position">
								{{ chat.position }}
							</span>
						</div>
						<div
							v-else
							ref="messengerChatNameUsers"
							class="messenger__chat-name_overlay"
						>
							<div
								v-for="member in firstTenUsers"
								:key="member.id"
								class="messenger__chat-name_members"
								@click="showUserPopup(member)"
							>
								<JobtronAvatar
									:title="`${member.name} ${member.last_name}`"
									:image="'/users_img/' + member.img_url"
									:size="27"
									:class="{
										'messenger__chat-name_member-admin': chat.users.find(u => u.id === member.id) && chat.users.find(u => u.id === member.id).pivot ? chat.users.find(u => u.id === member.id).pivot.is_admin : false
									}"
									tooltip
								/>
								<template v-if="showMembersNames">
									{{ member.name }}
								</template>
								<PopupMenu
									v-if="userPopup === member.id"
									v-click-outside="toggleUserPopupIfShown"
									position="bottomLeft"
								>
									<div
										v-if="isAdmin"
										class="PopupMenu-item wsnw ChatIcon-parent"
										@click="userPopup === 0; changeAdmin(member)"
									>
										{{ (chat.users.find(u => u.id === member.id) && chat.users.find(u => u.id === member.id).pivot ? chat.users.find(u => u.id === member.id).pivot.is_admin : false) ? 'Забрать права админа' : 'Сделать админом' }}
									</div>
									<div
										class="PopupMenu-item wsnw ChatIcon-parent"
										@click="userPopup === 0; openChat(member)"
									>
										Написать сообщение
									</div>
									<div
										v-if="isAdmin"
										class="PopupMenu-item wsnw ChatIcon-parent"
										@click="userPopup === 0; removeFromChat(member)"
									>
										Удалить из чата
									</div>
								</PopupMenu>
							</div>
							<span
								v-if="members.length > firstTenUsers.length"
								class="messenger__chat-more__names"
							>
								еще {{ otherUsers.length }}+
								<span class="messenger__chat-more__names-popup">
									<div class="messenger__chat-more__names-popup-scroll">
										<span
											v-for="user in otherUsers"
											:key="user.id"
											class="messenger__chat-more__names-popup-item"
											@click="showUserPopup(user)"
										>
											<JobtronAvatar
												:title="`${user.name} ${user.last_name}`"
												:image="'/users_img/' + user.img_url"
												:size="24"
												:class="{
													'messenger__chat-name_member-admin': chat.users.find(u => u.id === user.id) && chat.users.find(u => u.id === user.id).pivot ? chat.users.find(u => u.id === user.id).pivot.is_admin : false
												}"
											/>
											{{ `${user.name} ${user.last_name}` }}
										</span>
									</div>
									<template v-for="user in otherUsers">
										<PopupMenu
											v-if="userPopup === user.id"
											:key="user.id"
											v-click-outside="toggleUserPopupIfShown"
											position="bottomLeft"
										>
											<div
												v-if="isAdmin"
												class="PopupMenu-item wsnw ChatIcon-parent"
												@click="userPopup === 0; changeAdmin(user)"
											>
												{{ (chat.users.find(u => u.id === user.id) && chat.users.find(u => u.id === user.id).pivot ? chat.users.find(u => u.id === user.id).pivot.is_admin : false) ? 'Забрать права админа' : 'Сделать админом' }}
											</div>
											<div
												class="PopupMenu-item wsnw ChatIcon-parent"
												@click="userPopup === 0; openChat(user)"
											>
												Написать сообщение
											</div>
											<div
												v-if="isAdmin"
												class="PopupMenu-item wsnw ChatIcon-parent"
												@click="userPopup === 0; removeFromChat(user)"
											>
												Удалить из чата
											</div>
										</PopupMenu>
									</template>
								</span>
							</span>
						</div>
					</div>
					<!-- <div class="messenger__chat-info">
						{{ chat.role }}
					</div> -->
				</div>
				<div
					v-if="actions"
					class="messenger__search-button ConversationHeader-icon ChatIcon-parent"
					title="Архив"
					@click="toggleChatSearchMode"
				>
					<ChatIconSearchMessages />
				</div>
				<div
					v-if="actions"
					class="messenger__chat-button-right"
				>
					<div
						class="ConversationHeader-icon ChatIcon-parent"
						@click="togglePopup"
					>
						<ChatIconMore />
					</div>
					<PopupMenu
						v-if="isPopup"
						v-click-outside="togglePopupIfShown"
					>
						<div
							class="PopupMenu-item wsnw ChatIcon-parent"
							@click="togglePinned"
						>
							<ChatIconPinChat />
							{{ chat.pinned ? 'Открепить чат' : 'Закрепить чат' }}
						</div>
						<!-- <div
							class="ContextMenu-item wsnw ChatIcon-parent"
							@click="contextViewProfile"
						>
							<ChatIconViewUser />
							Посмотреть профиль
						</div> -->
						<div
							v-if="isAdmin && !chat.private"
							class="ContextMenu-item wsnw ChatIcon-parent"
							@click="toggleEdit"
						>
							<ChatIconEditChat />
							Редактировать
						</div>
						<div
							class="PopupMenu-item wsnw ChatIcon-parent"
							@click="toggleMuted"
						>
							<ChatIconMuteChat />
							{{ chat.is_mute ? 'Включить уведомления' : 'Выключить уведомления' }}
						</div>
						<div
							v-if="isAdmin"
							class="PopupMenu-item PopupMenu-item_red wsnw "
							@click="isPopup = false; remove(chat)"
						>
							<ChatIconDeleteChat />
							Удалить чат
						</div>
						<div
							v-else-if="chat.id"
							class="PopupMenu-item wsnw ChatIcon-parent"
							@click="isPopup = false; leftChat(chat)"
						>
							<ChatIconHistoryBack />
							Покинуть чат
						</div>
					</PopupMenu>
				</div>
			</div>
		</div>
		<ConversationPinned />

		<JobtronOverlay
			v-if="isCropModal"
			@close="isCropModal = false"
		>
			<div class="ConversationHeader-cropper">
				<div id="chat-croppie" />
				<JobtronButton @click="saveAvatar">
					Сохранить
				</JobtronButton>
			</div>
		</JobtronOverlay>
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import ConversationPinned from '../ConversationPinned/ConversationPinned.vue';
import PopupMenu from '@ui/PopupMenu'
import 'vue-advanced-cropper/dist/style.css'
import {
	ChatIconMore,
	ChatIconSearchMessages,
	ChatIconPinChat,
	ChatIconEditChat,
	ChatIconMuteChat,
	ChatIconDeleteChat,
	ChatIconHistoryBack,
} from '@icons'
import JobtronAvatar from '@ui/Avatar'
import JobtronOverlay from '@ui/Overlay'
import JobtronButton from '@ui/Button'
// import clickOutside from '../../directives/clickOutside.ts';


export default {
	name: 'ConversationHeader',
	components: {
		ConversationPinned,
		PopupMenu,
		JobtronAvatar,
		ChatIconMore,
		ChatIconSearchMessages,
		ChatIconPinChat,
		ChatIconEditChat,
		ChatIconMuteChat,
		ChatIconDeleteChat,
		ChatIconHistoryBack,
		JobtronOverlay,
		JobtronButton,
	},
	// directives: {
	// 	clickOutside
	// },
	props: {
		actions: {
			type: Boolean,
			default: true
		}
	},
	data() {
		return {
			showMembersNames: false,
			editTitle: false,
			isPopup: false,
			userPopup: 0,
			isCropModal: false,
			croppie: null,
		};
	},
	computed: {
		...mapGetters(['chat', 'user', 'accessDictionaries']),
		members() {
			if(!this.chat) return []
			if(!this.chat.id) return this.accessDictionaries.users.map(user => ({
				id: user.io,
				name: user.name.split(' ')[0],
				/* eslint-disable camelcase */
				last_name: user.name.split(' ').splice(1, 5).join(' '),
				img_url: user.avatar,
				pivot: {
					chat_id: 0,
					is_admin: 0,
					user_id: user.id
				}
				/* eslint-enable camelcase */
			}))
			return this.chat.users
		},
		isAdmin() {
			if(!this.chat?.users) return false
			const user = this.chat.users.find(user => user.id === this.user.id)
			return user?.pivot?.is_admin
		},
		firstTenUsers(){
			if(!this.isDesktop) return this.members.slice(0, 3)
			if(this.members.length < 11) return this.members
			return this.members.slice(0, 10)
		},
		otherUsers(){
			if(!this.isDesktop) return this.members.slice(3)
			if(this.members.length < 11) return []
			return this.members.slice(10)
		},
		avatar(){
			if(this.chat.private) return '/users_img/' + this.chat.second_user.img_url
			return this.chat.image?.replace('/storage', '')
		},
		isDesktop() {
			return this.$viewportSize.width > 670
		},
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
	created(){
		if(!this.accessDictionaries?.users?.length) this.loadCompany()
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
			'removeChat',
			'leftChat',
			'pinChat',
			'unpinChat',
			'muteChat',
			'unmuteChat',
			'loadChat',
			'removeMembers',
			'loadCompany',
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
						this.cropAvatar(file);
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
		cropAvatar(file){
			/* global Croppie */
			const reader = new FileReader()
			reader.addEventListener('load', () => {
				this.croppie = new Croppie(document.getElementById('chat-croppie'), {
					enableExif: true,
					viewport: {
						width:200,
						height:200,
						type:'square' //circle
					},
					boundary:{
						width:300,
						height:300
					}
				})
				this.croppie.bind({
					url: reader.result
				})
			}, false);

			this.isCropModal = true
			reader.readAsDataURL(file);
		},
		saveAvatar(){
			this.isCropModal = false
			this.croppie.result({
				type: 'blob',
				format: 'jpeg',
				quality: 0.8
			}).then(blob => {
				const file = new File([blob], 'image.jpeg')
				this.uploadChatAvatar(file)
			})
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
				if (user.pivot?.is_admin) {
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
		},
		togglePopup(){
			// какой-то костыль получился, но сроки жмут нормально делать времени нет
			if(this.isPopup) this.isPopup = false
			else{
				setTimeout(() => {
					this.isPopup = !this.isPopup
				}, 50)
			}
		},
		remove(chat) {
			this.$root.$emit('messengerConfirm', {
				title: 'Удалить чат?',
				message: `Вы уверены, что хотите удалить чат ${chat.title}?`,
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
		togglePinned(){
			this.isPopup = false
			if(this.chat.pinned) return this.unpinChat(this.chat)
			this.pinChat(this.chat)
		},
		toggleMuted(){
			this.isPopup = false
			if(this.chat.is_mute) return this.unmuteChat(this.chat.id)
			this.muteChat(this.chat.id)
		},
		toggleEdit(){
			this.contextMenuVisible = false
			this.toggleInfoPanel()
		},
		togglePopupIfShown(){
			if(!this.isPopup) return
			this.isPopup = false
		},
		toggleUserPopupIfShown(){
			if(!this.userPopup) return
			this.userPopup = 0
		},
		showUserPopup(user){
			setTimeout(() => {
				this.userPopup = user.id
			}, 100)
		},
		openChat(user){
			this.loadChat({chatId: 'user' + user.id})
		},
		removeFromChat(user){
			this.removeMembers([
				user
			])
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
}

.messenger__chat-header_search{
	border-radius: 1.2rem 0 0 0;
}
// .messenger__chat-header_search .messenger__chat-wrapper{}

.messenger__info-wrapper {
	display: flex;
	align-items: center;
	gap: 15px;
	min-width: 0;
	width: 100%;
	height: 100%;
}

.messenger__info-wrapper_avatar {
	cursor: pointer;
}

.ConversationHeader-status{
	margin-bottom: 8px;
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
	display: flex;
  flex-flow: column nowrap;
  justify-content: space-between;
  height: 50px;
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
	display: flex;
	align-items: center;
	height: 28px;
}

.messenger__chat-name_online,
.messenger__chat-name_offline {
	display: inline-block;
	width: 1rem;
	height: 1rem;
	border-radius: 2rem;
	margin-left: 1px;
}
.messenger__chat-name_online {
	background-color: #27AE60;
}
.messenger__chat-name_offline {
	background-color: #F8254B;
}

.messenger__chat-name_position {
	margin-left: 5px;
	color: #a7a7a7;
	font-weight: 400;
	font-size: 11px;
	line-height: 14px;
}

.messenger__chat-name_members {
	display: block;
	margin-right: -10px;
	position: relative;
}
.messenger__chat-name_members
.JobtronAvatar{
	border: 1.5px solid #fff;
}
.messenger__chat-more__names{
	margin-left: 25px;
	color: #3361FF;
	font-weight: 500;
	font-size: 11px;
	line-height: 14px;
	letter-spacing: -0.03em;
	margin-top: -2px;
	position: relative;
}
.messenger__chat-more__names-popup{
	display: none;
	min-width: 100px;
	border-radius: 8px;
	padding: 10px 0;

	position: absolute;
	z-index: 1000;
	top: 100%;
	right: 0;

	background-color: #fff;
	box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.05), 0px 15px 60px -40px rgba(45, 50, 90, 0.2);
}
.messenger__chat-more__names:hover .messenger__chat-more__names-popup{
	display: block;
}

.messenger__chat-more__names-popup-scroll{
	max-height: 30vh;
	overflow-y: auto;
}

.messenger__chat-more__names-popup-item{
	display: flex;
	align-items: center;
	justify-content: flex-start;
	gap: 10px;

	width: 100%;
	padding: 10px 20px;

	text-decoration: none;
	color: #0a0a0a;
	white-space: nowrap;
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
	margin-left: auto;
	border-radius: 50%;

	position: relative;

	cursor: pointer;
}

.ConversationHeader-icon{
	padding: 5px;
	cursor: pointer;
	user-select: none;
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


.ConversationHeader{
	&-title{
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	&-cropper{
		display: flex;
		flex-flow: column nowrap;
		align-items: center;

		padding-bottom: 10px;

		position: absolute;
		top: 50%;
		left: 50%;
		background-color: #fff;
		border-radius: 16px;
		transform: translate(-50%, -50%);
		box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.05), 0px 15px 60px -40px rgba(45, 50, 90, 0.2);
	}
}
</style>
