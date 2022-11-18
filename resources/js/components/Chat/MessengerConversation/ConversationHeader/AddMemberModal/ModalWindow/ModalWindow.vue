<template>
  <div class="messenger__modal-backdrop">
    <div class="messenger__modal">
      <header class="messenger__modal-header">
        <slot name="header">
          This is the default title!
        </slot>
        <button
          type="button"
          class="btn-close"
          @click="close"
        >
          x
        </button>
      </header>

      <section class="messenger__modal-body">
        <slot name="body">
          This is the default body!
        </slot>
      </section>

      <footer class="messenger__modal-footer">
        <slot name="footer">
          This is the default footer!
        </slot>
        <button
          v-if="closeButton"
          type="button"
          class="btn-green"
          @click="close"
        >
          Закрыть
        </button>
      </footer>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    close() {
      this.$emit('close');
    },
  },
  props: {
    closeButton: {
      type: Boolean,
      default: true,
    },
  },
};
</script>

<style>
.messenger__modal-backdrop {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: rgba(0, 0, 0, 0.3);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2;
}

.messenger__modal {
  background: #FFFFFF;
  box-shadow: 2px 2px 20px 1px;
  /*overflow-x: auto;*/
  display: flex;
  flex-direction: column;
  min-width: 350px;
  max-width: 600px;
  max-height: calc(100vh - 100px);
}

.messenger__modal-header, .messenger__modal-footer {
  padding: 15px;
  display: flex;
}

.messenger__modal-header {
  position: relative;
  border-bottom: 1px solid #eeeeee;
  color: #0a0a0a;
  justify-content: space-between;
}

.messenger__modal-footer {
  border-top: 1px solid #eeeeee;
  flex-direction: column;
  justify-content: flex-end;
}

.messenger__modal-body {
  position: relative;
  padding: 20px 10px;
}

.btn-close {
  position: absolute;
  top: 0;
  right: 0;
  border: none;
  font-size: 20px;
  padding: 10px;
  cursor: pointer;
  font-weight: bold;
  color: #4AAE9B;
  background: transparent;
}

</style>
