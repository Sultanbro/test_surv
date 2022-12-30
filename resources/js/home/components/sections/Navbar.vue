<template>
  <nav id="jNav">
    <div class="section-content jNav-content">
      <a
        href=""
        class="jNav-logo"
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
          <li class="jNav-menu-item">
            <span class="jNav-menu-auth">
              <template
                v-if="auth"
              >
                <div class="jNav-menu-user-info">
                  <div class="jNav-menu-user-data">
                    <div
                      class="jNav-menu-user-name"
                      :title="window.Laravel.fullname"
                    >{{ window.Laravel.fullname }}</div>
                    <div
                      class="jNav-menu-user-email"
                      :title="window.Laravel.email"
                    >{{ window.Laravel.email }}</div>
                  </div>
                </div>
                <div
                  class="jNav-menu-user"
                  @click="isUserMenu = !isUserMenu"
                >
                  <div
                    v-if="isUserMenu"
                    class="jNav-menu-user-menu"
                  >
                    <div class="jNav-menu-user-menu-item" v-for="cabinet in cabinets">
                      <a :href="'/login/' + cabinet.tenant_id">{{ cabinet.tenant_id }} + window.location.hostname</a>
                    </div>

                    <form
                      ref="formLogout"
                      class="jNav-menu-user-menu-item"
                      method="POST"
                      action="/logout"
                    >
                      <input
                        type="hidden"
                        :value="csrf"
                        name="csrf"
                      >
                      <button @click="$refs.formLogout.submit()" class="jNav-menu-user-menu-exit">
                        Выход
                      </button>
                    </form>
                  </div>
                </div>
              </template>
              <template v-else>
                <NavbarButton
                  :lang="lang"
                  href="/login"
                  text="auth"
                />
                <NavbarButton
                  :lang="lang"
                  href="/register"
                  text="register"
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
    }
  },
  data() {
    return {
      menu: false,
      csrf: window.Laravel.csrfToken,
      isUserMenu: false,
      cabinets: window.Laravel.cabinets,
      auth: window.Laravel.email !== undefined
    }
  },
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
  box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.05);
}

.jNav-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.jNav-logo-img {
  width: 8rem;
}

.jNav-menu-active {
  .jNav-menu-items {
    display: flex;
  }

  .jNav-menu-bg {
    display: block;
  }
}

.jNav-menu-user-info{
  display: flex;
  flex-flow: row nowrap;
  flex: 1 1 auto;
  align-items: center;
}
.jNav-menu-user-data{
  display: flex;
  flex-flow: column;
  flex: 0 1 10em;
  overflow: hidden;
}
.jNav-menu-user-name,
.jNav-menu-user-email{
  max-width: 10em;
  overflow: hidden;
  text-overflow: ellipsis;
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

.jNav-menu-item-md{
  display: none;
}

.jNav-menu-user-menu{
  padding: 0.5rem;
  position: absolute;
  z-index: 5;
  top: 100%;
  right: 0;
  background-color: #fff;
  box-shadow: 0 0.125rem 0.1875rem rgba(0,0,0,0.5);
}
.jNav-menu-user-menu-item{
  white-space: nowrap;
  cursor: pointer;
}
.jNav-menu-user-menu-exit{
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
    gap: 1rem;
  }

  .jNav-menu-user {
    width: 2.625rem;
    height: 2.625rem;
    margin-left: 2.5rem;
  }

  .jNav-menu-item-md{
    display: block;
  }
}
</style>
