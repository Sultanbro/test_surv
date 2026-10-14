<template>
	<div
		class="AuthLayout"
		:style="['--viewport-width: ' + $viewportSize.width].join(';')"
	>
		<div class="AuthLayout-form">
			<div class="AuthLayout-container">
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
		</div>
		<div
			v-if="!isMobile"
			class="AuthLayout-info"
		>
			<MediumCurcle class="AuthLayout-curcle-1" />
			<SmallCurcle class="AuthLayout-curcle-2" />
			<VectorImage class="AuthLayout-vector" />
			<div class="AuthLayout-container">
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
		</div>
		<div class="AuthLayout-toasts">
			<slot name="toasts" />
		</div>
	</div>
</template>

<script>
import MediumCurcle from '../../assets/img/MediumCurcle.vue';
import SmallCurcle from '../../assets/img/SmallCurcle.vue';
import VectorImage from '../../assets/img/VectorImage.vue';

export default {
	name: 'AuthLayout',
	components: {
		MediumCurcle,
		SmallCurcle,
		VectorImage,
	},
	props: {},
	data() {
		return {};
	},
	computed: {
		isMobile() {
			return this.$viewportSize.width < 768;
		},
	},
	watch: {},
	created() {},
	mounted() {},
	beforeDestroy() {},
	methods: {},
};
</script>

<style scoped lang="scss">
.AuthLayout {
	display: flex;
	flex-flow: row nowrap;
	align-items: stretch;
	position: fixed;
	z-index: 1;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;

	font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
		Oxygen, Ubuntu, Cantarell, "Fira Sans", "Droid Sans", "Helvetica Neue",
		sans-serif;
	color: #333;

	background: url(/svg/login-bg.svg) top right no-repeat;

	&-form {
		container: auth-info / inline-size;
		width: 483px;
		max-width: 100%;
		// padding: 72px;
    padding: 3%;

		position: relative;
		z-index: 1;

		background: #fff;
		border-radius: 0 40px 40px 0;
		box-shadow: 4px 0px 32px 0px #c3c3c340;

		.AuthSubmit {
			margin-top: 32px;
		}
		.AuthLayout {
			&-body {
				// a {
				// 	text-decoration: none;
				// 	color: #0c50ff;
				// 	&:hover {
				// 		color: #4a7cfd;
				// 	}
				// }
			}
		}

		.AuthFooter {
			flex-direction: column;
			&-text {
				margin-bottom: 10px;
			}
		}
	}
	&-info {
		background-color: #e8eefd;
		container: auth-info / inline-size;
		flex: 1;
		padding: 19px;
		padding-left: 40px;
		position: relative;
		z-index: 1;

		.AuthLayout-curcle-1 {
			position: absolute;
			left: 750px;
			top: 90px;
			z-index: -1;
		}

		.AuthLayout-curcle-2 {
			position: absolute;
			left: 800px;
			top: 280px;
			z-index: -2;
		}

		.AuthLayout-vector {
			position: absolute;
			right: 0;
			top: 0;
			z-index: -3;
		}
	}
	&-toasts {
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		pointer-events: none;
	}

	&-container {
		display: flex;
		flex-flow: column nowrap;
		height: 100%;
	}
	&-header {
		flex: 0 0 fit-content;
	}
	&-body {
		overflow: auto;
		flex: 1;
		scrollbar-width: thin;
	}
	&-footer {
		flex: 0 0 fit-content;
	}
}

@media (max-width: 1599px) {
	.AuthLayout {
		&-form {
			width: 30%;
			padding: 48px;
		}
		&-info {
			padding: 16px;
			padding-left: 24px;
		}
		.AuthSubmit {
			margin-top: 10px;
		}
	}
}

@media (max-width: 1359px) {
	.AuthLayout {
		&-form {
			padding: 56px;
		}
		&-info {
			padding: 32px;
			.AuthLayout-vector {
				display: none;
			}
		}
	}
}

@media (max-width: 1023px) {
	.AuthLayout {
		&-form {
			width: 50%;
			padding: 32px;
		}
		&-info {
			padding: 20px;
		}
	}
}
@media (max-width: 767px) {
	.AuthLayout {
		&-form {
			width: 100%;
			border-radius: 0;
			background: url(/svg/login-bg.svg) top right no-repeat;
		}
	}
}
@media (max-width: 479px) {
	.AuthLayout {
		&-form {
			padding: 10px;
		}
	}
}
.b24-widget-button-wrapper {
	display: none !important;
}
</style>
