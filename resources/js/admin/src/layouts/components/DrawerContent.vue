<script setup lang="ts">
/* global userInfo */
import { useTheme } from 'vuetify'
import upgradeBannerDark from '@/assets/images/pro/upgrade-banner-dark.png'
import upgradeBannerLight from '@/assets/images/pro/upgrade-banner-light.png'
import logo from '@/assets/logo.svg?raw'
import { VerticalNavLink, VerticalNavSectionTitle } from '@layouts'

const vuetifyTheme = useTheme()
const upgradeBanner = computed(() => {
  return vuetifyTheme.global.name.value === 'light' ? upgradeBannerLight : upgradeBannerDark
})

const isAdmin = userInfo.is_admin
</script>

<template>
  <!-- 👉 Nav header -->
  <div class="nav-header">
    <RouterLink
      to="/"
      class="app-logo d-flex align-center gap-x-3 app-title-wrapper"
    >
      <!-- ℹ️ You can also use img tag or VImg here -->
      <div v-html="logo" />

      <Transition name="vertical-nav-app-title">
        <h1 class="font-weight-semibold leading-normal text-xl text-uppercase">Admino</h1>
      </Transition>
    </RouterLink>
  </div>

  <!-- 👉 Nav items -->
  <ul>
    <VerticalNavLink
      key="admino"
      :item="{
        title: 'Dashboard',
        to: 'index',
        icon: { icon: 'mdi-home-outline' },
      }"
    />
    <VerticalNavLink
      key="admino-user-data"
      :item="{
        title: 'Пользователи',
        to: 'admino-user-data',
        icon: { icon: 'fa-users' },
      }"
    />
    <VerticalNavLink
      v-if="isAdmin"
      key="admino-user-permissions"
      :item="{
        title: 'Доступы',
        to: 'admino-user-permissions',
        icon: { icon: 'mdi-account-lock-open-outline' },
      }"
    />
    <VerticalNavLink
      v-if="isAdmin"
      key="admino-user-faq"
      :item="{
        title: 'FAQ',
        to: 'admino-user-faq',
        icon: { icon: 'mdi-frequently-asked-questions' },
      }"
    />
    <VerticalNavLink
      v-if="isAdmin"
      key="admino-ref-linker"
      :item="{
        title: 'RefLinker',
        to: 'admino-ref-linker',
        icon: { icon: 'mdi-link' },
      }"
    />
    <VerticalNavLink
      v-if="isAdmin"
      key="admino-promo-code"
      :item="{
        title: 'RefLinker',
        to: 'admino-promo-code',
        icon: { icon: 'fa-edit' },
      }"
    />
    <VerticalNavLink
      v-if="isAdmin"
      key="payers=users"
      :item="{
        title: 'payers-users',
        to: 'admino-payers-users',
        icon: { icon: 'fa-money' },
      }"
    />
    <VerticalNavLink
      v-if="isAdmin"
      key="post-news"
      :item="{
        title: 'post-news',
        to: 'admino-post-news',
        icon: { icon: 'fa-bullhorn' },
      }"
    />
    <!-- 👉 Pages -->
    <!--
      <VerticalNavSectionTitle :item="{ heading: 'Pages' }" />
      <VerticalNavLink
      :item="{
      title: 'Login',
      to: 'login',
      target: '_blank',
      icon: { icon: 'mdi-login' }
      }"
      />
      <VerticalNavLink
      :item="{
      title: 'Register',
      to: 'register',
      target: '_blank',
      icon: { icon: 'mdi-account-plus-outline' }
      }"
      />
    -->

    <!-- ℹ️ This path doesn't exist so 404 route will catch this undefined path -->
    <!--
      <VerticalNavLink
      :item="{
      title: 'Error',
      to: { path: '/error' },
      target: '_blank',
      icon: { icon: 'mdi-alert-circle-outline' }
      }"
      />
    -->

    <!-- 👉 User Interface -->
    <!-- <VerticalNavSectionTitle :item="{ heading: 'User Interface' }" /> -->

    <!--
      <VerticalNavLink
      :item="{
      title: 'Typography',
      to: 'typography',
      icon: { icon: 'mdi-alpha-t-box-outline' }
      }"
      />
      <VerticalNavLink
      :item="{
      title: 'Icons',
      to: 'icons',
      icon: { icon: 'mdi-eye-outline' }
      }"
      />
      <VerticalNavLink
      :item="{
      title: 'Cards',
      to: 'card-basic',
      icon: { icon: 'mdi-credit-card-outline' }
      }"
      />
      <VerticalNavLink
      :item="{
      title: 'Tables',
      to: 'tables',
      icon: { icon: 'mdi-table' }
      }"
      />
      <VerticalNavLink
      :item="{
      title: 'Form Layouts',
      to: 'form-layouts',
      icon: { icon: 'mdi-form-select' }
      }"
      />
    -->
  </ul>

  <!-- 👉 illustration -->
  <!--
    <a
    href="https://themeselection.com/item/materio-vuetify-vuejs-admin-template"
    target="_blank"
    rel="noopener noreferrer"
    >
    <img
    :src="upgradeBanner"
    alt="upgrade-banner"
    transition="scale-transition"
    class="upgrade-banner mx-auto"
    style="max-width: 230px"
    >
    </a>
  -->
</template>

<style lang="scss">
// .v-navigation-drawer {
//   height: 100%;

//   .v-navigation-drawer__content {
//     display: flex;
//     flex-direction: column;

//     > ul {
//       flex-grow: 1;
//     }
//   }
// }

.upgrade-banner {
  margin-top: auto;
  // position: absolute;
  // bottom: 13px;
  // left: 50%;
  // transform: translateX(-50%);
}
</style>
