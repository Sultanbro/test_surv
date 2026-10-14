<template>
  <div class="messenger__chat-footer">
    <div class="messenger__box-footer messenger__box-footer-border">
      <template v-if="isRecordingAudio">
        <SpectrumAnalyser class="messenger__chat-footer_spectrum" :fill-style="'#5ebee9'"></SpectrumAnalyser>
      </template>
      <template v-else>
        <div class="messenger__message-input messenger__message-text-input">
          <div v-if="citedMessage" class="messenger__message-input_cite">
            <div class="messenger__message-input_cite-body">
              {{ citedMessage.sender.name }}: {{ citedMessage.body }}
            </div>
            <span @click="closeCitation">x</span>
          </div>
          <textarea v-model="body"
                    @keydown.enter="performMessage" @paste="pasteMessage"
                    id="messengerMessageInput" class="messenger__textarea"
                    placeholder="Ввести сообщение"
          ></textarea>
          <div v-for="file in files" class="messenger__input_file-attachment">
            <div>
              <div>{{ file.name }}</div>
              <div>{{ file.size | fileSizeFormat }}</div>
            </div>
            <div class="messenger__input_file-attachment_remove" @click="removeFile(file, $event)">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                <path
                  d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z"/>
              </svg>
            </div>
          </div>
        </div>
      </template>
      <div class="messenger__message-input" id="messengerInput">

        <AudioDictaphone
          class="messenger__attachment"
          @stop="handleRecording"
          @start="setRecordingAudio(true)"
          @delete="setRecordingAudio(false)"
        >
          <template #default="{ isRecording, startRecording, stopRecording, deleteRecording }">
            <template v-if="!isRecording">
              <EmojiPopup @append="appendEmoji"></EmojiPopup>

              <div class="messenger__attachment-item" @click="$refs.messengerFile.click()">
                <input type="file" ref="messengerFile" style="display:none" @change="prepareFiles" multiple="multiple">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 297.126 297.126">
                  <g>
                    <path fill="#5ebee9"
                          d="m272.329,43.831c-34.802-34.802-90.486-33.062-123.549,0l-130.509,130.509c-24.362,24.362-24.362,62.644 1.42109e-14,87.006 12.181,12.181 27.842,17.401 43.503,17.401s31.322-6.96 41.763-19.141l118.328-118.328c13.921-13.921 13.921-34.802 0-48.723-13.921-13.921-34.802-13.921-48.723,0l-81.786,81.786c-3.48,3.48-3.48,8.701 0,12.181s8.701,3.48 12.181,0l81.786-81.786c6.96-6.96 17.401-6.96 24.362,0 6.96,6.96 6.96,17.401 0,24.362l-118.329,118.328c-15.661,17.401-43.503,17.401-60.904,0-17.401-15.661-17.401-43.503 0-60.904l130.509-130.51c27.842-27.842 71.345-27.842 99.187,0s27.842,71.345 0,99.187l-93.966,93.966c-3.48,3.48-3.48,8.701 0,12.181 3.48,3.48 8.701,3.48 12.181,0l93.966-93.966c33.062-34.802 33.062-90.486 0-123.549z"/>
                  </g>
                </svg>
              </div>

              <div class="messenger__attachment-item" @click="startRecording">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                  <path fill="#5ebee9"
                        d="M192 0C139 0 96 43 96 96V256c0 53 43 96 96 96s96-43 96-96V96c0-53-43-96-96-96zM64 216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 89.1 66.2 162.7 152 174.4V464H120c-13.3 0-24 10.7-24 24s10.7 24 24 24h72 72c13.3 0 24-10.7 24-24s-10.7-24-24-24H216V430.4c85.8-11.7 152-85.3 152-174.4V216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 70.7-57.3 128-128 128s-128-57.3-128-128V216z"/>
                </svg>
              </div>

            </template>
            <template v-else>

              <div class="messenger__attachment-item" @click="stopRecording($event)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                  <path
                    d="M0 128C0 92.7 28.7 64 64 64H320c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128z"/>
                </svg>
              </div>
              <div class="messenger__attachment-item" @click="deleteRecording($event)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                  <path
                    d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z"/>
                </svg>
              </div>

            </template>
          </template>
        </AudioDictaphone>

        <button v-if="!isRecordingAudio" class="messenger__message-send" @click="performMessage">
          <template v-if="editMessage">Отредактировать</template>
          <template v-else>Отправить</template>
        </button>

        <span v-if="isRecordingAudio" class="messenger__attachment_recording">
            {{ recordingTime | countdownFormat }}
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import EmojiPopup from "./EmojiPopup/EmojiPopup.vue";
import AudioDictaphone from "./AudioDictaphone/AudioDictaphone.vue";
import SpectrumAnalyser from "./SpectrumAnalyser/SpectrumAnalyser.vue";


