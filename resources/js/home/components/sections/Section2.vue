<template>
  <section id="jSec2">
    <div class="section-content">
      <h2 class="jSec2-header jHeader">{{ $lang(lang, 's2-header') }}</h2>
      <ul class="jSec2-items">
        <li class="jSec2-item jSec2-item-1">
          <span class="jSec2-item-value">{{ $lang(lang, 's2-b1-value') }}</span>
          <span class="jSec2-item-text">{{ $lang(lang, 's2-b1-text') }}</span>
        </li>
        <li class="jSec2-item jSec2-item-2">
          <span class="jSec2-item-value">{{ $lang(lang, 's2-b2-value') }}</span>
          <span class="jSec2-item-text">{{ $lang(lang, 's2-b2-text') }}</span>
        </li>
        <li class="jSec2-item jSec2-item-3">
          <span class="jSec2-item-value">{{ $lang(lang, 's2-b3-value') }}</span>
          <span class="jSec2-item-text">{{ $lang(lang, 's2-b3-text') }}</span>
        </li>
      </ul>
    </div>
  </section>
</template>

<script>
export default {
  computed: {
    lang() {
      return this.$root.$data.lang
    }
  },
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
}

.jSec2-item-value {
  margin-bottom: 0.5rem;
  font-weight: 700;
  font-size: 3.125rem;
  line-height: 2;
}

.jSec2-item-text {
  font-size: 1.125rem;
  line-height: 1.39;
  text-align: center;
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
