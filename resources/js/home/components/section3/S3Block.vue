<template>
	<div
		:class="{'jSec3-block-right': right}"
		class="jSec3-block"
	>
		<div class="jSec3-block-image">
			<img
				:alt="alt"
				:src="require('../../assets/img/s3-b1.png').default"
				class="jSec3-block-img"
			>
		</div>
		<div class="jSec3-block-content">
			<slot />
		</div>
	</div>
</template>

<script>
export default {
	props: {
		img: {
			type: String,
			default: ''
		},
		alt: {
			type: String,
			default() {
				return ''
			}
		},
		right: Boolean
	},
	computed: {
		lang() {
			return this.$root.$data.lang
		}
	},
}
</script>

<style lang="scss">
@import '../../assets/scss/app.variables.scss';

.jSec3-block {
  padding: 0.5rem 0 1.5rem;
}

.jSec3-block-img {
  width: 100%;
  box-shadow: 0 0 1rem rgba(0, 0, 0, 0.1);
}

@media screen and (min-width: $medium) {
  .jSec3-block {
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    padding: 5rem 0;
  }
}

.jSec3-block-right {
  .jSec3-block-image {
    order: 2;
  }

  .jSec3-block-content {
    order: 1;
  }
}

.jSec3-block-image,
.jSec3-block-content {
  flex: 0 0 50%;
}

.jSec3-block-image {
  padding: 0 1.125rem;
}

.jSec3-block-content {
  padding: 1rem 2rem;
}
</style>
