<template>
  <a href="#" class="header__right-icon loop" @click="toggleMessenger">
    <img src="/images/dist/header-right-3.svg" alt="nav icon" class="header__icon-img">
  </a>
</template>

<script>
import {mapActions} from "vuex";

export default {
  methods: {
    ...mapActions([
      "toggleMessenger"
    ])
  }
}
</script>

<style scoped>

</style>
