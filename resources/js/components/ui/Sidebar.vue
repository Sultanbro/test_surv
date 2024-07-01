<template>
	<div
		v-scroll-lock="open"
		class="UISidebar"
		:class="[{'UISidebar_open': open}]"
		@click.self="$emit('close');"
	>
		<div
			class="UISidebar-body"
			:style="`width: ${width}`"
		>
			<div
				v-if="$slots.header || title"
				class="UISidebar-header"
			>
				<slot name="header">
					<div class="UISidebar-title">
						{{ title }}
					</div>
					<!-- eslint-disable -->
					<!-- wtf???? -->
					<div
						class="UISidebar-link"
						v-html="link"
					/>
					<!-- eslint-enable -->
				</slot>
			</div>
			<div class="UISidebar-content">
				<slot />
			</div>
			<div
				v-if="$slots.footer"
				class="UISidebar-footer"
			>
				<slot name="footer" />
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'UISidebar',
	props: {
		title: {
			type: String,
			default: ''
		},
		open: {
			type: Boolean
		},
		width: {
			type: String,
			default: ''
		},
		link: {
			type: String,
			default: ''
		},
	},
	data() {
		return {}
	},
}
</script>

<style lang="scss">
.UISidebar{
	width: 100vw;
	height: 100vh;
	position: fixed;
	z-index: 11;
	top: 0;
	left: 100vw;

	background: rgba(0, 0, 0, 0.45);

	&-body{
		display: flex;
		flex-flow: column;
		height: 100vh;

		position: absolute;
		z-index: 10;
		top: 0;
		right: -100%;

		background: #fff;
		box-shadow: 1px 1px 4px rgba(0, 0, 0, 0.64);
    transition: 0.3s ease-in-out all;
		border-radius: 16px 0 0 16px;
	}
	&-header{
		display: flex;
		// flex-flow: column;
		padding: 12px 20px;
		border-radius: 16px 0 0 0;
		border-bottom: 1px solid #ddd;
		background-color: #8fc9ff;
	}
	&-title{
		font-weight: 700;
		font-size: 14px;
		color: #333;
	}
	// &-link{}
	&-content{
		flex: 1;
		padding: 20px;

		font-weight: 400;
		font-size: 14px;

		border-radius: 16px 0 0 16px;
		overflow: auto;
	}
	&-footer{
		padding: 12px 20px;
		border-top: 1px solid #ddd;
		border-radius: 0 0 0 16px;
	}

	&_open{
		left: 0;
		.UISidebar{
			&-body{
				right: 60px;
			}
		}
	}
}
</style>
