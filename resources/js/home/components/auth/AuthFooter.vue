<template>
	<div class="AuthFooter">
		<div class="AuthFooter-text">
			{{ lang.text }}
		</div>
		<div class="AuthFooter-text">
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

<style lang="scss">
$md2: 991.98px;
$md3: 767.98px;
$md4: 479.98px;
.AuthFooter {
	margin-top: 30px;
	display: flex;
	justify-content: space-between;
	// @media (max-width: $md2) {
	// 	flex-direction: column;
	// 	text-align: center;
	// }
	&-text {
		font-size: 12px;
		color: #737b8a;
		// @media (max-width: $md2) {
		// 	margin-bottom: 10px;
		// }
	}
}
@container auth-info (max-width: 991.98px){
	.AuthFooter{
		margin-top: 15px;
	}
}
</style>
