<template>
  <div ref="feed" class="feed">
    <template v-if="contact">
      <div class="pin-message" v-if="pinnedMessage">
        <div>
        <div class="pin-message-header">
          <div class="pin-message-header-info">
            <div class="pin-message-header-info-name">
<!--              <span>{{ contact.name }}</span>-->
              <span>&nbsp;</span>
            </div>
            <div class="pin-message-header-info-time">
<!--              <span>{{ contact.time }}</span>-->
            </div>
          </div>
        </div>
        <div class="pin-message-body">
          <div class="pin-message-body-text">
            <span>{{ pinnedMessage.body }}</span>
          </div>
        </div>
        </div>
        <div class="pin-message-close" @click="unpin(pinnedMessage)">x</div>
      </div>

      <ul>
        <li
          v-for="message in messages"
          :key="message.id"
          :class="`message${message.sender_id === user.id ? ' sent' : ' received'}`"
          @contextmenu.prevent.stop="handleClick($event, message)"
        >
          <div class="text">
            {{ message.body }}
            <span class="message-time">
              {{ message.created_at | moment }}
            </span>
          </div>
        </li>
      </ul>
      <vue-simple-context-menu
        element-id="feedMenu"
        :options="options"
        ref="vueSimpleContextMenuFeed"
        @option-clicked="optionClicked"
      />
    </template>
  </div>
</template>

<script>

import VueSimpleContextMenu from 'vue-simple-context-menu';
import 'vue-simple-context-menu/dist/vue-simple-context-menu.css';
import moment from "moment";
import API from "./API.vue";

export default {
  props: {
    contact: {
      type: Object,
      default: function () {
        return null;
      },
    },
    messages: {
      type: Array,
      required: true,
    },
    user: {
      type: Object,
      required: true,
    },
    pinnedMessage: {
      type: Object,
      default: function () {
        return null;
      },
    },
  },
  data: function () {
    return {
      options: [],
      menu: {
        pin: {
          name: 'Закрепить',
          icon: 'pin',
          action: (item) => {
            API.pinMessage(item.id);
          },
        },
        edit: {
          name: 'Редактировать',
          icon: 'edit',
          action: (item) => {
            this.$emit('mode', 'edit', item);
          },
        },
        delete: {
          name: 'Удалить',
          icon: 'delete',
          action: (item) => {
            this.$emit('delete', item);
          },
        },
      },
    };
  },
  components: {VueSimpleContextMenu},
  watch: {
    contact() {
      this.scrollToBottom();
      // this.pinnedMessage = this.messages[0];
    },
    messages() {
      this.scrollToBottom();
    },
  },
  updated() {
    this.scrollToBottom();
  },
  methods: {
    handleClick(e, item) {
      // if item is my message, show all options, else show only pin
      if (item.sender_id === this.user.id) {
        this.options = [
          this.menu.pin,
          this.menu.edit,
          this.menu.delete,
        ];
      } else {
        this.options = [
          this.menu.pin,
        ];
      }
      this.$refs.vueSimpleContextMenuFeed.showMenu(event, item);
    },
    optionClicked(item) {
      item.option.action(item.item);
    },
    scrollToBottom() {
      setTimeout(() => {
        this.$refs.feed.scrollTop =
          this.$refs.feed.scrollHeight - this.$refs.feed.clientHeight;
      }, 50);
    },
    unpin(message) {
      API.unpinMessage(message.id);
    },
  },
  filters: {
    moment: function (date) {
      // if today show only hour and minutes
      if (moment(date).isSame(moment(), 'day')) {
        return moment(date).format('HH:mm');
      }

      // if yesterday show only hour and minutes and yesterday
      if (moment(date).isSame(moment().subtract(1, 'day'), 'day')) {
        return 'Вчера, ' + moment(date).format('HH:mm');
      }

      // if older than yesterday show hour, minutes, day and month
      return moment(date).format('DD.MM, HH:mm');
    }
  }
};
</script>

<style>
.feed {
  background: #FFFFFF;
  overflow: auto;
}

.feed ul {
  list-style-type: none;
  padding: 5px;
}

.feed ul li.message {
  margin: 10px 0;
  width: 100%;
}

.feed ul li.message .text {
  max-width: 400px;
  border-radius: 5px;
  padding: 12px;
  display: inline-block;
}

.feed ul li.message.received {
  text-align: left;
}

.feed ul li.message.received .text {
  background: #b2b2b2;
}

.feed ul li.message.sent {
  text-align: right;
}

.feed ul li.message.sent .text {
  background: #f2f2f2;
}

/* total width */
.feed::-webkit-scrollbar {
  background-color: #fff;
  width: 16px;
}

/* background of the scrollbar except button or resizer */
.feed::-webkit-scrollbar-track {
  background-color: #fff;
}

/* scrollbar itself */
.feed::-webkit-scrollbar-thumb {
  background-color: #babac0;
  border-radius: 16px;
  border: 4px solid #fff;
}

/* set button(top and bottom of the scrollbar) */
.feed::-webkit-scrollbar-button {
  display: none;
}

.pin-message {
  position: sticky;
  top: 0;
  border-radius: 5px;
  padding: 10px;
  background: #fff;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

.pin-message-close {
  position: absolute;
  right: 10px;
  top: 10px;
  font-size: 20px;
  color: #b2b2b2;
  cursor: pointer;
}

.pin-message-header {
  display: flex;
  align-items: center;
}

.pin-message-header-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
  margin-right: 10px;
}

.pin-message-header-avatar img {
  width: 100%;
  height: 100%;
}

.pin-message-header-info {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: flex-start;
}

.pin-message-header-info .pin-message-header-info-name {
  font-size: 14px;
  font-weight: bold;
  color: #000;
}

.pin-message-header-info .pin-message-header-info-time {
  font-size: 12px;
  color: #babac0;
}

.pin-message-body {
}

.pin-message-body .pin-message-body-text {
  font-size: 14px;
  color: #000;
}

.pin-message-body .pin-message-body-attach {
  font-size: 12px;
  color: #babac0;
}

.pin-message-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding: 10px;

}

.pin-message-footer .pin-message-footer-icon {
  width: 20px;
  height: 20px;
  margin-right: 10px;
}

.pin-message-footer .pin-message-footer-icon img {
  width: 100%;
  height: 100%;
}

.pin-message-footer-actions {
  display: flex;
  align-items: center;
  justify-content: flex-end;
}

.pin-message-footer-actions .pin-message-footer-icon {
  width: 20px;
  height: 20px;
  margin-right: 10px;
}

.pin-message-footer-actions .pin-message-footer-icon img {
  width: 100%;
  height: 100%;
}

.pin-message-footer-actions .pin-message-footer-icon:hover {
  cursor: pointer;
}

.pin-message-footer-actions .pin-message-footer-icon:hover img {
  filter: brightness(0.8);
}

.pin-message-footer-actions .pin-message-footer-icon:hover img:hover {
  filter: brightness(1);
}

.pin-message-footer-actions .pin-message-footer-icon:hover .pin-message-footer-icon-text {
  color: #fff;
}

.pin-message-footer-actions .pin-message-footer-icon:hover .pin-message-footer-icon-text:hover {
  color: #fff;
}

.pin-message-footer-actions .pin-message-footer-icon-text {
  font-size: 12px;
  color: #babac0;
}

.pin-message-footer-actions .pin-message-footer-icon-text:hover {
  color: #babac0;
}

.received .message-time {
  display: block;
  font-size: 12px;
  color: #eaeaea;
}

.sent .message-time {
  display: block;
  font-size: 12px;
  color: #babac0;
}
</style>
