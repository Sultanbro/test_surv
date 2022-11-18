<template>
  <div class="messenger__card-window" v-show="isOpen" @keydown.esc="escapeChat" id="messengerWindow">
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

export default {
  name: "ChatApp",
  components: {
    SearchBox,
    ChatsList,
    MessengerConversation,
    ChinBox,
    InfoPanel
  },
  computed: {
    ...mapGetters(['isInitialized', 'user', 'isOpen'])
  },
  created() {
    this.boot();
  },
  methods: {
    ...mapActions(['boot', 'escapeChat', 'toggleMessenger'])
  }
}
</script>

<style>

.messenger__chat-container {
  display: flex;
  flex: 1;
  overflow-y: hidden;
}

.messenger__card-window {
  margin-right: 6rem;
  position: fixed;
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
}

</style>
