<template>
	<div
		class="profile__about"
		:class="{
			'hidden': data.user === undefined || data.user === null,
			'v-loading': loading
		}"
	>
		<template v-if="data.user !== undefined && data.user !== null">
			<div class="profile__name">
				{{ data.user.name + ' ' + data.user.last_name }}
			</div>
			<div class="profile__job profile-border">
				{{ data.position != null ? data.position.position : 'Без должности' }}
				<router-link
					v-if="isAdmin"
					to="/timetracking/settings?tab=2#nav-home"
				>
					<i class="fa fa-pen pulse-anim" />
				</router-link>
			</div>
			<div
				class="profile__job profile-border py-2"
			>
				<span v-html="data.groups" />
				<router-link
					v-if="isAdmin"
					to="/timetracking/settings?tab=2#nav-home"
				>
					<i class="fa fa-pen pulse-anim" />
				</router-link>
			</div>
			<div class="profile__salary profile-border">
				ОКЛАД: {{ data.salary }}
			</div>
			<div class="profile__wrapper">
				<p class="profile-border">
					{{ data.workingDay }}
				</p>
				<p class="profile-border">
					{{ data.schedule }}
				</p>
				<p class="profile-border">
					{{ data.workingTime }}
				</p>
			</div>
		</template>

		<!-- <select class="select-css" v-model="data.currency">
					<option v-for="key in Object.keys(data.currencies)" :value="key">
						{{ key }} {{ data.currencies[key] }}
					</option>
			</select> -->
	</div>
</template>
<script>
export default {
	name: 'ProfileInfo',
	props: {
		data: Object
	},
	data() {
		return {
			loading: false
		}
	},
	created() {
	},
	computed: {
		isAdmin(){
			return this.$store.state.user.user.is_admin === 1;
		}
	},
	methods: {}
}
</script>

<style lang="scss" scoped>
</style>
