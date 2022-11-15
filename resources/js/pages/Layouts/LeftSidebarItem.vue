<template>
  <div class="header__nav-link">
    <a :href="href" class="header__nav-link-a">
      <span v-if="icon" :class="icon" class="header__nav-icon"></span>
      <img
        v-if="img"
        :style="img.style"
        :src="img.src"
        :class="img.className"
      >
      <span class="header__nav-name">{{ name }}</span>
    </a>
    <LeftSidebarMenu
      v-if="menu"
      :items="menu"
    />
  </div>
</template>

<script>
import LeftSidebarMenu from './LeftSidebarMenu'

export default {
  name: 'LeftSidebarItem',
  components: {
    LeftSidebarMenu
  },
  props: [
    'href',
    'name',
    'icon',
    'img',
    'menu',
  ],
  mounted(){
    this.$emit('calcsize', this.$el)
  }
}
</script>

<style lang="scss">
</style>