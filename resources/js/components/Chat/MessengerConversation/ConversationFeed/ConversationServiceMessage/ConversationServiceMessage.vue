<template>
  <div class="messenger__message-notification">
    <div class="messenger__message-notification-content">
      <div class="messenger__message-notification-content-text">
        <template v-if="message.event.type === 'join'">
          <span class="messenger__message-notification__link">{{ message.sender.name }}</span>
          добавил
          <span class="messenger__message-notification__link">{{ message.event.payload.user.name }}</span>
        </template>
        <template v-else-if="message.event.type === 'leave'">
          <template v-if="message.sender.id === message.event.payload.user.id">
            <span class="messenger__message-notification__link">{{ message.sender.name }}</span>
            покинул чат
          </template>
          <template v-else>
            <span class="messenger__message-notification__link">{{ message.sender.name }}</span>
            удалил
            <span class="messenger__message-notification__link">{{ message.event.payload.user.name }}</span>
          </template>
        </template>
        <template v-else-if="message.event.type === 'rename'">
          <span class="messenger__message-notification__link">{{ message.sender.name }}</span>
          переименовал чат в
          <span class="messenger__message-notification__link">{{ message.event.payload.chat.title }}</span>
        </template>
        <template v-else-if="message.event.type === 'pin'">
          <span class="messenger__message-notification__link">{{ message.sender.name }}</span>
          закрепил сообщение
        </template>
        <template v-else-if="message.event.type === 'unpin'">
          <span class="messenger__message-notification__link">{{ message.sender.name }}</span>
          открепил сообщение
        </template>
        <template v-else-if="message.event.type === 'chat_created'">
          <span class="messenger__message-notification__link">{{ message.sender.name }}</span>
          создал новый чат
        </template>
        <template v-else-if="message.event.type === 'delete_chat'">
          <span class="messenger__message-notification__link">{{ message.sender.name }}</span>
          удалил этот чат
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  name: "ConversationServiceMessage",
  props: {
    message: {
      type: Object,
      required: true
    },
  },
  computed: {
    ...mapGetters(['user'])
  }
}
</script>

<style>
.messenger__message-notification {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 5px;
}

.messenger__message-notification-content {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
  background-color: #f5f5f5;
  border-radius: 10px;
  padding: 5px 10px;
  max-width: 100%;
}

.messenger__message-notification-content-text {
  font-size: 14px;
  color: #000;
  max-width: 100%;
}
</style>
