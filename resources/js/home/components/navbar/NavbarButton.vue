<template>
	<a
		:href="href"
		class="jNav-menu-button"
	>
		{{ $lang(lang, text) }}
	</a>
</template>

<script>
export default {
	props: {
		href: {
			type: String,
			default: ''
		},
		lang: {
			type: String,
			default: ''
		},
		text: {
			type: String,
			default: ''
		}
	}
}
</script>

<style lang="scss">
@import '../../assets/scss/app.variables';

.jNav-menu-button {
  display: inline-block;
  padding: 0.5rem 0.75rem;
  margin: 0.5rem 0;
  vertical-align: middle;
  background: $button-color;
  border-radius: 3.125rem;
  font-size: 0.75rem;
  line-height: 0.875rem;
  text-decoration: none;
  color: #fff;

  &:hover {
    background: $button-color-hover;
  }
}

@media screen and (min-width: $medium) {
  .jNav-menu-button{
    padding: 0.875rem 1.1875rem;
    font-size: 0.938rem;
  }
}
</style>
