<template>
  <div class="messenger__wrapper">
    <div class="messenger__side-panel" v-show="!isOpen">
      <div class="header__right-nav" @click="toggleMessenger()">
        <a href="#" class="header__right-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" fill="none">
            <rect x=".5" y=".5" width="37" height="37" rx="9.5" stroke="#E6EDFF"/>
            <path
              d="M17.007 22.87v-.222c.005-1.029.102-1.847.29-2.455.193-.608.465-1.096.818-1.466a6.285 6.285 0 0 1 1.287-1.031c.346-.216.656-.452.928-.707.279-.262.498-.552.657-.87.159-.323.238-.684.238-1.082 0-.449-.105-.838-.315-1.168a2.14 2.14 0 0 0-.852-.767 2.545 2.545 0 0 0-1.185-.273c-.403 0-.787.089-1.15.265a2.23 2.23 0 0 0-.895.784c-.233.346-.364.787-.392 1.32h-3.034c.028-1.079.29-1.982.784-2.71a4.637 4.637 0 0 1 1.977-1.636c.824-.363 1.733-.545 2.727-.545 1.085 0 2.04.19 2.864.57.83.381 1.474.924 1.934 1.629.466.698.7 1.528.7 2.488 0 .648-.106 1.228-.316 1.739a4.223 4.223 0 0 1-.878 1.364c-.38.397-.832.752-1.355 1.065-.46.284-.838.58-1.133.886-.29.307-.506.668-.648 1.083-.137.409-.208.915-.213 1.517v.221h-2.838Zm1.483 5.317c-.512 0-.952-.181-1.321-.545a1.795 1.795 0 0 1-.554-1.33c0-.51.184-.948.554-1.312.369-.364.81-.546 1.32-.546.506 0 .944.182 1.313.546.375.364.563.801.563 1.313 0 .346-.088.661-.265.946-.17.284-.397.51-.681.681a1.79 1.79 0 0 1-.93.247Z"
              fill="#8D9CA9"/>
          </svg>
        </a>
        <a href="#" class="header__right-icon bell red">
          <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" fill="#ED2353">
            <rect id="" width="38" height="38" rx="10" fill="#ED2353"/>
            <g id="bell">
              <path clip-rule="evenodd"
                    d="M19 11.875c-2.815 0-4.222 1.985-4.222 4.433v1.373c0 .874-.787 1.596-1.418 2.202-.406.39-.693.943-.693 1.598 0 .816.63 1.477 1.407 1.477h9.852c.777 0 1.407-.661 1.407-1.477a2.2 2.2 0 0 0-.693-1.598c-.631-.606-1.418-1.328-1.418-2.202v-1.373c0-2.448-1.407-4.433-4.222-4.433Z"
                    stroke="#fff" stroke-width="1.5"/>
              <path
                d="M20.583 25.333c-.16.24-.391.44-.67.578a2.056 2.056 0 0 1-.913.211c-.32 0-.636-.073-.913-.211a1.73 1.73 0 0 1-.67-.578"
                stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </g>
            <circle cx="28.104" cy="9.104" r="2.771" fill="#fff"/>
          </svg>
        </a>
        <a href="#" class="header__right-icon loop">
          <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" fill="none">
            <rect x=".5" y=".5" width="37" height="37" rx="9.5" stroke="#E6EDFF"/>
            <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M17.417 13.458a3.959 3.959 0 1 0 0 7.917 3.959 3.959 0 0 0 0-7.917Zm-5.542 3.959a5.542 5.542 0 1 1 9.98 3.318l4.038 4.038a.792.792 0 0 1-1.12 1.12l-4.037-4.038a5.541 5.541 0 0 1-8.861-4.438Z"
                  fill="#8D9CA9"/>
          </svg>
        </a>
        <a href="#" class="header__right-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" fill="none">
            <rect x=".5" y=".5" width="37" height="37" rx="9.5" stroke="#E6EDFF"/>
            <path
              d="M25.125 17.417a1 1 0 1 0 2 0h-2ZM19 25.542a1 1 0 1 0 0-2v2Zm4.75-13.084h-9.5v2h9.5v-2Zm-12.875 3.375v6.334h2v-6.334h-2Zm3.375-3.375a3.375 3.375 0 0 0-3.375 3.375h2c0-.759.616-1.375 1.375-1.375v-2Zm12.875 3.375a3.375 3.375 0 0 0-3.375-3.375v2c.76 0 1.375.616 1.375 1.375h2ZM14.25 23.542c-.76 0-1.375-.616-1.375-1.375h-2a3.375 3.375 0 0 0 3.375 3.375v-2Zm12.875-6.125v-1.584h-2v1.584h2ZM14.25 25.542H19v-2h-4.75v2Z"
              fill="#8D9CA9"/>
            <path d="M15.042 16.625 19 19.792l3.958-3.167" stroke="#8D9CA9" stroke-width="2" stroke-linecap="round"/>
            <path d="m26.917 21.375-2.969 3.167-1.781-1.44" stroke="#8D9CA9" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round"/>
          </svg>
        </a>
        <a href="#" class="header__right-icon check">
          <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" fill="none">
            <rect x=".5" y=".5" width="37" height="37" rx="9.5" stroke="#E6EDFF"/>
            <path
              d="M13.458 14.25a2.375 2.375 0 0 1 2.375-2.375h6.334a2.375 2.375 0 0 1 2.375 2.375v9.5a2.375 2.375 0 0 1-2.375 2.375h-6.334a2.375 2.375 0 0 1-2.375-2.375v-9.5Z"
              stroke="#8D9CA9" stroke-width="2"/>
            <path
              d="M22.167 11.875h-.088c-.73 0-1.32.59-1.32 1.32a.264.264 0 0 1-.264.263h-2.99a.264.264 0 0 1-.264-.264 1.32 1.32 0 0 0-1.32-1.319h-.088"
              stroke="#8D9CA9" stroke-width="2"/>
            <path d="m21.375 18.208-2.969 3.167-1.781-1.44" stroke="#8D9CA9" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round"/>
          </svg>
        </a>
      </div>
      <div class="messenger__chat-container">
        <ChatsList></ChatsList>
      </div>
      <ChinBox></ChinBox>
    </div>
    <div class="messenger__card-window" v-show="isOpen" @keydown.esc="escapeChat" id="messengerWindow">
      <SearchBox></SearchBox>
      <div class="messenger__chat-container">
        <ChatsList :fullscreen="true"></ChatsList>
        <MessengerConversation></MessengerConversation>
        <InfoPanel></InfoPanel>
      </div>
      <ChinBox></ChinBox>
    </div>
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

