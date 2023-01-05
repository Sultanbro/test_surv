<template>
  <nav id="jNav">
    <div
        :class="{'jNav-scroll': isScroll}"
        class="jNav-content"
    >
      <a
          class="jNav-logo"
          href=""
      >
        <img
            :src="require('../../assets/img/logo.svg').default"
            alt="logo-img"
            class="jNav-logo-img"
        >
      </a>
      <div
          :class="{'jNav-menu-active': menu}"
          class="jNav-menu"
      >
        <!-- <button
          class="jNav-menu-hamburger jButton"
          @click="menu = !menu"
        /> -->
        <ul class="jNav-menu-items">
          <a
              :class="{'jNav-menu-lang-active': active}"
              class="jNav-menu-hamburger jButton"
              href="javascript:void(0)"
              @click="active = !active"
          >
            <div class="jNav-menu-lang-popup">
              <li class="jNav-menu-item jNav-menu-item-md">
                <NavbarLink
                    :lang="lang"
                    href="#prices"
                    text="prices"
                />
              </li>
              <li class="jNav-menu-item jNav-menu-item-md">
                <NavbarLink
                    :lang="lang"
                    href="#reviews"
                    text="reviews"
                />
              </li>
              <li class="jNav-menu-item jNav-menu-item-md">
                <NavbarLink
                    :lang="lang"
                    href="#features"
                    text="features"
                />
              </li>
            </div>
          </a>
          <li class="jNav-menu-item">
            <span class="jNav-menu-auth">
              <form
                  v-if="csrf"
                  action="/logout"
                  method="POST"
              >
                <input
                    :value="csrf"
                    name="csrf"
                    type="hidden"
                >
              </form>
              <template
                  v-if="authorized"
              >
                <div class="jNav-menu-user-info">
                  <div class="jNav-menu-user-data">
                    <div
                        :title="laravel.fullname"
                        class="jNav-menu-user-name"
                    >{{ laravel.fullname }}</div>
                    <div
                        :title="laravel.email"
                        class="jNav-menu-user-email"
                    >{{ laravel.email }}</div>
                  </div>
                </div>
                <div
                    :class="{'jNav-menu-user-active': isUserMenu}"
                    class="jNav-menu-user"
                    @click="isUserMenu = !isUserMenu"
                >
                  <div
                      v-if="isUserMenu"
                      class="jNav-menu-user-menu"
                  >
                    <!--                    <div class="jNav-menu-user-menu-item" v-for="cabinet in laravel.cabinets">-->
                    <!--                      <a :href="'/login/' + cabinet.tenant_id">{{ cabinet.tenant_id }}.{{ hostname }}</a>-->
                    <!--                    </div>-->

                    <form
                        ref="formLogout"
                        action="/logout"
                        class="jNav-menu-user-menu-item"
                        method="POST"
                        style="display: flex; justify-content: center"
                    >
                      <input
                          :value="laravel.csrfToken"
                          name="_token"
                          type="hidden"
                      >
                      <button class="jNav-menu-user-menu-exit jNav-menu-link" @click="$refs.formLogout.submit()">
                        {{ $lang(lang, 'logout') }}
                      </button>
                    </form>
                  </div>
                </div>
              </template>
              <template v-else>
                <NavbarButton
                    :lang="lang"
                    href="/register"
                    text="register"
                />
                <a
                    :title="$lang(lang, 'auth')"
                    class="jNav-menu-user"
                    href="/login"
                />
              </template>
            </span>
          </li>
          <li class="jNav-menu-item jNav-menu-item-md">
            <NavbarLang
                :lang="lang"
                @change="$root.$data.setLang($event)"
            />
          </li>
        </ul>
        <div
            class="jNav-menu-bg"
            @click="menu = false"
        />
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
    lang() {
      return this.$root.$data.lang
    },
    laravel() {
      return window.Laravel
    },
    authorized() {
      return window.Laravel.email !== undefined
    },
    hostname() {
      return window.location.hostname
    }
  },

  data() {
    return {
      menu: false,
      csrf: '',
      isScroll: false,
      active: false,
      isUserMenu: false
    }
  },

  methods: {
    changeLogoSizeByScroll() {
      document.body.scrollTop > 20
      || document.documentElement.scrollTop > 20
          ? this.isScroll = true
          : this.isScroll = false
    },
  },

  mounted() {
    window.addEventListener('scroll', this.changeLogoSizeByScroll);
    this.csrf = document.getElementById('csrf')?.value
  },

  beforeDestroy() {
    window.removeEventListener('scroll', this.changeLogoSizeByScroll);
  }
}
</script>

<style lang="scss">
@import '../../assets/scss/app.variables.scss';

