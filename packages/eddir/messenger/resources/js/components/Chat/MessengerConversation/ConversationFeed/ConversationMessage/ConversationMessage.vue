<template>
  <div :class="message.sender_id === user.id ?
    'messenger__message-box-right' :
    'messenger__message-box-left'">
    <AlternativeAvatar v-if="message.sender_id !== user.id" :title="message.sender.name"
                       :image="message.sender.img_url"></AlternativeAvatar>
    <div class="messenger__message-container">
      <div :class="messageCardClass">
        <div class="messenger__format-message-wrapper">
          <div class="messenger__format-container_parent" v-if="message.parent" @click="goto(message.parent, $event)">
            <div class="messenger__format-container_parent-author">{{ message.parent.sender.name }}</div>
            <div class="messenger__format-container_parent-message">{{ message.parent.body }}</div>
          </div>
          <div class="messenger__format-container">
            <span v-text="message.body"></span>
          </div>
          <div v-if="isGallery()" class="messenger__message-files messenger__message-files_group">
            <div v-for="(file, key) in message.files.slice(0, 3)" :key="key"
                 :class="{
                    'messenger__message-file_group': true,
                    'messenger__last-row': key === 2 && message.files.length > 2,
                    'messenger__last-column': (key === 1 && message.files.length > 2) || (key === 2 && message.files.length > 1)
                  }"
            >
              <div @click="openImage(file)" class="messenger__message-file-image">
                <img v-on:load="$emit('loadImage')"
                     :src="file.thumbnail_path ? file.thumbnail_path : file.file_path" alt="file.name">
                <div v-if="message.files.length > 3 && key === 2" class="messenger__message-files_group-count">
                  <span>+{{ message.files.length - 3 }}</span>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="messenger__message-files">
            <div class="messenger__message-file" v-for="file in message.files">
              <template v-if="isImage(file)">
                <div class="messenger__message-file-image" @click="openImage(file)">
                  <img v-on:load="$emit('loadImage')"
                       :src="file.thumbnail_path ? file.thumbnail_path : file.file_path" alt="file.name">
                </div>
              </template>
              <template v-else-if="isAudio(file)">
                <div class="messenger__message-file-audio">
                  <VoiceMessage :audioSource="file.file_path"
                                :isActive="active"
                                @play="$emit('active')"
                  >
                  </VoiceMessage>
                </div>
              </template>
              <template v-else>
                <div class="messenger__message-file-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="25" height="25">
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
        </div>
      </div>
      <div v-if="message.readers && message.readers.length > 0" class="messenger__message-reactions">
        <template v-if="last && message.readers && message.readers.length > 0 && message.sender_id === user.id">
          <MessageReaders :message="message" :user="user"></MessageReaders>
        </template>

        <template v-for="reaction in reactions" v-if="reactions">
          <div class="messenger__message-reaction" @click="reactMessage({message: message, emoji_id: reaction.type})">
            <div class="messenger__message-reaction-icon">
              <span v-if="reaction.type === 1">&#128077;</span>
              <span v-else-if="reaction.type === 2">&#128078;</span>
              <span v-else-if="reaction.type === 3">&#10004;</span>
              <span v-else-if="reaction.type === 4">&#10006;</span>
              <span v-else-if="reaction.type === 5">&#10067;</span>
            </div>
            <div class="messenger__message-reaction-count">
              <span>{{ reaction.count }}</span>
            </div>
          </div>
        </template>
      </div>


      <div class="messenger__text-timestamp">
        <span>{{ message.created_at | moment }}</span>
      </div>

    </div>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import moment from "moment";
import MessageReaders from "./MessageReaders/MessageReaders.vue";
import AlternativeAvatar from "../../../ChatsList/ContactItem/AlternativeAvatar/AlternativeAvatar.vue";
import VoiceMessage from "./VoiceMessage/VoiceMessage.vue";

export default {
  name: "ConversationMessage",
  components: {
    MessageReaders, AlternativeAvatar, VoiceMessage
  },
  props: {
    message: {
      type: Object,
      required: true
    },
    last: {
      type: Boolean,
      default: false
    },
    active: {
      type: Boolean,
      default: false
    },
  },
  computed: {
    ...mapGetters(['user', 'chat']),
    messageCardClass() {
      return {
        'messenger__message-card': true,
        'messenger__message__failed': this.message.failed,
      }
    },
    reactions() {
      // go through each reader and if include reaction type
      // add to reactions array
      let reactions = [];
      this.message.readers.forEach(reader => {
        if (reader.pivot && reader.pivot.reaction) {
          // increment reaction count if already in array
          let reaction = reactions.find(reaction => reaction.type === reader.pivot.reaction);
          if (reaction) {
            reaction.count++;
          } else {
            reactions.push({
              type: reader.pivot.reaction,
              count: 1,
            });
          }
        }
      });
      return reactions;
    },
  },
  methods: {
    ...mapActions(['showGallery', 'reactMessage', 'loadMessages', 'loadMoreNewMessages', 'requestScroll',
      'setLoading']),
    isImage(file) {
      const ext = file.name.split('.').pop();
      return ['jpg', 'jpeg', 'png', 'gif'].includes(ext);//todo: другой способ определения. Хранить тип файла в БД.
    },
    isAudio(file) {
      const ext = file.name.split('.').pop();
      return ['mp3', 'wav', 'ogg', 'webm'].includes(ext);
    },
    isGallery() {
      return this.message.files && this.message.files.length > 1 && this.message.files.every(file => this.isImage(file));
    },
    getImages() {
      return this.message.files.filter(file => this.isImage(file)).map(file => file.file_path);
    },
    openImage(image) {
      this.showGallery({
        images: this.getImages(),
        index: this.message.files.findIndex(f => f.id === image.id),
      });
    },
    goto(message, event) {
      event.stopPropagation();
      this.setLoading(true);
      this.loadMessages({
        reset: false, goto: message.id, callback: () => {
          // after a second
          setTimeout(() => {
            this.setLoading(false);
          }, 1000);
        }
      });
    }
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
    },
  }
}
</script>

