<template>
  <div class="overlay custom-scroll-y js-overlay" @click.self="$emit('close');" :class="[{'is-open':open}]">
    <div class="popup award js-popup" :style="`width:${width}`">
      <div class="popup__content">
        <div class="popup-header">
            <a class="popup-close js-close-popup" href="#">
              <img src="/images/dist/popup-close.svg" alt="Close icon">
            </a>
            <div class="popup__header-content">
                <div class="popup__title">
                  {{ title }}
                </div>
                <div class="popup__subtitle">
                  {{ desc }}
                </div>
            </div>
        </div>
        <div class="popup__body">
          <slot></slot>
        </div>
      </div>
    </div>   
  </div>
</template>

<script>
export default {

  name: 'Popup',
  props: {
    title: {},
    desc: {},
    open: {},
    width: {},
  },

  data() {
    return {

    }
  },

}
</script>

<style lang="scss" scoped>

</style>