.header__right-nav {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2rem;
  margin-bottom: 2.5rem;
}

.header__right-icon {
  width: 3.8rem;
  height: 3.8rem;
}

.messenger__chat-container {
  display: flex;
  flex: 1;
  overflow-y: hidden;
}

.messenger__side-panel {
  display: flex;
  flex-direction: column;
  max-width: 300px;
  color: #0a0a0a;
  overflow-wrap: break-word;
  white-space: normal;
  border: none;
  border-radius: 4px;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
  -webkit-tap-highlight-color: transparent;
  width: 6rem;
  max-height: inherit;
  padding-top: 2.5rem;
  align-items: center;
  transform: translateX(0);
  opacity: 1;
  visibility: visible;
  transition: all 0.5s;
  overflow-y: auto;
  overflow-x: hidden;
  height: 100%;
  position: fixed;
  z-index: 1001;
  top: 0;
  min-height: 100vh;
  background-color: #f6f7fc;
  right: 0;
}

.messenger__card-window {
  position: fixed;
  right: 0;
  bottom: 0;
  width: 90vw;
  height: 90vh;
  display: flex;
  flex-direction: column;
  background: #ffffff;
  color: #0a0a0a;
  font-size: 14px;
  overflow-wrap: break-word;
  white-space: normal;
  border: none;
  border-radius: 4px;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
  -webkit-tap-highlight-color: transparent;
}

</style>
