<template>
	<div
		class="user-group-modal"
		ref="usersMore"
	>
		<div class="user-group-list">
			<div
				class="user-group-item"
				v-for="(user, idx) in users"
				:key="idx"
			>
				<img
					:src="user.photo"
					alt="photo"
					class="user-group-photo"
				>
				<div>
					<p class="user-group-full-name">
						{{ user.fullName }}
					</p>
					<p class="user-group-position">
						{{ user.position }}
					</p>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default{
	name: 'StructureUsersMore',
	props:{
		users: Array
	},
	mounted(){
		const difference = window.innerHeight - this.$refs.usersMore.getBoundingClientRect().top;
		if (difference < 540) {
			this.$refs.usersMore.style.top = `-${540 - difference + 50}px`;
		}
	}
}
</script>