#jNav {
  display: flex;
  align-items: center;
  position: sticky;
  width: 100vw;
  height: 4.875rem;
  z-index: 9000;
  top: -1.125rem;
  background: #fff;
  box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.05);
}

.jNav-content {
  display: flex;
  position: sticky;
  top: 0;
  justify-content: space-between;
  align-items: center;
  margin: 0 auto;
  width: 78.125rem;
  height: 3.75rem;
  padding: 0 1rem;
}

@media (max-width: $medium) {
  .jNav-logo-img {
    width: 8rem;
  }

  .jNav-scroll .jNav-logo-img {
    width: 7rem;
    transition: 0.5s;
  }
}

@media screen and (min-width: $medium) {
  .jNav-logo-img {
    width: 15.25rem;
  }

  .jNav-scroll .jNav-logo-img {
    width: 11.25rem;
    transition: 0.5s;
  }
}

@media (min-width: $large) {
  .jNav-logo-img {
    width: 15.25rem;
  }
}

.jNav-menu-active {
  .jNav-menu-items {
    display: flex;
  }

  .jNav-menu-bg {
    display: block;
  }
}

.jNav-menu-user-menu {
  display: none;
  width: auto;
  position: absolute;
  top: 100%;
  right: 0;
  background: #fff;
  box-shadow: 0 0.125rem 0.1875rem rgba(0, 0, 0, 0.5);
  border-radius: 0.8rem;
}

.jNav-menu-user-active {
  .jNav-menu-user-menu {
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

@media (max-width: $medium) {
  .jNav-menu-hamburger.jButton {
    display: none;
  }
}

.jNav-menu-item .jNav-menu-link {
  font-size: 0.9rem;
  padding: 1.25rem;
}

@media (max-width: $small) {
  .jNav-menu-item .jNav-menu-link {
    font-size: 0.8rem;
    padding: 0.5rem;
  }
}

.jNav-menu-user-info {
  display: flex;
  flex-flow: row nowrap;
  flex: 1 1 auto;
  align-items: center;
}

.jNav-menu-user-data {
  display: flex;
  flex-flow: column;
  flex: 0 1 10em;
  overflow: hidden;
}

.jNav-menu-user-name,
.jNav-menu-user-email {
  max-width: 10em;
  overflow: hidden;
  text-overflow: ellipsis;
}

@media (max-width: $small) {
  .jNav-menu-user-name,
  .jNav-menu-user-email {
    font-size: 12px;
  }
}

// .jNav-menu-hamburger {
//   &.jButton {
//     display: block;
//     width: 2rem;
//     height: 2rem;
//     padding: 1.25rem;
//     position: relative;

//     &:before {
//       content: '';
//       width: 50%;
//       height: 0.75rem;
//       position: absolute;
//       top: 50%;
//       left: 50%;
//       transform: translate(-50%, -45%);
//       background: repeating-linear-gradient(#fff, #fff 0.125rem, transparent 0.125rem, transparent 0.25rem);
//     }
//   }
// }


.jNav-menu-bg {
  display: none;
  position: fixed;
  z-index: 9004;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.25);
  backdrop-filter: blur(6px);
}

.jNav-menu-items {
  display: flex;
  align-items: center;
  max-width: none;
  padding: 0.625rem;
  margin: 0;
  flex-flow: row nowrap;
}

.jNav-menu-item {
  display: block;
  list-style: none;
}

.jNav-menu-auth {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.jNav-menu-user {
  display: inline-block;
  width: 2rem;
  height: 2rem;
  border: none;
  border-radius: 2.625rem;
  position: relative;
  vertical-align: middle;
  background: #6f4f28 url("../../assets/img/user.svg") center center no-repeat;
}

.jNav-menu-item-md {
  display: none;
}

.jNav-menu-user-menu {
  padding: 0.5rem;
  position: absolute;
  z-index: 5;
  top: 100%;
  right: 0;
  background-color: #fff;
  box-shadow: 0 0.125rem 0.1875rem rgba(0, 0, 0, 0.5);
}

.jNav-menu-user-menu-item {
  white-space: nowrap;
  cursor: pointer;
}

.jNav-menu-user-menu-exit {
  padding: 0;
  border: none;
  background: none;
  cursor: pointer;
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

  .jNav-menu-lang-popup .jNav-menu-item {
    margin-left: 0;
    display: flex;
    justify-content: center;
  }

  .jNav-menu-lang-popup .jNav-menu-lang:after {
    display: none;
  }

  .jNav-menu-auth {
    //margin-left: 2.5rem;
    gap: 1rem;
  }

  .jNav-menu-user {
    width: 2.625rem;
    height: 2.625rem;
    //margin-left: 2.5rem;
  }

  .jNav-menu-item-md {
    display: block;
  }
}
</style>
