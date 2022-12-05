<template>
<div class="messenger__container-scroll">
  <div class="messenger__messages-container" style="flex-grow: 3">
    <div class="messenger__messages-search">
      <div class="messenger__messages-search-input">
        <input type="text" placeholder="Введите текст для поиска" v-model="searchMessagesQuery">
      </div>
      <div class="messenger__messages-search-date">
        <input type="date" v-model="searchMessagesDate">
      </div>
    </div>
    <div class="messenger__messages-search-results">
      <div class="messenger__message-wrapper" v-for="message in chatSearchMessagesResults" @click="goto(message, $event)">
        <ConversationMessage :message="message"/>
      </div>
    </div>
  </div>
  <div class="messenger__messages-container">
    <div class="messenger__messages-container">
      <div class="messenger__messages-search">
        <div class="messenger__messages-search-input">
          <input type="text" placeholder="Введите имя файла" v-model="searchFilesQuery">
        </div>
      </div>
      <div class="messenger__messages-search-results">
        <div class="messenger__message-wrapper" v-for="file in chatSearchFilesResults" @click="goto(file, $event)">
          <ConversationMessage :message="file"></ConversationMessage>
        </div>
      </div>
    </div>
  </div>
</div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import ConversationMessage from "../ConversationFeed/ConversationMessage/ConversationMessage.vue";

export default {
  name: "ConversationSearch",
  components: {ConversationMessage},
  data() {
    return {
      searchMessagesQuery: '',
      searchFilesQuery: '',
      searchMessagesDate: '',
    }
  },
  computed: {
    ...mapGetters(['chat', 'chatSearchMessagesResults', 'chatSearchFilesResults']),
  },
  watch: {
    searchMessagesDate() {
      this.searchMessages();
    },
    searchMessagesQuery() {
      this.searchMessages();
    },
    searchFilesQuery() {
      this.searchFiles();
    },
  },
  methods: {
    ...mapActions(['findMessagesInChat', 'findFilesInChat', 'loadMessages', 'toggleChatSearchMode']),
    goto(message, event) {
      event.stopPropagation();
      this.loadMessages({
        reset: false,
        goto: message.id
      });
      this.toggleChatSearchMode();
    },
    searchMessages() {
      this.findMessagesInChat({
        text: this.searchMessagesQuery,
        chat_id: this.chat.id,
        date: this.searchMessagesDate,
      });
    },
    searchFiles() {
      this.findFilesInChat({
        text: this.searchFilesQuery,
        chat_id: this.chat.id,
      });
    },
  }
}
</script>

<style scoped>

.messenger__container-scroll {
  position: relative;
  height: 100%;
  display: flex;
  flex-flow: column;
  flex-direction: row;
  flex-wrap: wrap;
  flex: 1;
  overflow: auto;
  border: 1px solid #c6c6c6;
}

.messenger__messages-container {
  position: relative;
  height: 100%;
  width: 100%;
  flex: 1;
  overflow: hidden;
  display: flex;
  flex-flow: column;
  flex-wrap: nowrap;
  border: 1px solid #e8e7e7;
  min-width: 250px;
}

.messenger__messages-search {
  display: flex;
  flex-flow: row;
  padding: 10px;
  border-bottom: 1px solid #c6c6c6;
}

.messenger__messages-search-input {
  flex: 1;
}

.messenger__messages-search-input input {
  width: 100%;
  padding: 10px;
  border: 1px solid #c6c6c6;
  border-radius: 5px;
}

.messenger__messages-search-date {
  margin-left: 10px;
}

.messenger__messages-search-date input {
  padding: 10px;
  border: 1px solid #c6c6c6;
  border-radius: 5px;
}

.messenger__messages-search-results {
  max-width: 100%;
}

.messenger__message-wrapper {
  cursor: pointer;
}

</style>
