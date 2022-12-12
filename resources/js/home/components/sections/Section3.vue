<template>
  <section id="jSec3">
    <div class="section-content">
      <h2 class="jSec3-header jHeader">{{ $lang(lang, 's3-header') }}</h2>
      <p class="jSec3-subheader">{{ $lang(lang, 's3-subheader') }}</p>
      <div class="jSec3-blocks">
        <S3Block class="jSec3-block-1">
          <h3 class="jSec3-block-title">{{ $lang(lang, 's3-b1-title') }}</h3>
          <p class="jSec3-block-text">{{ $lang(lang, 's3-b1-text') }}</p>
        </S3Block>
        <S3Block :right="true" class="jSec3-block-2">
          <h3 class="jSec3-block-title">{{ $lang(lang, 's3-b2-title') }}</h3>
          <p class="jSec3-block-text">{{ $lang(lang, 's3-b2-text') }}</p>
        </S3Block>
        <S3Block class="jSec3-block-3">
          <h3 class="jSec3-block-title">{{ $lang(lang, 's3-b3-title') }}</h3>
          <p class="jSec3-block-text">{{ $lang(lang, 's3-b3-text') }}</p>
        </S3Block>
      </div>
    </div>
  </section>
</template>

<script>
import S3Block from '../section3/S3Block'

export default {
  components: {
    S3Block
  },
  computed: {
    lang() {
      return this.$root.$data.lang
    }
  },
}
</script>

<style lang="scss">
@import '../../assets/scss/app.variables.scss';

#jSec3 {
  width: 100%;
}

.jSec3-header {
  width: fit-content;
  margin-left: auto;
  margin-right: auto;
  margin-bottom: 5rem;
  position: relative;

  &:before {
    content: '';
    display: block;
    width: 10.625rem;
    height: 6.625rem;
    position: absolute;
    z-index: -1;
    top: -2.5rem;
    left: -3rem;
    background-image: url("../../assets/img/s2-bg.svg");
  }
}

.jSec3-subheader {
  text-align: center;
}

.jSec3-block-title {
  margin-bottom: 1rem;
  font-size: 1.25rem;
  font-weight: 500;
}

.jSec3-block-1 {
  background: url("../../assets/img/s3-bg1.svg") center no-repeat;
}

.jSec3-block-2 {
  background: url("../../assets/img/s3-bg2.svg") center no-repeat;
}

.jSec3-block-3 {
  background: url("../../assets/img/s3-bg3.svg") center no-repeat;
  background-size: contain;
}

@media screen and (min-width: $medium) {
  #jSec3 {
    padding-bottom: 5rem;
  }
  .jSec3-block-title {
    margin-bottom: 1rem;
    font-size: 1.5rem;
  }
  .jSec3-block-text {
    line-height: 1.875;
  }
}
</style>
