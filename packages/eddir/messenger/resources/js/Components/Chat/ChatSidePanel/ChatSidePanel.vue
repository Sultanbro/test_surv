<template>
  <div class="header__right-messages">
    <ChatsList></ChatsList>
  </div>
</template>

<script>
import ChatsList from "../ChatsList/ChatsList";

// noinspection JSUnusedGlobalSymbols
export default {
  components: {
    ChatsList,
  }
}
</script>

<style>

.header__right-messages {
  overflow-y: auto;
}

</style>
