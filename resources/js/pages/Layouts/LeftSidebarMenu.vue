<template>
  <div class="header__menu">
    <template v-for="item in items">
      <a
        :key="item.name"
        v-if="!item.hide"
        :href="item.href"
        class="menu__item"
      >
        <span v-if="item.icon" :class="item.icon" class="menu__item-icon"/>
        <img
          v-if="item.img"
          :src="item.img.src"
          :style="'max-width: 1em;' + item.img.style"
          :alt="item.alt"
        >
        <span class="menu__item-title">{{item.name}}</span>
      </a>
    </template>
  </div>
</template>

<script>
export default {
  name: 'LeftSidebarMenu',
  props: ['items']
} 
</script>

<style lang="scss">
</style>