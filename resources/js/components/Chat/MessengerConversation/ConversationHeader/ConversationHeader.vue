<template>
  <div class="messenger__chat-header">
    <div class="messenger__chat-wrapper">
      <div class="messenger__info-wrapper" v-if="chat" @click="toggleInfoPanel">
        <AlternativeAvatar :title="chat.title" :image="chat.image"/>
        <div class="messenger__info-wrapper_head messenger__text-ellipsis messenger__clickable">
          <div class="messenger__chat-name">
            <span v-text="chat.title"></span>
            <span v-if="chat.private" class="messenger__chat-status" :class="{'messenger__chat-status--online': chat.isOnline}"></span>
          </div>
          <div class="messenger__chat-info" v-text="chat.role"></div>
        </div>
        <div class="messenger__chat-button-right" @click="openAddMemberModal">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 52 52">
            <path d="M38.5 25H27V14c0-.553-.448-1-1-1s-1 .447-1 1v11H13.5c-.552 0-1 .447-1 1s.448 1 1 1H25v12c0 .553.448 1 1 1s1-.447 1-1V27h11.5c.552 0 1-.447 1-1s-.448-1-1-1z" fill="#FFFFFF"/>
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
    ...mapGetters(['chat', 'contacts', 'user'])
  },
  data() {
    return {
      showAddMemberModal: false,
      imageError: false,
    };
  },
  methods: {
    ...mapActions(['getUsers', 'setCurrentChatContacts', 'toggleInfoPanel']),
    openAddMemberModal(e) {
      e.stopPropagation();
      this.getUsers();
      this.setCurrentChatContacts(this.chat.users.filter(user => user.id !== this.user.id));
      this.showAddMemberModal = true;
    },
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
  font-size: 16px;
  line-height: 22px;
  color: #3f4144;
  margin-bottom: 2px;
}

.messenger__chat-status {
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #f60000;
  margin-left: 4px;
}

.messenger__chat-status--online {
  background-color: #4cd964;
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

</style>
