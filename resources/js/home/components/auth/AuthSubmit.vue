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
.AuthSubmit{
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 4px;

	width: 100%;
	height: 48px;
	padding: 16px;
	border: none;

	color: #fff;

	border-radius: 8px;
	background-color: #0C50FF;
	&:hover{
		background-color: #4A7CFD;
	}
	&:focus{
		outline: none;
		background-color: #4A7CFD;
	}
	&_disabled{
		background-color: #777;
		&:hover{
			background-color: #777;
		}
		button{
			cursor: wait;
		}
	}
}
</style>
