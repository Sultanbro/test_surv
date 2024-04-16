<template>
	<div class="section__footer">
		<div class="section__footer__text">
			{{ lang.text }}
		</div>
		<div class="section__footer__text">
			{{ lang.copy }}
		</div>
	</div>
</template>

<script>
import * as LANG from './AuthFooter.lang.js'

export default {
	name: 'AuthFooter',
	components: {},
	props: {},
	data(){
		return {}
	},
	computed: {
		lang(){
			return LANG[this.$root.$data.lang || 'ru']
		},
	},
	watch: {},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {},
}
</script>

<style lang="scss" scoped>
$md2: 991.98px;
$md3: 767.98px;
$md4: 479.98px;
.section__footer {
	margin-top: 30px;
	display: flex;
	justify-content: space-between;
	@media (max-width: $md2) {
		flex-direction: column;
		text-align: center;
	}
	&__text {
		font-size: 14px;
		color: #737b8a;
		@media (max-width: $md2) {
			margin-bottom: 10px;
		}
	}
}
</style>
