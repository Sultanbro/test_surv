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

/*noinspection CssUnusedSymbol*/
.header__right-nav {
  margin-bottom: 2rem !important;
}

</style>
