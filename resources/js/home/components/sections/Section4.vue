<template>
  <section id="jSec4">
    <div class="section-content">
      <h2 class="jSec4-header jHeader">{{ $lang(lang, 's4-header') }}</h2>
      <p class="jSec4-subheader">{{ $lang(lang, 's4-subheader') }}</p>
      <ul
        v-show="isMedium"
        ref="items"
        class="jSec4-items"
      >
        <li
          class="jSec4-item jSec4-item-1"
          :class="{'jSec4-highlight': isBlock1Highlight}"
        >
          <span class="jSec4-item-title">{{ $lang(lang, 's4-b1-title') }}</span>
          <span class="jSec4-item-text">{{ $lang(lang, 's4-b1-text') }}</span>
        </li>
        <li
          class="jSec4-item jSec4-item-2"
          :class="{'jSec4-highlight': isBlock2Highlight}"
        >
          <span class="jSec4-item-title">{{ $lang(lang, 's4-b2-title') }}</span>
          <span class="jSec4-item-text">{{ $lang(lang, 's4-b2-text') }}</span>
        </li>
        <li
          class="jSec4-item jSec4-item-3"
          :class="{'jSec4-highlight': isBlock3Highlight}"
        >
          <span class="jSec4-item-title">{{ $lang(lang, 's4-b3-title') }}</span>
          <span class="jSec4-item-text">{{ $lang(lang, 's4-b3-text') }}</span>
        </li>
      </ul>
      <Hooper
        v-if="!isMedium"
        :infiniteScroll="true"
        :autoPlay="true"
        :playSpeed="3000"
      >
        <Slide>
          <div class="jSec4-item jSec4-item-1">
            <span class="jSec4-item-title">{{ $lang(lang, 's4-b1-title') }}</span>
            <span class="jSec4-item-text">{{ $lang(lang, 's4-b1-text') }}</span>
          </div>
        </Slide>
        <Slide>
          <div class="jSec4-item jSec4-item-2">
            <span class="jSec4-item-title">{{ $lang(lang, 's4-b2-title') }}</span>
            <span class="jSec4-item-text">{{ $lang(lang, 's4-b2-text') }}</span>
          </div>
        </Slide>
        <Slide>
          <div class="jSec4-item jSec4-item-3">
            <span class="jSec4-item-title">{{ $lang(lang, 's4-b3-title') }}</span>
            <span class="jSec4-item-text">{{ $lang(lang, 's4-b3-text') }}</span>
          </div>
        </Slide>
      </Hooper>
      <form
        action=""
        class="jSec4-form"
        @submit="onSubmit"
      >
        <p class="jSec4-footer">{{ $lang(lang, 's4-footer') }}</p>
        <div class="jSec4-form-inputs">
          <InputText
            v-model="name"
            :label="$lang(lang, 's4-name')"
          />
          <InputText
            v-model="phone"
            :label="$lang(lang, 's4-phone')"
          />
        </div>
        <button
          class="jButton"
          type="submit"
        >{{ $lang(lang, 's4-free') }}</button>
      </form>
    </div>
  </section>
</template>

<script>
import { Hooper, Slide } from 'hooper'
import 'hooper/dist/hooper.css'
import InputText from '../../components/InputText'

export default {
  components: {
    InputText,
    Hooper,
    Slide,
  },
  data() {
    return {
      name: '',
      phone: '',
      isBlock1Highlight: false,
      isBlock2Highlight: false,
      isBlock3Highlight: false,
      observer: null,
    }
  },
  computed: {
    lang() {
      return this.$root.$data.lang
    },
    isMedium(){
      return this.$viewportSize.width >= 1260
    },
  },
  mounted(){
    this.observer = new IntersectionObserver(this.animate, {
      rootMargin: '30px',
      threshold: 1
    })
    this.observer.observe(this.$refs.items)
  },
  methods: {
    onSubmit(e) {
      e.preventDefault()
      alert(`name: ${this.name}, phone: ${this.phone}`)
    },
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

#jSec4 {
  width: 100%;
  padding-bottom: 2rem;
  .hooper{
    height: auto;
  }
}

.jSec4-header {
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

.jSec4-subheader {
  text-align: center;
  font-size: 1.125rem;
}

.jSec4-items {
  display: flex;
  flex-flow: row wrap;
  justify-content: stretch;
  padding: 0;
  margin: 0;
  list-style: none;
}

.jSec4-item {
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

.jSec4-item-title {
  margin-bottom: 0.5rem;
  font-weight: 700;
  font-size: 3.125rem;
  line-height: 2;

  position: relative;
  z-index: 5;

  transition: text-shadow 0.35s;
}

.jSec4-item-text {
  font-size: 1.125rem;
  line-height: 1.39;
  text-align: center;
  width: 16rem;
}

.jSec4-item-1 {
  background: #edf8ff;

  .jSec4-item-title {
    color: #72c6f9;
  }
}

.jSec4-item-2 {
  background: #fff9ea;

  .jSec4-item-title {
    color: #ffd86b;
  }
}

.jSec4-item-3 {
  background: #f3f0fc;

  .jSec4-item-title {
    color: #9082bb;
  }
}

.jSec4-highlight{
  &:before{
    width: 120%;
    height: 120%;
  }
  .jSec4-item-title{
    text-shadow:
      0 0 2px #aaa,
      0 0 1rem #fff,
      0 0 1rem #fff,
      0 0 1rem #fff,
      0 0 1rem #fff;
  }
}

.jSec4-form,
.jSec4-form-inputs{
  display: flex;
  flex-flow: column;
  align-items: center;
  gap: 1rem;
}

@media screen and (min-width: $medium) {
  #jSec4 {
    padding-bottom: 5rem;
  }
  .jSec4-items {
    flex-flow: row nowrap;
    justify-content: stretch;
    align-items: stretch;
    gap: 1.25rem;
  }
  .jSec4-item {
    flex: 0 1 33%;
    padding: 2rem 3rem;
  }
  .jSec4-form {
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    gap: 1.25rem;

    .jButton {
      white-space: nowrap;
    }
  }
  .jSec4-form-inputs {
    display: flex;
    flex-flow: row nowrap;
    gap: 1.25rem;
  }
  .jSec4-footer {
    flex: 1 1 30%;
    font-size: 1.125rem;
    line-height: 1.39;
  }
}
</style>
