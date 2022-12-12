<template>
  <nav id="jNav">
    <div class="section-content jNav-content">
      <a href="" class="jNav-logo">
        <img class="jNav-logo-img" src="../../assets/img/logo.svg" alt="logo-img">
      </a>
      <div
        class="jNav-menu"
        :class="{'jNav-menu-active': menu}"
      >
        <button
          class="jNav-menu-hamburger jButton"
          @click="menu = !menu"
        />
        <ul class="jNav-menu-items">
          <li class="jNav-menu-item">
            <NavbarLink text="prices" href="#prices" :lang="lang"/>
          </li>
          <li class="jNav-menu-item">
            <NavbarLink text="reviews" href="#reviews" :lang="lang"/>
          </li>
          <li class="jNav-menu-item">
            <NavbarLink text="features" href="#features" :lang="lang"/>
          </li>
          <li class="jNav-menu-item">
            <span class="jNav-menu-auth">
              <NavbarButton text="auth" href="#auth" :lang="lang"/>
              <a href="#user" class="jNav-menu-user"/>
            </span>
          </li>
          <li class="jNav-menu-item">
            <NavbarLang :lang="lang" @change="$root.$data.setLang($event)"/>
          </li>
        </ul>
        <div class="jNav-menu-bg" @click="menu = false"></div>
      </div>
    </div>
  </nav>
</template>

<script>
import NavbarLink from '../navbar/NavbarLink.vue'
import NavbarButton from '../navbar/NavbarButton.vue'
import NavbarLang from '../navbar/NavbarLang.vue'

export default {
  name: 'Nav',
  components: {
    NavbarLink,
    NavbarButton,
    NavbarLang
  },
  computed: {
    lang () {
      return this.$root.$data.lang
    }
  },
  data(){
    return {
      menu: false
    }
  }
}
</script>

<style lang="scss">
@import '../../assets/scss/app.variables.scss';
#jNav {
  width: 100vw;
  position: sticky;
  z-index: 9000;
  top: 0;
  left: 0;
  right: 0;
  background: #fff;
  box-shadow: 0 0.25rem 1rem rgba(0,0,0,0.05);
}
.jNav-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.jNav-logo-img {
  width: 12.25rem;
}
.jNav-menu-active {
  .jNav-menu-items {
    display: flex;
  }
  .jNav-menu-bg {
    display: block;
  }
}
.jNav-menu-hamburger {
  &.jButton {
    display: block;
    width: 2rem;
    height: 2rem;
    padding: 1.25rem;
    position: relative;
    &:before {
      content: '';
      width: 50%;
      height: 0.75rem;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -45%);
      background: repeating-linear-gradient(#fff, #fff 0.125rem, transparent 0.125rem, transparent 0.25rem);
    }
  }
}
.jNav-menu-bg {
  display: none;
  position: fixed;
  z-index: 9004;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.25);
  backdrop-filter: blur(6px);
}
.jNav-menu-items {
  display: none;
  max-width: 85vw;
  padding: 0.625rem;
  margin: 0;
  flex-flow: column nowrap;
  position: fixed;
  z-index: 9005;
  top: 0;
  right: 0;
  bottom: 0;
  background: #fff;
  box-shadow: 0 0 0.125rem rgba(0,0,0,0.25);
}
.jNav-menu-item {
  display: block;
  list-style: none;
}
.jNav-menu-auth {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.jNav-menu-user {
  display: inline-block;
  width: 2.625rem;
  height: 2.625rem;
  border-radius: 2.625rem;
  vertical-align: middle;
  background: #6f4f28 url("../../assets/img/user.svg") center center no-repeat;
}

@media screen and (min-width: $small) {
  .jNav-logo-img {
    width: 15.25rem;
  }
}

@media screen and (min-width: $medium) {
  .jNav-menu-active,
  .jNav-menu-bg {
    display: none;
  }

  .jNav-menu-hamburger.jButton {
    display: none;
  }

  .jNav-menu-items {
    display: flex;
    align-items: center;
    max-width: none;
    padding: 0.625rem;
    margin: 0;
    flex-flow: row nowrap;
    position: static;
    background: none;
    box-shadow: none;
  }

  .jNav-menu-item {
    margin-left: 2.5rem;
  }

  .jNav-menu-auth {
    margin-left: 2.5rem;
  }

  .jNav-menu-user {
    margin-left: 2.5rem;
  }
}
</style>
