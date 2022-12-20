<template>
  <section id="jSec2">
    <div class="section-content">
      <h2 class="jSec2-header jHeader">{{ $lang(lang, 's2-header') }}</h2>
      <ul class="jSec2-items" ref="items">
        <li class="jSec2-item jSec2-item-1" :class="{'jSec2-highlight': isBlock1Highlight}">
          <span class="jSec2-item-value">{{ $lang(lang, 's2-b1-value') }}</span>
          <span class="jSec2-item-text">{{ $lang(lang, 's2-b1-text') }}</span>
        </li>
        <li class="jSec2-item jSec2-item-2" :class="{'jSec2-highlight': isBlock2Highlight}">
          <span class="jSec2-item-value">{{ $lang(lang, 's2-b2-value') }}</span>
          <span class="jSec2-item-text">{{ $lang(lang, 's2-b2-text') }}</span>
        </li>
        <li class="jSec2-item jSec2-item-3" :class="{'jSec2-highlight': isBlock3Highlight}">
          <span class="jSec2-item-value">{{ $lang(lang, 's2-b3-value') }}</span>
          <span class="jSec2-item-text">{{ $lang(lang, 's2-b3-text') }}</span>
        </li>
      </ul>
    </div>
  </section>
</template>

<script>
export default {
  data(){
    return {
      isBlock1Highlight: false,
      isBlock2Highlight: false,
      isBlock3Highlight: false,
      observer: null,
    }
  },
  computed: {
    lang() {
      return this.$root.$data.lang
    }
  },
  mounted(){
    this.observer = new IntersectionObserver(this.animate, {
      rootMargin: '30px',
      threshold: 1
    })
    this.observer.observe(this.$refs.items)
  },
  methods: {
    wait(ms){
      return new Promise(resolve => {
        setTimeout(resolve, ms)
      })
    },
    async animate(entries, observer){
      this.isBlock1Highlight = true
      await this.wait(350)
      this.isBlock1Highlight = false
      this.isBlock2Highlight = true
      await this.wait(350)
      this.isBlock2Highlight = false
      this.isBlock3Highlight = true
      await this.wait(350)
      this.isBlock3Highlight = false
    }
  }
}
</script>

<style lang="scss">
@import '../../assets/scss/app.variables.scss';

#jSec2 {
  width: 100%;
}

.jSec2-header {
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
    left: -1rem;
    background-image: url("../../assets/img/s2-bg.svg");
  }
}

.jSec2-items {
  display: flex;
  flex-flow: row wrap;
  justify-content: stretch;
  padding: 0;
  margin: 0 0 3rem;
  list-style: none;
}

.jSec2-item {
  display: flex;
  flex-flow: column nowrap;
  align-items: center;
  flex: 0 0 100%;
  padding: 2rem;
  margin-bottom: 1.25rem;
  border-radius: 0.9375rem;

  position: relative;
  overflow: hidden;

  &:before{
    content: '';
    width: 0;
    height: 0;
    border-radius: 100vw;
    position: absolute;
    z-index: 1;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: rgba(#000, 0.25);
    transition: all 0.35s;
  }
}

.jSec2-item-value {
  margin-bottom: 0.5rem;
  font-weight: 700;
  font-size: 3.125rem;
  line-height: 2;

  position: relative;
  z-index: 5;

  transition: text-shadow 0.35s;
}

.jSec2-item-text {
  font-size: 1.125rem;
  line-height: 1.39;
  text-align: center;
  width: 16.5rem;

  position: relative;
  z-index: 5;
}

.jSec2-item-1 {
  background: #edf8ff;

  .jSec2-item-value {
    color: #72c6f9;
  }
}

.jSec2-item-2 {
  background: #fff9ea;

  .jSec2-item-value {
    color: #ffd86b;
  }
}

.jSec2-item-3 {
  background: #f3f0fc;

  .jSec2-item-value {
    color: #9082bb;
  }
}



.jSec2-highlight{
  &:before{
    width: 120%;
    height: 120%;
  }
  .jSec2-item-value{
    text-shadow:
      0 0 2px #aaa,
      0 0 1rem #fff,
      0 0 1rem #fff,
      0 0 1rem #fff,
      0 0 1rem #fff;
  }
}

@media screen and (min-width: $medium) {
  .jSec2-items {
    flex-flow: row nowrap;
    justify-content: stretch;
    align-items: stretch;
    gap: 1.25rem;
  }
  .jSec2-item {
    flex: 0 1 33%;
    padding: 2rem 3rem;
  }
}
</style>
