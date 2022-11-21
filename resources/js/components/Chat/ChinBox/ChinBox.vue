<template>
  <div class="messenger__chin" @click="toggleMessenger">
    <button class="messenger__chin-button">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
           :class="!isOpen ? 'messenger__chin-button__rotate' : ''">
        <path
          d="M470.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 256 265.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160zm-352 160l160-160c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L210.7 256 73.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0z"/>
      </svg>
    </button>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";

export default {
  name: "ChinBox",
  computed: {
    ...mapGetters(['isOpen'])
  },
  methods: {
    ...mapActions(['toggleMessenger']),
  }
}
</script>

<style scoped>

.messenger__chin {
  bottom: 0;
  right: 0;
  width: 100%;
  height: 50px;
  background: #fff;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  justify-content: flex-start;
  z-index: 1;
  cursor: pointer;
}

.messenger__chin-button {
  width: 40px;
  height: 40px;
  border: none;
  margin: 20px;
  background: #fff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.messenger__chin-button:hover {
  background: #f8f9fa;
}

.messenger__chin-button svg {
  width: 24px;
  height: 24px;
  fill: #000;
}

.messenger__chin-button svg:hover {
  fill: #000;
}

.messenger__chin-button__rotate {
  transform: rotate(180deg);
}

</style>
