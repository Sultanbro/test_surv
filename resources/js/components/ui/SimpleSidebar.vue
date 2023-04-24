<template>
	<div
		class="ui-simple-sidebar"
		:class="[{'is-open':open}]"
		@click.self="$emit('close')"
	>
		<div
			class="ui-simple-sidebar-content"
			:style="`width:${width}`"
		>
			<div class="ui-simple-sidebar-header">
				<p class="ui-simple-sidebar-title">
					{{ title }}
				</p>
				<span v-html="link" />
			</div>
			<div class="ui-simple-sidebar-body">
				<slot name="body" />
			</div>
			<div class="ui-simple-sidebar-footer">
				<slot name="footer" />
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'UISidebar',
	props: {
		title: {},
		open: {},
		width: {},
		link: {
			default: ''
		},
	},
	data() {
		return {}
	},
}
</script>

<style lang="scss" scoped>
	.ui-simple-sidebar {
		background: rgba(0, 0, 0, 0);
		width: 100%;
		height: 100%;
		left: 0;
		top: 0;
		position: fixed;
		visibility: hidden;
		transition: 0.3s ease-in-out all;
		z-index: 101;

		&-content {
			position: absolute;
			right: -100%;
			top: 0;
			border-radius: 12px 0 0 12px;
			min-height: 450px;
			background: #fff;
			z-index: 12;
			box-shadow: -10px 0px 60px -40px rgba(45, 50, 90, 0.1),
			0px 0px 3px 0px rgba(0, 0, 0, 0.05);
			transition: 0.3s ease-in-out all;
			overflow: auto;
		}

		&.is-open {
			visibility: visible;
			background: rgba(0, 0, 0, 0.45);

			.ui-simple-sidebar-content {
				right: 60px;
			}

		}

		.ui-simple-sidebar-header {
			padding: 30px 25px 15px 25px;

			.ui-simple-sidebar-title {
				color: #0A1323;
				font-size: 18px;
				margin: 0;
				font-weight: 700;
			}
		}

		.ui-simple-sidebar-body {
			overflow: auto;
			max-height: calc(100vh - 135px);
			min-height: calc(100vh - 135px);
			padding: 15px 25px 25px 25px;
		}
		.ui-simple-sidebar-footer{
			padding: 15px 25px;
			border-top: 1px solid #ddd;
		}
	}
</style>
