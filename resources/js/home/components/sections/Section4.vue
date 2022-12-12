<template>
  <section id="jSec4">
    <div class="section-content">
      <h2 class="jSec4-header jHeader">{{ $lang(lang, 's4-header') }}</h2>
      <p class="jSec4-subheader">{{ $lang(lang, 's4-subheader') }}</p>
      <ul class="jSec4-items">
        <li class="jSec4-item jSec4-item-1">
          <span class="jSec4-item-title">{{ $lang(lang, 's4-b1-title') }}</span>
          <span class="jSec4-item-text">{{ $lang(lang, 's4-b1-text') }}</span>
        </li>
        <li class="jSec4-item jSec4-item-2">
          <span class="jSec4-item-title">{{ $lang(lang, 's4-b2-title') }}</span>
          <span class="jSec4-item-text">{{ $lang(lang, 's4-b2-text') }}</span>
        </li>
        <li class="jSec4-item jSec4-item-3">
          <span class="jSec4-item-title">{{ $lang(lang, 's4-b3-title') }}</span>
          <span class="jSec4-item-text">{{ $lang(lang, 's4-b3-text') }}</span>
        </li>
      </ul>
      <form action="" class="jSec4-form" @submit="onSubmit">
        <p class="jSec4-footer">{{ $lang(lang, 's4-footer') }}</p>
        <div class="jSec4-form-inputs">
          <InputText v-model="name" :label="$lang(lang, 's4-name')"/>
          <InputText v-model="phone" :label="$lang(lang, 's4-phone')"/>
        </div>
        <button class="jButton" type="submit">{{ $lang(lang, 's4-free') }}</button>
      </form>
    </div>
  </section>
</template>

<script>
import InputText from '../../components/InputText'

export default {
  components: {
    InputText
  },
  data() {
    return {
      name: '',
      phone: ''
    }
  },
  computed: {
    lang() {
      return this.$root.$data.lang
    }
  },
  methods: {
    onSubmit(e) {
      e.preventDefault()
      alert(`name: ${this.name}, phone: ${this.phone}`)
    }
  }
}
</script>

<style lang="scss">
@import '../../assets/scss/app.variables.scss';

#jSec4 {
  width: 100%;
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
}

.jSec4-item-title {
  margin-bottom: 0.5rem;
  font-weight: 700;
  font-size: 3.125rem;
  line-height: 2;
}

.jSec4-item-text {
  font-size: 1.125rem;
  line-height: 1.39;
  text-align: center;
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
