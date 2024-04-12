<template>
	<div class="AuthLayout">
		<div class="AuthLayout-form AuthLayout-container">
			<div class="AuthLayout-header">
				<slot name="form-header" />
			</div>
			<div class="AuthLayout-body">
				<slot name="form" />
			</div>
			<div class="AuthLayout-footer">
				<slot name="form-footer" />
			</div>
		</div>
		<div class="AuthLayout-info AuthLayout-container">
			<div class="AuthLayout-header">
				<slot name="info-header" />
			</div>
			<div class="AuthLayout-body">
				<slot name="info" />
			</div>
			<div class="AuthLayout-footer">
				<slot name="info-footer" />
			</div>
		</div>
		<div class="AuthLayout-toasts">
			<slot name="toasts" />
		</div>
	</div>
</template>

<script>
export default {
	name: 'AuthLayout',
	components: {},
	props: {},
	data(){
		return {}
	},
	computed: {},
	watch: {},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {},
}
</script>

<style lang="scss">
.AuthLayout{
	display: flex;
	flex-flow: row nowrap;
	align-items: stretch;

	position: fixed;
	z-index: 1;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;

	font-family: Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;
	color: #333;

	background: url(/svg/login-bg.svg) top right no-repeat;
	&-form{
		width: 100%;
		max-width: 564px;
		padding: 72px;

		position: relative;
		z-index: 1;

		background: #fff;
		border-radius: 0 40px 40px 0;
		box-shadow: 4px 0px 32px 0px #C3C3C340;
	}
	&-info{
		flex: 1;
		padding: 32px;
		padding-left: 80px;
	}
	&-toasts{
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		pointer-events: none;
	}

	&-container{
		display: flex;
		flex-flow: column nowrap;
		height: 100%;
	}
	&-header{
		flex: 0 0 fit-content;
	}
	&-body{
		flex: 1;
		overflow-y: auto;
	}
	&-footer{
		flex: 0 0 fit-content;
	}
}
</style>
