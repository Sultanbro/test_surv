<template>
	<a
		:href="href"
		class="jFooter-menu-link jFooter-link"
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
.jFooter-menu-link {
  display: inline-block;
  margin: 0.5rem 0;
  position: relative;
  font-size: 1rem;
  &:hover {
    text-decoration: none;
    border-bottom: 0;
    &:after {
      content: '';
      display: block;
      position: absolute;
      left: 0;
      right: 0;
      bottom: 0;
      //border-bottom: 0.125rem dotted #000;
    }
  }
}
</style>
