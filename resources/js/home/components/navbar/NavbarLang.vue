<template>
  <a
    href="javascript:void(0)"
    class="jNav-menu-lang"
    :class="{'jNav-menu-lang-active': active}"
    @click="active = !active"
  >
    {{ lang.toUpperCase() }}
    <div class="jNav-menu-lang-popup">
      <div class="jNav-menu-lang-button" @click="$emit('change', 'ru')">Russian</div>
      <div class="jNav-menu-lang-button" @click="$emit('change', 'en')">English</div>
      <div class="jNav-menu-lang-button" @click="$emit('change', 'kz')">Kazakh</div>
    </div>
  </a>
</template>

<script>
export default {
  props: {
    lang: String
  },
  data () {
    return {
      active: false
    }
  }
}
</script>

<style lang="scss">
.jNav-menu-lang {
  display: inline-block;
  padding: 0.25rem 0;
  margin: 0.5rem 0;
  position: relative;
  font-size: 0.9rem;
  color: #000;
  text-decoration: none;
  &:after {
    content: '';
    display: inline-block;
    width: 0.5rem;
    height: 0.3125rem;
    vertical-align: middle;
    background-image: url("../../assets/img/select-arrow.svg");
    background-repeat: no-repeat;
  }
}
.jNav-menu-lang-popup {
  display: none;
  width: auto;
  position: absolute;
  top: 100%;
  right: 0;
  background: #fff;
  box-shadow: 0 0.125rem 0.1875rem rgba(0,0,0,0.5);
  border-radius: 0.8rem;
}
.jNav-menu-lang-active {
  .jNav-menu-lang-popup {
    display: block;
  }
}
.jNav-menu-lang-button {
  padding: 1.25rem;
}
</style>
