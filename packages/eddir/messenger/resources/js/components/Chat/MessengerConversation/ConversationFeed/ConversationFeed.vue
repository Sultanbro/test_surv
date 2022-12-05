<template>
  <div class="messenger__container-scroll" ref="messengerContainer" id="messenger_container"
       @click="contextMenuVisible = false"
       @scroll="onScroll">
    <ContextMenu
      :show="contextMenuVisible"
      :x="contextMenuX"
      :y="contextMenuY"
      :parent-element="$refs.messengerContainer"
    >
      <a href="javascript:" class="messenger__context-menu_reaction" @click="react(1)">
        &#128077;
      </a>
      <a href="javascript:" class="messenger__context-menu_reaction" @click="react(2)">
        &#128078;
      </a>
      <a href="javascript:" class="messenger__context-menu_reaction" @click="react(3)">
        &#10004;
      </a>
      <a href="javascript:" class="messenger__context-menu_reaction" @click="react(4)">
        &#10006;
      </a>
      <a href="javascript:" class="messenger__context-menu_reaction" @click="react(5)">
        &#10067;
      </a>

      <a href="javascript:" @click="startEditMessage(contextMenuMessage)"
         v-if="contextMenuMessage && user && contextMenuMessage.sender_id === user.id">
        Отредактировать
      </a>
      <a href="javascript:" @click="citeMessage(contextMenuMessage)">
        Цитировать
      </a>
      <a href="javascript:" @click="pinMessage(contextMenuMessage)">
        Закрепить
      </a>
      <a href="javascript:" @click="remove(contextMenuMessage)"
         v-if="contextMenuMessage && user && contextMenuMessage.sender_id === user.id">
        Удалить
      </a>
    </ContextMenu>
    <div class="messenger__messages-container" id="messenger__messages">
      <template v-for="d in messagesMap">
        <div class="messenger__messages-date">
          <span>{{ d[0].created_at | formatDate }}</span>
        </div>
        <div v-for="(message, index) in d" :key="message.id"
             class="messenger__message-wrapper" :id="'messenger_message_' + message.id"
             @contextmenu.prevent="!message.event && showChatContextMenu(message, ...arguments)">
          <ConversationServiceMessage v-if="message.event" :message="message"/>
          <ConversationMessage v-else :message="message"
                               @active="activeMessageId = message.id"
                               :active="activeMessageId === message.id"
                               :last="index === d.length - 1"
                               @loadImage="index === d.length - 1 && scrollBottom"/>
        </div>
      </template>
    </div>
    <div class="messenger__loader" v-show="this.isLoading">
      <div class="messenger__loader-spinner">
        <div class="messenger__loader-spinner-item"></div>
        <div class="messenger__loader-spinner-item"></div>
        <div class="messenger__loader-spinner-item"></div>
        <div class="messenger__loader-spinner-item"></div>
      </div>
    </div>
  </div>
</template>

<script>
import ConversationMessage from "./ConversationMessage/ConversationMessage.vue";
import {mapActions, mapGetters} from "vuex";
import ContextMenu from "../../ContextMenu/ContextMenu.vue";
import ConversationServiceMessage from "./ConversationServiceMessage/ConversationServiceMessage.vue";
import moment from "moment/moment";

export default {
  name: "ConversationFeed",
  components: {
    ConversationMessage,
    ConversationServiceMessage,
    ContextMenu
  },
  computed: {
    ...mapGetters([
      'messagesMap', 'messages',
      'user',
      'messagesOldEndReached', 'messagesNewEndReached',
      'scrollingPosition',
      'messagesLoading', 'isLoading'
    ]),
  },
  updated() {
    if (this.scrollingPosition !== -1) {
      this.scroll();
    }
  },
  data() {
    return {
      contextMenuVisible: false,
      contextMenuTarget: null,
      contextMenuX: 0,
      contextMenuY: 0,
      contextMenuMessage: null,
      activeMessageId: 0
    }
  },
  methods: {
    ...mapActions([
      'startEditMessage', 'citeMessage',
      'deleteMessage', 'pinMessage', 'reactMessage',
      'loadMoreNewMessages', 'loadMoreOldMessages',
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
    }
  },
  filters: {
    formatDate(date) {
      // if today show only hour and minutes
      if (moment(date).isSame(moment(), 'day')) {
        return moment(date).format('HH:mm');
      }
      // if yesterday show only hour and minutes and yesterday
      if (moment(date).isSame(moment().subtract(1, 'day'), 'day')) {
        return 'Вчера';
      }
      // if older than yesterday show hour, minutes, day and month
      return moment(date).format('DD.MM');
    }
  }
}
</script>

<style scoped>

.messenger__container-scroll {
  background: #ffffff;
  display: flex;
  flex: 1;
  overflow-y: auto;
  margin-right: 1px;
  -webkit-overflow-scrolling: touch;
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

.messenger__context-menu_reaction {
  display: inline-block;
}

.messenger__messages-date {
  display: block;
  text-align: center;
  overflow: hidden;
  white-space: nowrap;
}

.messenger__messages-date > span {
  position: relative;
  display: inline-block;
  color: #a0a0a4;
}

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
}

.messenger__loader {
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  height: 100%;
  width: 100%;
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
