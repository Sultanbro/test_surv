<template>
	<a
		:href="href"
		class="jNav-menu-link"
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
.jNav-menu-link {
  display: inline-block;
  padding: 0.25rem 0;
  margin: 0.5rem 0;
  position: relative;
  font-size: 1.125rem;
  color: #000;
  text-decoration: none;
  text-align: center;
  &:hover {
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
