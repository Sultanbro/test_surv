<template>
  <div class="jSec3-block" :class="{'jSec3-block-right': right}">
    <div class="jSec3-block-image">
      <img :src="img" :alt="alt" class="jSec3-block-img">
    </div>
    <div class="jSec3-block-content">
      <slot/>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    img: String,
    alt: {
      type: String,
      default(){ return '' }
    },
    right: Boolean
  },
  computed: {
    lang () {
      return this.$root.$data.lang
    }
  },
}
</script>

<style lang="stylus">
@import '../../themes/app.variables.styl'
.jSec3-block
  padding .5rem 0 1.5rem
.jSec3-block-img
  width 100%
  box-shadow 0 0 1rem rgba(#000, 0.1)

@media screen and (min-width: $medium)
  .jSec3-block
    display flex
    flex-flow row nowrap
    align-items center
    padding 5rem 0
  .jSec3-block-right
    .jSec3-block-image
      order 2
    .jSec3-block-content
      order 1  
  .jSec3-block-image
  .jSec3-block-content
    flex 0 0 50%
  .jSec3-block-image
    padding 0 1.125rem
  .jSec3-block-content
    padding 1rem 2rem
</style>