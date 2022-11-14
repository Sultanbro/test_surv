<template>
    <div>
        <div class="messenger__side-panel" v-show="!isOpen">
            <SearchBox></SearchBox>
            <div class="messenger__chat-container">
                <ChatsList></ChatsList>
            </div>
            <ChinBox></ChinBox>
        </div>
        <div class="messenger__card-window" v-show="isOpen" @keydown.esc="escapeChat" id="messengerWindow">
            <SearchBox></SearchBox>
            <div class="messenger__chat-container">
                <ChatsList></ChatsList>
                <MessengerConversation></MessengerConversation>
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

export default {
    name: "ChatApp",
    components: {
        SearchBox,
        ChatsList,
        MessengerConversation,
        ChinBox
    },
    computed: {
        ...mapGetters(['isInitialized', 'user', 'isOpen'])
    },
    created() {
        this.boot();
    },
    methods: {
        ...mapActions(['boot', 'escapeChat'])
    }
}
</script>

<style>

.messenger__chat-container {
    height: calc(100% - 50px);
    display: flex;
}

.messenger__side-panel {
    max-height: 1000px;
    display: none;
    max-width: 300px;
    position: fixed;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    height: calc(100vh - 100px);
    background: #ffffff;
    color: #0a0a0a;
    overflow-wrap: break-word;
    white-space: normal;
    border: none;
    border-radius: 4px;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
    -webkit-tap-highlight-color: transparent;
}

.messenger__card-window {
    /*resize: both;*/
    /*overflow: auto;*/
    max-height: 1000px;
    max-width: 1200px;
    
    /*custom */
    width: 1200px;
    /***/
    
    position: fixed;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    height: calc(100vh - 100px);
    background: #ffffff;
    color: #0a0a0a;
    overflow-wrap: break-word;
    white-space: normal;
    border: none;
    border-radius: 4px;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
    -webkit-tap-highlight-color: transparent;
}

</style>