<style>
/*noinspection CssUnusedSymbol*/
.messenger__message-box-right, .messenger__message-box-left {
  display: flex;
  flex: 0 0 50%;
  line-height: 1.4;
  margin-left: 20px;
}

/*noinspection CssUnusedSymbol*/
.messenger__message-box-left {
  justify-content: flex-start;
}

/*noinspection CssUnusedSymbol*/
.messenger__message-box-right {
  justify-content: flex-end;
}

.messenger__message-container {
  position: relative;
  padding: 2px 10px;
  min-width: 75px;
  box-sizing: content-box;
  display: flex;
  flex-wrap: wrap;
  flex-direction: column;
  align-content: stretch;
  align-items: stretch;
  margin-top: 4px;
}

/*noinspection CssUnusedSymbol*/
.messenger__message-card {
  font-size: 12px;
  padding: 10px;
  white-space: pre-line;
  max-width: 360px;
  -webkit-transition-property: box-shadow, opacity;
  transition-property: box-shadow, opacity;
  transition: box-shadow .28s cubic-bezier(.4, 0, .2, 1);
  will-change: box-shadow;
  box-shadow: 0 1px 1px -1px #0000001a, 0 1px 1px -1px #0000001c, 0 1px 2px -1px #0000001c;
  background: #f4f6fa;
  color: #5f5d5d;
}

/*noinspection CssUnusedSymbol*/
.messenger__message__failed {
  background-color: #ffcdd2 !important;
}

/*noinspection CssUnusedSymbol*/
.messenger__message-box-right .messenger__message-card {
  float: right;
}

/*noinspection CssUnusedSymbol*/
.messenger__message-box-left .messenger__message-card {
  float: left;
}

.messenger__text-timestamp {
  font-size: 10px;
  line-height: 10px;
  margin-top: 10px;
  color: #828c94;
  text-align: right;
}

.messenger__message-box-right .messenger__text-timestamp {
  text-align: left;
}

.messenger__message-files {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.messenger__message-file {
  display: flex;
  align-items: center;
  margin: 5px;
  max-width: 100%;
}

.messenger__message-file-icon {
  font-size: 20px;
  margin-right: 5px;
}

.messenger__message-file-name {
  font-size: 14px;
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
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

.messenger__message-file-image {
  cursor: pointer;
}

.messenger__message-file-image img {
  max-width: 100%;
}

audio {
  max-width: 230px;
}

.messenger__message-files_group {
  flex-wrap: wrap;
  width: 210px;
  height: 160px;
  align-content: space-around;
}

.messenger__message-files_group-count {
  position: relative;
  top: -75px;
  height: 100%;
  width: 100%;
  color: white;
  background: rgba(0, 0, 0, 0.5);
}

.messenger__message-files_group-count span {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  -ms-transform: translate(-50%, -50%);
}

.messenger__message-file_group {
  width: 100px;
  height: 155px;
  margin-bottom: 5px;
}

.messenger__message-file_group .messenger__message-file-image {
  height: 100%;
}

.messenger__message-file_group .messenger__message-file-image img {
  height: 100%;
}

.messenger__last-row {
  width: 100px;
  height: 75px;
}

.messenger__last-column {
  width: 100px;
  height: 75px;
}

.messenger__message-reactions {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: flex-end;
  margin-top: 5px;
}

.messenger__message-reactions .messenger__message-reaction {
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 40%;
  margin-left: 5px;
  font-size: 12px;
  cursor: pointer;
  padding: 5px 10px;
  background-color: #f5f5f5;
}

.messenger__message-reactions .messenger__message-reaction:hover {
  background-color: #e0e0e0;
}

.messenger__message-reactions .messenger__message-reaction .messenger__message-reaction-count {
  margin-left: 5px;
}

.messenger__message-reactions .messenger__message-reaction .messenger__message-reaction-count:hover {
  background-color: transparent;
}

.messenger__format-container_parent {
  border-left: 2px solid #5ebee9;
  cursor: pointer;
  padding: 2px 10px;
}

.messenger__format-container_parent-author {
  font-weight: bold;
}

.messenger__format-container_parent-message {
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}

</style>
