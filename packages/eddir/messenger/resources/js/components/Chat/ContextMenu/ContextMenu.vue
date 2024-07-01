<template>
  <div :style="style" v-show="show"
       class="messenger__context-menu"
       @mousedown.stop
       @contextmenu.prevent
  >
    <slot></slot>
  </div>
</template>

<script>
export default {
  props: {
    show: Boolean,
    x: null,
    y: null,
    parentElement: null
  },
  watch: {
    x() {
      this.updateStyle();
    },
    y() {
      this.updateStyle();
    }
  },
  data() {
    return {
      style: {
        top: 0,
        left: 0
      }
    }
  },
  methods: {
    updateStyle() {
      if (this.parentElement) {
        const messengerWindowRect = this.parentElement.getBoundingClientRect();
        const x = this.x - messengerWindowRect.left;
        const y = this.y - messengerWindowRect.top;

        if (x + this.$el.offsetWidth + 50 > messengerWindowRect.width) {
          this.style.left = messengerWindowRect.width - this.$el.offsetWidth + 'px';
        } else {
          this.style.left = x + 'px';
        }

        if (y + this.$el.offsetHeight > messengerWindowRect.height) {
          this.style.top = messengerWindowRect.height - this.$el.offsetHeight + 'px';
        } else {
          this.style.top = y + 'px';
        }
      }
    }
  }
}
</script>

<style>

.messenger__context-menu {
  position: absolute;
  background-color: #fff;
  border: 1px solid #e5e5e5;
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
  z-index: 1000;
  display: block;
  min-width: 100px;
}

.messenger__context-menu a {
  display: block;
  padding: 10px 10px;
  text-decoration: none;
  color: #0a0a0a;
}

.messenger__context-menu a:hover {
  background-color: #f5f5f5;
}

</style>
