<template>
	<div
		class="user-group-modal"
		ref="usersMore"
	>
		<template v-if="users.length">
			<div class="user-group-list">
				<div
					class="user-group-item"
					v-for="(user, idx) in users"
					:key="idx"
				>
					<img
						:src="user.avatar"
						alt="photo"
						class="user-group-photo"
					>
					<div>
						<p class="user-group-full-name">
							{{ user.name }} {{ user.last_name }}
						</p>
						<p class="user-group-position">
							{{ user.position }}
						</p>
					</div>
				</div>
			</div>
		</template>
	</div>
</template>
<script>
export default {
	name: 'StructureUsersMore',
	props: {
		users: {
			type: Array,
			default: () => {},
		},
	},
	computed: {
		shouldPositionAtTop() {
			const { top } = this.$refs.usersMore.getBoundingClientRect();
			return window.innerHeight - top < 540;
		},
		topStyle() {
			const difference = window.innerHeight - this.$refs.usersMore.getBoundingClientRect().top;
			return `-${540 - difference + 50}px`;
		},
	},
	mounted() {
		if (this.shouldPositionAtTop) {
			this.$refs.usersMore.style.top = this.topStyle;
		}
	},
};
</script>
