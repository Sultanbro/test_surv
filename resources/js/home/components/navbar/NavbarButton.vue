<template>
  <a :href="href" class="jNav-menu-button">
    {{ $lang(lang, text) }}
  </a>
</template>

<script>
export default {
  props: {
    href: String,
    lang: String,
    text: String
  }
}
</script>

<style lang="stylus">
@import '../../themes/app.variables.styl'

.jNav-menu-button
  display inline-block
  padding 0.875rem 1.1875rem
  margin 0.5rem 0
  vertical-align middle
  background: $button-color
  border-radius: 3.125rem
  font-size: 0.938rem
  line-height 0.875rem
  text-decoration none
  color #fff
  &:hover
    background $button-color-hover
</style>