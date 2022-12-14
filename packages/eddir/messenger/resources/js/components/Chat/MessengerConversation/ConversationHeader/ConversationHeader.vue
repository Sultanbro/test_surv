<template>
  <div class="messenger__chat-header">
    <div class="messenger__chat-wrapper">
      <div class="messenger__info-wrapper" v-if="chat">
        <div class="messenger__info-wrapper_avatar" @click="changeAvatar">
          <AlternativeAvatar :title="chat.title" :image="chat.image"/>
        </div>
        <div class="messenger__info-wrapper_head messenger__text-ellipsis messenger__clickable">
          <div class="messenger__chat-name">
            <span v-text="chat.title" @click="changeTitle" v-if="!editTitle"></span>
            <div v-if="editTitle" @keyup.enter="saveTitle" class="messenger__chat-info-title-text">
              <input v-model="chat.title" type="text" class="messenger__chat-info-title-input"
                     placeholder="Введите название">
              <div class="messenger__chat-info__button">
                <svg @click="saveTitle" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 448 512">
                  <path
                    d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V173.3c0-17-6.7-33.3-18.7-45.3L352 50.7C340 38.7 323.7 32 306.7 32H64zm0 96c0-17.7 14.3-32 32-32H288c17.7 0 32 14.3 32 32v64c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V128zM224 416c-35.3 0-64-28.7-64-64s28.7-64 64-64s64 28.7 64 64s-28.7 64-64 64z"/>
                </svg>
              </div>
            </div>
            <template v-if="chat.private">
              <span v-if="chat.isOnline" class="messenger__chat-name_online">Онлайн</span>
              <span v-else class="messenger__chat-name_online">Офлайн</span>
              <div class="messenger__chat-name_position">{{ chat.position }}</div>
            </template>
            <div v-else class="messenger__chat-name_overlay" ref="messengerChatNameUsers">
              <span v-for="member in chat.users" class="messenger__chat-name_members" @click="changeAdmin(member)">
                <AlternativeAvatar :class="{
                  'messenger__chat-name_member-admin': chat.users.find(u => u.id === member.id).pivot.is_admin
                }"
                                   :inline=" true" :title="member.name" :image="member.img_url"
                />
                <template v-if="showMembersNames">
                  {{ member.name }}
                </template>
              </span>
            </div>
          </div>
          <div class="messenger__chat-info" v-text="chat.role"></div>
        </div>
        <div class="messenger__search-button" @click="toggleChatSearchMode">
          <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24">
            <path
              d="m1253.89 164.337-3.16-3.159a5.585 5.585 0 1 0-.55.547l3.16 3.16a.375.375 0 0 0 .27.115.4.4 0 0 0 .28-.115.39.39 0 0 0 0-.548Zm-12.11-6.794a4.765 4.765 0 1 1 4.76 4.768 4.763 4.763 0 0 1-4.76-4.768Z"
              id="messenger__icon-search" transform="translate(-1241 -147)"/>
          </svg>
        </div>
        <div class="messenger__chat-button-right" @click="openAddMemberModal">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 52 52">
            <path
              d="M38.5 25H27V14c0-.553-.448-1-1-1s-1 .447-1 1v11H13.5c-.552 0-1 .447-1 1s.448 1 1 1H25v12c0 .553.448 1 1 1s1-.447 1-1V27h11.5c.552 0 1-.447 1-1s-.448-1-1-1z"
              fill="#FFFFFF"/>
          </svg>
        </div>
      </div>
    </div>
    <ConversationPinned></ConversationPinned>
    <AddMemberModal v-if="showAddMemberModal" @close="showAddMemberModal = false"></AddMemberModal>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import ConversationPinned from "../ConversationPinned/ConversationPinned.vue";
import AddMemberModal from "./AddMemberModal/AddMemberModal.vue";
import AlternativeAvatar from "../../ChatsList/ContactItem/AlternativeAvatar/AlternativeAvatar.vue";

export default {
  name: "ConversationHeader",
  components: {
    ConversationPinned,
    AddMemberModal,
    AlternativeAvatar,
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
  },
  data() {
    return {
      showAddMemberModal: false,
      imageError: false,
      showMembersNames: false,
      editTitle: false
    };
  },
  watch: {
    members() {
      if (!this.chat.private) {
        this.$nextTick(() => {
          this.showMembersNames = this.chat.users.length * 100 < this.$refs.messengerChatNameUsers.clientWidth;
        });
      }
    },
  },
  methods: {
    ...mapActions([
      'setCurrentChatContacts',
      'toggleInfoPanel', 'toggleChatSearchMode',
      'uploadChatAvatar',
      'setChatAdmin', 'unsetChatAdmin', 'editChatTitle'
    ]),
    openAddMemberModal(e) {
      e.stopPropagation();
      this.showAddMemberModal = true;
    },
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

<style>

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
  font-size: 14px;
  line-height: 22px;
  color: #3f4144;
  margin-bottom: 2px;
}

.messenger__chat-selected .messenger__chat-name {
  color: #fff;
}

.messenger__chat-name_overlay {
  overflow-x: auto;
}

.messenger__chat-name_online {
  color: #a7a7a7;
  margin-left: 5px;
}

.messenger__chat-name_position {
  color: #a7a7a7;
  margin-left: 5px;
  font-size: 13px;
}

.messenger__chat-name_members {
  margin-right: 5px;
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
  background: #5ebee9;
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
