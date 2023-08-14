<template>
	<div
		v-if="isInfoPanel && chat"
		class="messenger__info-container"
	>
		<div class="messenger__chat-info">
			<div
				v-if="!chat.private"
				class="messenger__chat-info-title"
			>
				<div
					v-if="!editMode"
					class="messenger__chat-info-title-text"
					@click="editMode = true"
				>
					<span>{{ chat.title }}</span>
					<div class="messenger__chat-info__button">
						<svg
							xmlns="http://www.w3.org/2000/svg"
							width="20"
							height="20"
							viewBox="0 0 512 512"
						>
							<path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.8 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
						</svg>
					</div>
				</div>
				<div
					v-if="editMode"
					class="messenger__chat-info-title-text"
					@keyup.enter="changeTitle"
				>
					<input
						v-model="chat.title"
						type="text"
						class="messenger__chat-info-title-input"
						placeholder="Enter chat name"
					>
					<div class="messenger__chat-info__button">
						<svg
							xmlns="http://www.w3.org/2000/svg"
							width="25"
							height="25"
							viewBox="0 0 448 512"
							@click="changeTitle"
						>
							<path
								d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V173.3c0-17-6.7-33.3-18.7-45.3L352 50.7C340 38.7 323.7 32 306.7 32H64zm0 96c0-17.7 14.3-32 32-32H288c17.7 0 32 14.3 32 32v64c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V128zM224 416c-35.3 0-64-28.7-64-64s28.7-64 64-64s64 28.7 64 64s-28.7 64-64 64z"
							/>
						</svg>
					</div>
				</div>
			</div>
			<div class="messenger__chat-info-members">
				<div class="messenger__chat-info-members-list">
					<div
						v-for="member in chat.users"
						:key="member.id"
						class="messenger__chat-info-members-list-item"
					>
						<AlternativeAvatar
							:title="member.name"
							:image="member.img_url"
						/>
						<div class="messenger__chat-info-members-list-item-name">
							<span>{{ member.name }}</span>
						</div>
						<!--            крестик справа в конца-->
						<div
							v-if="!chat.private && member.id !== user.id"
							class="messenger__chat-info-members-list-item-remove"
							@click="removeMembers([member])"
						>
							<svg
								xmlns="http://www.w3.org/2000/svg"
								width="20"
								height="20"
								viewBox="0 0 320 512"
								fill="#9ca6af"
							>
								<path
									d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z"
								/>
							</svg>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import AlternativeAvatar from '../ChatsList/ContactItem/AlternativeAvatar/AlternativeAvatar';

export default {
	name: 'InfoPanel',
	components: {
		AlternativeAvatar
	},
	data() {
		return {
			editMode: false,
		};
	},
	computed: {
		...mapGetters(['user', 'chat', 'isInfoPanel'])
	},
	methods: {
		...mapActions(['editChatTitle', 'removeMembers']),
		changeTitle() {
			this.editChatTitle();
			this.editMode = false;
		},
	}
}
</script>

<style scoped>
.messenger__info-container {
  display: flex;
  flex-flow: column;
  flex: 0 0 25%;
  min-width: 280px;
  max-width: 500px;
  position: relative;
  background: #fff;
  height: 100%;
  border-top-left-radius: 4px;
  border-bottom-left-radius: 4px;
  overflow-y: auto;
}

.messenger__collapsed {
  min-width: auto;
}

.messenger__chats-container {
  display: flex;
  flex-flow: column;
  flex: 1 1 auto;
  position: relative;
  background: #fff;
  height: 100%;
  border-top-left-radius: 4px;
  border-bottom-left-radius: 4px;
}

.messenger__chat-info {
  display: flex;
  flex-flow: column;
  flex: 1 1 auto;
  position: relative;
  background: #fff;
  height: 100%;
  border-top-left-radius: 4px;
  border-bottom-left-radius: 4px;
}

.messenger__chat-info-title {
  display: flex;
  flex-flow: row;
  flex: 0 0 auto;
  position: relative;
  background: #fff;
  height: 50px;
  cursor: pointer;
  color: #3f4144;
}

.messenger__chat-info-title-text {
  display: flex;
  flex-flow: row;
  flex: 1 1 auto;
  position: relative;
  background: #fff;
  height: 100%;
  align-items: flex-start;
  font-size: 16px;
  line-height: 22px;
  justify-content: space-between;
  padding: 10px;
}

.messenger__chat-info__button {
  margin: 0 10px;
}

.messenger__chat-info-title-buttons {
  display: flex;
  flex-flow: row;
  flex: 0 0 auto;
  position: relative;
  background: #fff;
  height: 100%;
  align-items: center;
  padding-right: 10px;
}

.messenger__chat-info-members {
  display: flex;
  flex-flow: column;
  flex: 1 1 auto;
  position: relative;
  background: #fff;
  border-bottom: 1px solid #e6e6e6;
}

.messenger__chat-info-members-title {
  display: flex;
  flex-flow: row;
  flex: 0 0 auto;
  position: relative;
  background: #fff;
  height: 50px;
  align-items: center;
  padding-left: 10px;
}

.messenger__chat-info-members-list {
  display: flex;
  flex-flow: column;
  flex: 1 1 auto;
  position: relative;
  background: #fff;
  height: 100%;
  overflow-y: auto;
}

.messenger__chat-info-members-list-item {
  display: flex;
  flex-flow: row;
  flex: 0 0 auto;
  position: relative;
  background: #fff;
  height: 50px;
  align-items: center;
  padding-left: 10px;
}

.messenger__chat-info-members-list-item-avatar {
  display: flex;
  flex-flow: row;
  flex: 0 0 auto;
  position: relative;
  background: #fff;
  height: 100%;
  align-items: center;
  padding-right: 15px;
}

.messenger__chat-info-members-list-item-avatar img {
  width: 30px;
  height: 30px;
  border-radius: 50%;
}

.messenger__chat-info-members-list-item-name {
  display: flex;
  flex-flow: row;
  flex: 1 1 auto;
  position: relative;
  background: #fff;
  height: 100%;
  align-items: center;
  font-size: 16px;
}

.messenger__chat-info-members-list-item-name-text {
  display: flex;
  flex-flow: row;
  flex: 1 1 auto;
  position: relative;
  background: #fff;
  height: 100%;
  align-items: center;
}

.messenger__chat-info-members-list-item-name-text span {
  font-size: 14px;
  font-weight: 400;
  color: #000;
}

.messenger__chat-info-members-list-item-name-text span:first-child {
  font-weight: 600;
}

.messenger__chat-info-members-list-item-remove {
  padding-right: 10px;
  cursor: pointer;
}

/* total width */
.messenger__info-container::-webkit-scrollbar {
  background-color: #fff;
  width: 8px;
}

/* background of the scrollbar except button or resizer */
.messenger__info-container::-webkit-scrollbar-track {
  background-color: #bcbcbd;
  border-radius: 24px;
}

/* scrollbar itself */
.messenger__info-container::-webkit-scrollbar-thumb {
  background-color: #7e7e81;
  border-radius: 24px;
}

/* set button(top and bottom of the scrollbar) */
.messenger__info-container::-webkit-scrollbar-button {
  display: none;
}

</style>
