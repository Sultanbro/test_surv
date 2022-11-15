<template>
  <div class="header__menu">
    <template v-for="item in items">
      <a
        :key="item.title"
        v-if="!item.hide"
        :href="item.href"
        class="menu__item"
      >
        <img :src="item.img" :alt="item.alt">
        <span class="menu__item-title">{{item.title}}</span>
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