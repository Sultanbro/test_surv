<template>
  <div @keydown.esc="escapeChat" v-click-outside="toggle" v-show="isOpen"
       class="messenger__card-window" id="messengerWindow">
    <SearchBox></SearchBox>
    <div class="messenger__chat-container">
      <ChatsList :fullscreen="true"></ChatsList>
      <MessengerConversation></MessengerConversation>
      <InfoPanel></InfoPanel>
    </div>
    <ChinBox></ChinBox>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import SearchBox from "./SearchBox/SearchBox.vue";
import ChatsList from "./ChatsList/ChatsList.vue";
import MessengerConversation from "./MessengerConversation/MessengerConversation.vue";
import ChinBox from "./ChinBox/ChinBox.vue";
import InfoPanel from "./InfoPanel/InfoPanel";
import clickOutside from "./directives/clickOutside.ts";

// noinspection JSUnusedGlobalSymbols
export default {
  name: "ChatApp",
  components: {
    SearchBox,
    ChatsList,
    MessengerConversation,
    ChinBox,
    InfoPanel
  },
  directives: {
    clickOutside
  },
  watch: {
    isOpen: function (val) {
      if (val) {
        // set div modal-open class
        document.body.classList.add('modal-open');
      } else {
        // remove div modal-open class
        document.body.classList.remove('modal-open');
      }
    }
  },
  computed: {
    ...mapGetters(['isInitialized', 'user', 'isOpen'])
  },
  created() {
    this.boot();
  },
  methods: {
    ...mapActions(['boot', 'escapeChat', 'toggleMessenger']),
    toggle() {
      if (this.isOpen) {
        this.toggleMessenger();
      }
    }
  }
}
</script>

<style>

body.modal-open {
  overflow: hidden;
  position: fixed;
}

.messenger__card-window {
  position: fixed;
  margin-right: 6rem;
  right: 0;
  bottom: 0;
  width: 90vw;
  height: 100vh;
  display: flex;
  flex-direction: column;
  background: #ffffff;
  color: #0a0a0a;
  font-size: 14px;
  overflow-wrap: break-word;
  white-space: normal;
  -webkit-tap-highlight-color: transparent;
  z-index: 1000;
}

.messenger__chat-container {
  display: flex;
  flex: 1;
  overflow-y: hidden;
}

</style>
