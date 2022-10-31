<template>
  <div class="messenger__message-wrapper" @contextmenu.prevent="$emit('clicked', $event)">
    <div :class="message.sender_id === user.id ?
    'messenger__message-box-right' :
    'messenger__message-box-left'">
      <div class="messenger__message-container">
        <div :class="messageCardClass">
          <div class="messenger__format-message-wrapper">
            <template v-if="message.files">
              <div class="messenger__message-files">
                <div class="messenger__message-file" v-for="file in [message.files]">
                  <template v-if="isImage(file)">
                    <div class="">
                      <img :src="file.file_path" alt="">
                    </div>
                  </template>
                  <template v-else>
                    <div class="messenger__message-file-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="30" height="30">
                        <path
                          d="M0 64C0 28.65 28.65 0 64 0H229.5C246.5 0 262.7 6.743 274.7 18.75L365.3 109.3C377.3 121.3 384 137.5 384 154.5V448C384 483.3 355.3 512 320 512H64C28.65 512 0 483.3 0 448V64zM336 448V160H256C238.3 160 224 145.7 224 128V48H64C55.16 48 48 55.16 48 64V448C48 456.8 55.16 464 64 464H320C328.8 464 336 456.8 336 448z"/>
                      </svg>
                    </div>
                    <div class="messenger__message-file-name">
                      <a :href="file.file_path" download>{{ file.name }}</a>
                    </div>
                  </template>
                </div>
              </div>
            </template>
            <template v-else>
              <div class="messenger__format-container">
                <span v-text="message.body"></span>
              </div>
            </template>
          </div>
          <div class="messenger__text-timestamp">
            <span>
              {{ message.created_at | moment }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {mapGetters} from "vuex";
import moment from "moment";

export default {
  name: "ConversationMessage",
  props: {
    message: {},
  },
  computed: {
    ...mapGetters(['user']),
    messageCardClass() {
      return {
        'messenger__message-card': true,
        'messenger__message__failed': this.message.failed,
      }
    }
  },
  methods: {
    isImage(file) {
      const ext = file.file_path.split('.').pop();
      return ['jpg', 'jpeg', 'png', 'gif'].includes(ext);
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
}
</script>

<style>

.messenger__message-box-right, .messenger__message-box-left {
  display: flex;
  flex: 0 0 50%;
  line-height: 1.4;
}

.messenger__message-box-left {
  justify-content: flex-start;
}

.messenger__message-box-right {
  justify-content: flex-end;
}

.messenger__message-container {
  position: relative;
  padding: 2px 10px;
  align-items: end;
  min-width: 100px;
  box-sizing: content-box;
}

.messenger__message-card {
  border-radius: 8px;
  font-size: 14px;
  padding: 6px 9px 3px;
  white-space: pre-line;
  max-width: 360px;
  -webkit-transition-property: box-shadow, opacity;
  transition-property: box-shadow, opacity;
  transition: box-shadow .28s cubic-bezier(.4, 0, .2, 1);
  will-change: box-shadow;
  box-shadow: 0 1px 1px -1px #0000001a, 0 1px 1px -1px #0000001c, 0 1px 2px -1px #0000001c;
}

.messenger__message__failed {
  background-color: #ffcdd2 !important;
}

.messenger__message-box-right .messenger__message-card {
  background-color: #f5f5f5;
  color: #0a0a0a;
  float: right;
}

.messenger__message-box-left .messenger__message-card {
  background-color: #eff8fd;
  color: #0a0a0a;
}

.messenger__text-timestamp {
  font-size: 10px;
  color: #828c94;
  text-align: right;
}

.messenger__message-files {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.messenger__message-file {
  display: flex;
  align-items: center;
  margin-bottom: 5px;
}

.messenger__message-file-icon {
  font-size: 20px;
  margin-right: 5px;
}

.messenger__message-file-name a {
  color: #001da1;
}

.messenger__message-file-name a:hover {
  text-decoration: underline;
}

.messenger__message-file-name a:visited {
  color: rgba(0, 9, 72, 0.94);
}


</style>
