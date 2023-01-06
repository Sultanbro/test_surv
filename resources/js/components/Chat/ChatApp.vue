<template>
  <div class="messenger__wrapper" v-click-outside="toggle" v-show="isOpen">
    <div @keydown.esc="escapeChat" class="messenger__card-window" id="messengerWindow">
      <SearchBox></SearchBox>
      <div class="messenger__chat-container">
        <ChatsList :fullscreen="true"></ChatsList>
        <MessengerConversation></MessengerConversation>
        <InfoPanel></InfoPanel>
      </div>
      <ChinBox></ChinBox>
    </div>
    <ImageGallery
      :images="galleryImages"
      id="messenger_gallery"
      :index="galleryIndex"
      @onopen="openGallery"
      @close="hideGallery"
    />
    <ConfirmDialog></ConfirmDialog>
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
import ImageGallery from "./ImageGallery/ImageGallery.vue";
import ConfirmDialog from "./ConfirmDialog/ConfirmDialog.vue";
// noinspection JSUnusedGlobalSymbols
export default {
  name: "ChatApp",
  components: {
    SearchBox,
    ChatsList,
    MessengerConversation,
    ChinBox,
    InfoPanel,
    ImageGallery,
    ConfirmDialog
  },
  directives: {
    clickOutside
  },
  watch: {
    isOpen: function (val) {
      if (val) {
        // set div messenger__open class
        document.body.classList.add('messenger__open');
      } else {
        // remove div messenger__open class
        document.body.classList.remove('messenger__open');
      }
    }
  },
  data() {
    return {
      galleryOpened: false,
    };
  },
  computed: {
    ...mapGetters(['isInitialized', 'user', 'isOpen', 'galleryImages', 'galleryIndex']),
  },
  created() {
    this.boot();
  },
  methods: {
    ...mapActions(['boot', 'escapeChat', 'toggleMessenger', 'hideGallery']),
    toggle() {

      if (this.galleryOpened) {
        this.galleryOpened = false;
        return;
      }

      if (this.isOpen) {
        this.toggleMessenger();
      }

    },
    openGallery() {
      this.galleryOpened = true;
    },
  }
}
</script>

<style>

/*noinspection CssUnusedSymbol*/
body.messenger__open {
  overflow: hidden;
  position: fixed;
}

.messenger__wrapper {
  /* width: 90vw;
  height: 100vh; */
  position: fixed;
  right: 0;
  bottom: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    top: 0;
    left: 0;
    z-index: 10000;
    overflow-x: hidden;
    overflow-y: auto;
    display: flex;
    opacity: 1;
    visibility: visible;
    transition: 0.2s;
}

.messenger__card-window {
  position: fixed;
  padding-right: 6rem;
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

@media only screen and (max-width: 670px) {
  .messenger__card-window {
    width: 100vw;
  }
}

.messenger__chat-container {
  display: flex;
  flex: 1;
  overflow-y: hidden;
}

</style>
