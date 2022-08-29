<template>
  <div class="chats-list-panel">
    <input
      type="text"
      class="search-input"
      placeholder="Search"
      v-model="search"
      @input="onSearchInput()"
    />
<!--    кнопка создать-->
    <button class="create-chat-button" @click="showCreateModal()">Create</button>
    <CreateChatModal v-show="createModalVisible"
                     :users="createModalUsers"
                     @create="createChat"
                     @close="createModalVisible=false"/>
    <div class="chats-list">
      <ul>
        <li
          v-for="chat in chats"
          :key="chat.id"
          :class="{ selected: chat === selected }"
          @onkeydown="selectContact(chat)"
          @click="selectContact(chat)"
          @contextmenu.prevent.stop="handleClick($event, chat)"
        >
          <div class="avatar">
            <img :src="chat.image" :alt="chat.title"/>
          </div>
          <div class="chat">
            <p class="name">{{ chat.title }}</p>
          </div>
          <span v-if="chat.unread_messages_count" class="unread">{{ chat.unread_messages_count }}</span>
        </li>
      </ul>
    </div>
    <vue-simple-context-menu
      element-id="chatsMenu"
      :options="options"
      ref="vueSimpleContextMenuChats"
      @option-clicked="optionClicked"
    />
  </div>
</template>

<script>
import VueSimpleContextMenu from "vue-simple-context-menu";
import CreateChatModal from "./CreateChatModal.vue";
import API from "./API.vue";

export default {
  components: {
    VueSimpleContextMenu,
    CreateChatModal,
  },
  props: {
    chats: {
      type: Array,
      default: () => [],
    },
    users: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      selected: this.chats.length ? this.chats[0] : null,
      search: "",
      options: [
        {
          name: "Покинуть чат",
          icon: "fa fa-sign-out",
          action: (item) => {
            this.$emit("leave-chat", item);
          },
        }
      ],
      createModalVisible: false,
      createModalUsers: [],
    };
  },
  methods: {
    selectContact(chat) {
      this.selected = chat;

      // // check if the chat is private
      // if (chat.private) {
      //   console.log("private chat");
      //
      //   // get user id as part after user in chat.id
      //   const userId = chat.id.split("user")[1];
      //
      //   this.$emit('selected', {id: userId})
      //
      // } else {
        this.$emit('selected', chat);
        this.search = "";
      // }

    },
    onSearchInput() {
      this.$emit('search', this.search);
    },
    handleClick(event, item) {
      this.$refs.vueSimpleContextMenuChats.showMenu(event, item);
    },
    optionClicked(item) {
      item.option.action(item.item);
    },
    showCreateModal() {
      API.fetchUsers(users => {
        this.createModalUsers = users;
        this.createModalVisible = true;
      });
    },
    createChat(items) {
      this.$emit('create-chat', items);
    },
  },
};
</script>

<style>
.chats-list-panel {
  background-color: white;
}

.chats-list {
  flex: 2;
  max-height: 100%;
  height: 600px;
  overflow: auto;
  border-left: 1px solid #a6a6a6;
  border-top: 1px solid #a6a6a6;
}

.chats-list ul {
  list-style-type: none;
  padding-left: 0;
}

.chats-list ul li {
  display: flex;
  padding: 2px;
  border-bottom: 1px solid #e1dddd;
  height: 60px;
  position: relative;
  cursor: pointer;
}

.chats-list ul li:hover {
  background-color: #f5f5f5;
}

.chats-list ul li.selected {
  background: #dfdfdf;
}

.chats-list ul li span.unread {
  background: #82e0a8;
  color: #fff;
  position: absolute;
  right: 11px;
  top: 20px;
  display: flex;
  font-weight: 700;
  min-width: 20px;
  justify-content: center;
  align-items: center;
  line-height: 20px;
  font-size: 12px;
  padding: 0 4px;
  border-radius: 3px;
}

.chats-list ul li .avatar {
  flex: 1;
  display: flex;
  align-items: center;
}

.chats-list ul li .avatar img {
  width: 35px;
  border-radius: 50%;
  margin: 0 auto;
}

.chats-list ul li .chat {
  flex: 3;
  font-size: 18px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.chats-list ul li .chat p {
  margin: 0;
}

.chats-list ul li .chat p.name {
  font-weight: bold;
}

/* total width */
.chats-list::-webkit-scrollbar {
  background-color: #fff;
  width: 16px;
}

/* background of the scrollbar except button or resizer */
.chats-list::-webkit-scrollbar-track {
  background-color: #fff;
}

/* scrollbar itself */
.chats-list::-webkit-scrollbar-thumb {
  background-color: #babac0;
  border-radius: 16px;
  border: 4px solid #fff;
}

/* set button(top and bottom of the scrollbar) */
.chats-list::-webkit-scrollbar-button {
  display: none;
}

.search-input {
  flex: 1;
  border: none;
  padding: 10px;
  font-size: 18px;
  outline: none;
  height: 50px;
}

.search-input:focus {
  outline: none 0 !important;
  box-shadow: none;
  -moz-box-shadow: none;
  -webkit-box-shadow: none;
}

.create-chat-button {
/*  прямоугольник с круглыми углами с серым фоном */
  background-color: #f5f5f5;
  border-radius: 25px;
  border: 1px solid #e1dddd;
  padding: 10px;
  font-size: 18px;
  outline: none;
  height: 50px;
}

.create-chat-button:hover {
  background-color: #f5f5f5;
  color: #a6a6a6;
}
</style>
