<template functional>
	<button
		v-bind="data.attrs"
		:class="['AuthSubmit', data.staticClass]"
		type="submit"
	>
		<slot />
	</button>
</template>

<script>
export default {
	name: 'AuthSubmit',
	components: {},
	props: {},
	data() {
		return {};
	},
	computed: {},
	watch: {},
	created() {},
	mounted() {},
	beforeDestroy() {},
	methods: {},
};
</script>

<style lang="scss">
.AuthSubmit {
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 4px;

	width: 90%;
	height: 48px;
	padding: 16px;
	border: none;

	color: #fff;

	border-radius: 8px;
	background-color: #0c50ff;
	&:hover {
		background-color: #4a7cfd;
	}
	&:focus {
		outline: none;
		background-color: #4a7cfd;
	}
	&_disabled {
		background-color: #777;
		&:hover {
			background-color: #777;
		}
		button {
			cursor: wait;
		}
	}
}
</style>
