<template>
	<a
		:to="href"
		class="jNav-menu-link"
		@click="scrollToElement"
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
	},
	methods: {
		scrollToElement() {
			setTimeout(() => {
				const element = document.querySelector(this.href);
				if (element) {
					element.scrollIntoView({ behavior: 'smooth' });
				}
			}, 0);
		}
	}
};
</script>

<style lang="scss">
.jNav-menu-link {
  display: inline-block;
  padding: 0.5rem 0;
  position: relative;
  font-size: 1.125rem;
  color: #000;
  text-decoration: none;
  text-align: left;
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
