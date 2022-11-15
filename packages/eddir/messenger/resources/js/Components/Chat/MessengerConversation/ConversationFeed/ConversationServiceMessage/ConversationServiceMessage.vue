<template>
  <div class="messenger__message-notification">
    <div class="messenger__message-notification-content">
      <div class="messenger__message-notification-content-text">
        <template v-if="message.type === 'join'">
          <span class="messenger__message-notification__link">{{ message.user.name }}</span>
          добавил
          <span class="messenger__message-notification__link">{{ message.payload.user.name }}</span>
        </template>
        <template v-if="message.type === 'leave'">
          <span class="messenger__message-notification__link">{{ message.user.name }}</span>
          удалил
          <span class="messenger__message-notification__link">{{ message.payload.user.name }}</span>
        </template>
        <template v-if="message.type === 'rename'">
          <span class="messenger__message-notification__link">{{ message.user.name }}</span>
          переименовал чат в
          <span class="messenger__message-notification__link">{{ message.payload.chat.title }}</span>
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
