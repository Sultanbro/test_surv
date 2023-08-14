<template>
	<label class="input-text">
		<input
			v-model="localValue"
			type="text"
			class="input-text-input"
			:placeholder="placeholder"
		>
	</label>
</template>

<script>
export default {
	props: {
		placeholder: {
			type: String,
			default: ''
		},
		value: {
			type: String,
			default: ''
		}
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

<style lang="scss">
.input-text {
  display: block;
  padding: 0;
  margin: 0.75rem 0;
  border: 0.0625rem solid #d9d9d9;
  border-radius: 1rem;
  position: relative;
}
.input-text-input {
  display: block;
  width: 100%;
  padding: 1.125rem;
  border: none;
  border-radius: 1rem;
  font-size: 1rem;
  text-align: center;
  line-height: 1;
  color: #000;
  background: #fff;

  &:focus {
    outline: none;
    & ~ .input-text-bar {
      &:before {
        width: 100%;
      }
    }
  }
}
.input-text-input:not(:placeholder-shown) ~ .input-text-label,
.input-text-input:focus ~ .input-text-label {
  top: -0.357rem;
  font-size: 0.875rem;
  line-height: 1;
  color: #000;
  transform: translate(-50%, 0);
}
.input-text-label {
  position: absolute;
  z-index: 1;
  top: 50%;
  left: 50%;
  font-size: 1rem;
  line-height: 1;
  color: #777;
  background: #fff;
  transform: translate(-50%, -50%);
  transition: 300ms ease all;
  white-space: nowrap;
}
</style>
