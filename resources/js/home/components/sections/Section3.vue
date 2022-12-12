<template>
  <section id="jSec3">
    <div class="section-content">
      <h2 class="jSec3-header jHeader">{{ $lang(lang, 's3-header') }}</h2>
      <p class="jSec3-subheader">{{ $lang(lang, 's3-subheader') }}</p>
      <div class="jSec3-blocks">
        <S3Block class="jSec3-block-1" :img="require('@/assets/s3-b1.png')">
          <h3 class="jSec3-block-title">{{ $lang(lang, 's3-b1-title') }}</h3>
          <p class="jSec3-block-text">{{ $lang(lang, 's3-b1-text') }}</p>
        </S3Block>
        <S3Block class="jSec3-block-2" :img="require('@/assets/s3-b1.png')" :right="true">
          <h3 class="jSec3-block-title">{{ $lang(lang, 's3-b2-title') }}</h3>
          <p class="jSec3-block-text">{{ $lang(lang, 's3-b2-text') }}</p>
        </S3Block>
        <S3Block class="jSec3-block-3" :img="require('@/assets/s3-b1.png')">
          <h3 class="jSec3-block-title">{{ $lang(lang, 's3-b3-title') }}</h3>
          <p class="jSec3-block-text">{{ $lang(lang, 's3-b3-text') }}</p>
        </S3Block>
      </div>
    </div>
  </section>
</template>

<script>
import S3Block from '@/components/section3/S3Block'
export default {
  components: {
    S3Block
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
#jSec3
  width 100%
.jSec3-header
  width fit-content
  margin-left auto
  margin-right auto
  margin-bottom 5rem
  position relative
  &:before
    content ''
    display block
    width 10.625rem
    height 6.625rem
    position absolute
    z-index -1
    top -2.5rem
    left -3rem
    background-image url(../../assets/s2-bg.svg)
.jSec3-subheader
  text-align center
.jSec3-block-title
  margin-bottom 1rem
  font-size 1.25rem
  font-weight 500

.jSec3-block-1
  background url(../../assets/s3-bg1.svg) center no-repeat
.jSec3-block-2
  background url(../../assets/s3-bg2.svg) center no-repeat
.jSec3-block-3
  background url(../../assets/s3-bg3.svg) center no-repeat
  background-size contain

@media screen and (min-width: $medium)
  #jSec3
    padding-bottom 5rem
  .jSec3-block-title
    margin-bottom 1rem
    font-size 1.5rem
  .jSec3-block-text
    line-height 1.875
</style>