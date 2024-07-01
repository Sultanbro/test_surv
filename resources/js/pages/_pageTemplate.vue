<template>
	<div />
</template>

<script>
export default {
	name: 'KBPage',
	components: {},
	props: {
		staff: {
			type: Array,
			default: () => []
		},
		staffByGroup: {
			type: Object,
			default: () => ({})
		},
		staffLongevity: {
			type: Object,
			default: null
		},
		causes: {
			type: String,
			default: ''
		},
	},
	data() {
		return {
			fields: [],
		};
	},
	computed: {},
	watch: {},
	created() {},
	mounted() {},
	beforeDestroy() {},
	methods: {}
};
</script>

<style lang="scss" scoped>

</style>