export default {
  name: "ConversationFooter",
  components: {EmojiPopup, AudioDictaphone, SpectrumAnalyser},
  data() {
    return {
      body: "",
      files: [],
      isRecordingAudio: false,
      recordingTime: 0,
    };
  },
  computed: {
    ...mapGetters(['chat', 'editMessage', 'citedMessage'])
  },
  watch: {
    editMessage(message) {
      if (message) {
        this.body = message.body;
      } else {
        this.body = "";
      }
    },
    recordingTime(duration) {
      if (duration > 0) {
        setTimeout(() => {
          this.recordingTime = duration + 1;
        }, 10);
      }
    },
  },
  methods: {
    ...mapActions(["sendMessage", "editMessageAction", "uploadFiles", "citeMessage"]),
    performMessage(e) {
      let text = this.body.trim();
      if ((text || this.files.length > 0) && this.chat) {
        if (this.editMessage) {
          this.editMessageAction(text);
        } else {
          e.preventDefault();
          if (this.files.length > 0) {
            this.uploadFiles({'files': this.files, 'caption': text});
            this.files = [];
          } else {
            this.sendMessage(text)
          }
          this.body = "";
          this.$nextTick(() => {
            document.getElementById("messengerMessageInput").focus();
          });
        }
      }
    },
    appendEmoji(emoji) {
      this.body += emoji;
    },
    pasteMessage(e) {
      const items = (e.clipboardData || e.originalEvent.clipboardData).items;
      for (let index in items) {
        let item = items[index];
        if (item.kind === 'file') {
          this.files.push(item.getAsFile());
        }
      }
    },
    removeFile(file, event) {
      event.stopPropagation();
      this.files = this.files.filter(f => f !== file);
    },
    prepareFiles() {
      let files = this.$refs.messengerFile.files;
      for (let file of files) {
        this.files.push(file);
      }
    },
    handleRecording({blob}) {
      this.uploadFiles({'files': [blob], 'caption': ""});
      this.isRecordingAudio = false;
    },
    setRecordingAudio(value) {
      this.isRecordingAudio = value;
      this.recordingTime += 1;
    },
    closeCitation(event) {
      event.stopPropagation();
      this.citeMessage(null);
    },
  },
  filters: {
    countdownFormat(value) {
      let seconds = Math.floor(value / 100);
      let minutes = Math.floor(seconds / 60);
      let santiseconds = value % 100;
      seconds = seconds % 60;
      return `${minutes}:${seconds < 10 ? "0" + seconds : seconds},${santiseconds < 10 ? "0" + santiseconds : santiseconds}`;
    },
    fileSizeFormat(value) {
      if (value < 1024) {
        return `${value} B`;
      } else if (value < 1024 * 1024) {
        return `${Math.floor(value / 1024)} KB`;
      } else {
        return `${Math.floor(value / 1024 / 1024)} MB`;
      }
    }
  }
}
</script>

<style>

.messenger__chat-footer {
  width: 100%;
  border-bottom-right-radius: 4px;
  border-top: 2px solid #e2e2e2;
  max-height: 30vh;
  overflow-y: auto;
  overflow-x: hidden;
}

.messenger__box-footer {
  display: flex;
  position: relative;
  padding: 5px 8px 10px 8px;
  background-color: #fff;
}

.messenger__textarea {
  min-height: 20px;
  max-height: 80px;
  overflow-x: hidden !important;
  overflow-y: auto !important;
  width: 100%;
  height: 100%;
  line-height: 20px;
  outline: 0;
  resize: none;
  border: none;
  box-sizing: content-box;
  font-size: 14px;
  background: #fff;
  color: #0a0a0a;
  caret-color: #1976d2;
  padding: 5px 10px 0 5px;
}

.messenger__textarea:focus {
  border: none;
  outline: none;
  -webkit-box-shadow: none;
  box-shadow: none;
}

.messenger__chat-footer_spectrum {
  min-height: 20px;
  max-height: 80px;
  width: 100%;
  resize: none;
  border: none;
  box-sizing: content-box;
  background: #fff;
  padding: 5px 10px 0 5px;
}

.messenger__message-input {
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: center;
  margin-right: 10px;
  min-width: 0;
}

.messenger__message-text-input {
  flex-grow: 1;
  align-items: flex-start;
}

.messenger__input_file-attachment {
  margin: 5px;
  line-height: 1.5;
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
  width: 100%;
}

.messenger__input_file-attachment_remove {
  width: 10px;
  height: 10px;
  cursor: pointer;
  margin: 10px;
  min-width: 10px;
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

.messenger__attachment_recording {
  align-items: center;
  justify-content: center;
  padding: 7px;
}

.messenger__message-input_cite {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
  justify-content: space-between;
  width: 100%;
  max-height: 40px;
  padding: 5px 10px 0 5px;
  background: #fff;
  border-radius: 4px;
  margin-bottom: 5px;
}

.messenger__message-input_cite-body {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.messenger__message-input_cite span {
  font-size: 14px;
  color: #0a0a0a;
  cursor: pointer;
}

</style>
