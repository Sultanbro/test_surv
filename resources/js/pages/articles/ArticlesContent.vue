<template>
	<main>
		<!-- eslint-disable vue/no-v-html -->
		<div
			v-if="paper.data"
			v-html="paper.data.title"
		/>
		<!-- eslint-disable vue/no-v-html -->

		<!-- eslint-disable vue/no-v-html -->
		<div
			v-if="paper.data"
			v-html="paper.data.body"
		/>
		<!-- eslint-disable vue/no-v-html -->
	</main>
</template>

<script>
export default {
	name: 'ArticlesContent',
	data() {
		return {
			currentId: '',
			paper: { data: {} },
		};
	},
	mounted() {
		this.currentId = this.getIdbyRoute(this.$route.path);
		this.getPaperById(this.currentId);
	},
	methods: {
		getIdbyRoute(path) {
			const splitRoute = path.split('/');
			return splitRoute[splitRoute.length - 1];
		},

		async getPaperById(id) {
			const data = await fetch(`http://admin.localhost/paper/get/${id}`);
			this.paper = await data.json();
		},
	},
};
</script>
