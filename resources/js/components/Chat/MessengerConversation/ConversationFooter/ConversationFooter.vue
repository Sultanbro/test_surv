<template>
  <div class="messenger__chat-footer">
    <div class="messenger__box-footer messenger__box-footer-border">
      <textarea v-model="body" @keydown.enter="performMessage" id="messengerMessageInput"
                cols="30" rows="10" class="messenger__textarea" placeholder="Ввести сообщение"></textarea>
      <div class="messenger__message-input" id="messengerInput">
        <div class="messenger__attachment">
          <EmojiPopup @append="appendEmoji"></EmojiPopup>
          <div class="messenger__attachment-item" @click="$refs.messengerFile.click()">
            <input type="file" ref="messengerFile" style="display:none" @change="prepareFile">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 297.126 297.126">
              <g>
                <path fill="#5ebee9"
                      d="m272.329,43.831c-34.802-34.802-90.486-33.062-123.549,0l-130.509,130.509c-24.362,24.362-24.362,62.644 1.42109e-14,87.006 12.181,12.181 27.842,17.401 43.503,17.401s31.322-6.96 41.763-19.141l118.328-118.328c13.921-13.921 13.921-34.802 0-48.723-13.921-13.921-34.802-13.921-48.723,0l-81.786,81.786c-3.48,3.48-3.48,8.701 0,12.181s8.701,3.48 12.181,0l81.786-81.786c6.96-6.96 17.401-6.96 24.362,0 6.96,6.96 6.96,17.401 0,24.362l-118.329,118.328c-15.661,17.401-43.503,17.401-60.904,0-17.401-15.661-17.401-43.503 0-60.904l130.509-130.51c27.842-27.842 71.345-27.842 99.187,0s27.842,71.345 0,99.187l-93.966,93.966c-3.48,3.48-3.48,8.701 0,12.181 3.48,3.48 8.701,3.48 12.181,0l93.966-93.966c33.062-34.802 33.062-90.486 0-123.549z"/>
              </g>
            </svg>
          </div>
<!--          <div class="messenger__attachment-item">-->
<!--            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">-->
<!--              <path fill="#5ebee9"-->
<!--                    d="M192 0C139 0 96 43 96 96V256c0 53 43 96 96 96s96-43 96-96V96c0-53-43-96-96-96zM64 216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 89.1 66.2 162.7 152 174.4V464H120c-13.3 0-24 10.7-24 24s10.7 24 24 24h72 72c13.3 0 24-10.7 24-24s-10.7-24-24-24H216V430.4c85.8-11.7 152-85.3 152-174.4V216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 70.7-57.3 128-128 128s-128-57.3-128-128V216z"/>-->
<!--            </svg>-->
<!--          </div>-->
        </div>
        <button class="messenger__message-send" @click="performMessage">
          <template v-if="editMessage">Отредактировать</template>
          <template v-else>Отправить</template>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import EmojiPopup from "./EmojiPopup/EmojiPopup.vue";

export default {
  name: "ConversationFooter",
  components: {EmojiPopup},
  data() {
    return {
      body: ""
    };
  },
  computed: {
    ...mapGetters(['chat', 'editMessage'])
  },
  watch: {
    editMessage(message) {
      if (message) {
        this.body = message.body;
      } else {
        this.body = "";
      }
    }
  },
  methods: {
    ...mapActions(["sendMessage", "editMessageAction", "uploadFile"]),
    performMessage(e) {
      let text = this.body.trim();
      if (text && this.chat) {
        if (this.editMessage) {
          this.editMessageAction(text);
        } else {
          this.sendMessage(text)
          this.body = "";
          e.preventDefault();
          this.$nextTick(() => {
            document.getElementById("messengerMessageInput").focus();
          });
        }
      }
    },
    appendEmoji(emoji) {
      this.body += emoji;
    },
    prepareFile() {
      let file = this.$refs.messengerFile.files[0];
      if (file) {
        this.uploadFile(file);
      }
    }
  }
}
</script>

<style>

.messenger__chat-footer {
  width: 100%;
  border-bottom-right-radius: 4px;
  border-top: 2px solid #5ebee9;
  z-index: 10;
  margin: 0 10px;
}

.messenger__box-footer {
  display: flex;
  position: relative;
  padding: 5px 8px 10px 8px;
}

.messenger__textarea {
  min-height: 20px;
  max-height: 80px;
  overflow-x: hidden !important;
  overflow-y: auto !important;
  width: 100%;
  line-height: 20px;
  outline: 0;
  resize: none;
  border: none;
  box-sizing: content-box;
  font-size: 16px;
  background: #fff;
  color: #0a0a0a;
  caret-color: #1976d2;
  padding: 0;
}

.messenger__textarea:focus {
  border: none;
  outline: none;
  -webkit-box-shadow: none;
  box-shadow: none;
}

.messenger__message-input {
  display: flex;
  flex: 1;
  flex-direction: column;
  align-items: center;
  margin-right: 10px;
}

.messenger__attachment {
  display: flex;
  align-items: center;
}

.messenger__attachment-item {
  width: 35px;
  height: 35px;
  padding: 7px;
  cursor: pointer;
}

.messenger__attachment-item:hover {
  background: #dff2fb;
  border-radius: 50%;
}

.messenger__attachment-item svg {
  width: 100%;
  height: 100%;
}

.messenger__message-send {
  height: 35px;
  border: none;
  cursor: pointer;
  background: #5ebee9;
  color: #fff;
  border-radius: 30px;
  padding: 0 20px;
  margin: 5px 0 10px 0;
}

</style>
