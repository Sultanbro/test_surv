<template>
	<div>
		<multiselect
			v-model="value"
			:options="groups"
			:multiple="true"
			:close-on-select="false"
			:clear-on-select="false"
			:preserve-search="true"
			placeholder="Выберите"
			label="name"
			track-by="name"
			@remove="removeGroup"
			@select="selectGroup"
		/>
		<!-- :taggable="true" -->
		<!-- @tag="addTag" -->
	</div>
</template>

<script>
/* eslint-disable vue/no-mutating-props */
const MEMBER = 1;
const HEAD = 2;

export default {
	name: 'ProfileGroups',
	props: {
		user_id: {
			default: null
		},
		groups: {
			default: null
		},
		in_groups: {
			default: []
		},
		user_role: {
			type: Number,
			default: MEMBER
		},
	},
	data() {
		return {
			message: null,
			value: [],
			url: '/timetracking/edit-person/group',
		}
	},
	created() {
		this.value = this.in_groups;

		if(this.user_role === HEAD) {
			this.url = '/timetracking/edit-person/head_in_groups';
		}
	},
	mounted() {},
	methods: {
		addTag(newTag) {
			const tag = {
				name: newTag,
				id: newTag
			}
			this.groups.push(tag)
			this.value.push(tag)
		},

		messageoff() {
			setTimeout(() => {
				this.message = null
			}, 3000)
		},

		selectGroup(selectedOption) {
			let data = {
				user_id: this.user_id,
				group_id: selectedOption.id,
				action: 'add',
			};

			let msg = 'Сотрудник добавлен в отдел "' + selectedOption.name + '"';

			this.request(data, msg);
		},

		removeGroup(selectedOption) {
			let data = {
				user_id: this.user_id,
				group_id: selectedOption.id,
				action: 'delete',
			};

			let msg = 'Сотрудник удален из группы "' + selectedOption.name + '"';

			this.request(data, msg);
		},

		request(data, msg) {
			this.axios.post(this.url, data)
				.then(() => {
					this.$toast.info(msg);
					this.messageoff()
				})
				.catch(error => {
					console.error(error.response)
					this.$toast.info(error.response);
				});
		},
	}
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="scss"></style>
