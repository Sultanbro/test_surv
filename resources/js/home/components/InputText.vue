<template>
  <label class="input-text">
    <input
      type="text"
      class="input-text-input"
      placeholder=""
      v-model="localValue"
    >
    <span class="input-text-label">{{ label }}</span>
  </label>
</template>

<script>
export default {
  props: {
    label: String,
    value: String
  },
  data(){
    return {
      localValue: this.value
    }
  },
  watch: {
    localValue(val){
      this.$emit('input', val)
    }
  }
}
</script>

<style lang="stylus">
.input-text
  display block
  padding 0
  margin 0.75rem 0
  border 0.0625rem solid #D9D9D9
  border-radius 1rem
  position relative
.input-text-input
  display block
  width 100%
  padding 1.125rem
  border none
  border-radius 1rem
  font-size 1rem
  line-height 1
  color #000
  background #fff
  &:focus
    outline none
  &:not(:placeholder-shown) ~ .input-text-label
  &:focus ~ .input-text-label
    left 1.5rem
    top -0.357rem
    font-size 0.875rem
    line-height 1
    color #000
    transform none
  &:focus ~ .input-text-bar:before
    width 100%
.input-text-label
  position absolute
  z-index 1
  top 50%
  left 50%
  font-size 1rem
  line-height 1
  color #777
  background #fff
  transform translate(-50%, -50%)
  transition 300ms ease all
  white-space nowrap
</style>
