<template>
  <div class="header__nav-link">
    <router-link :to="href || ''" class="header__nav-link-a">
      <span v-if="icon" :class="icon" class="header__nav-icon"></span>
      <img
        v-if="img"
        :style="img.style"
        :src="img.src"
        :class="img.className"
      >
      <span class="header__nav-name">{{ name }}</span>
    </router-link>
    <div v-if="popover" class="header__nav-popover">{{ popover }}</div>
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
    'popover'
  ],
  mounted(){
    this.$emit('calcsize', this.$el)
  }
}
</script>

<style lang="scss">
.header__nav-link{
  &:hover{
    .header__nav-popover{
      opacity: 1;
      visibility: visible;
    }
  }
}

.header__nav-popover{
  display: block;
  width: 25rem;
  padding: 1rem;
  border-radius: 1rem 1rem;

  position: fixed;
  z-index: 1005;
  left: 8rem;

  background: #fff;
  color: #657A9F;
  font-size: 1.3rem;
  box-shadow: 1rem 0 2rem rgba(0, 0, 0, 0.15);
  opacity: 0;
  visibility: hidden;
}
</style>