<template>
  <div
      @click="$emit('selected', selected)">
    <select class="jTariffs-valute" v-model="selected">
      <option value="">₽</option>
      <option>₸</option>
      <option>$</option>
    </select>
  </div>
</template>

<script>
export default {
  data() {
    return {
      selected: ''
    }
  },
  computed: {
  },
  methods: {
  }
}
</script>

<style lang="scss">
@import '../../assets/scss/app.variables.scss';

.jTariffs-valute {
  display: inline-block;
  padding: 0.25rem 0;
  margin: 0.5rem 0;
  position: relative;
  font-size: 0.9rem;
  color: #000;
  border-radius: 10px;
  border: 1px solid #43a9fe;
}
</style>
