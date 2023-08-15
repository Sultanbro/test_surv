<template>
	<div class="StructureUsersMore">
		<div
			class="StructureUsersMore user-group-modal"
			ref="usersMore"
		>
			<template v-if="users.length">
				<div class="user-group-list">
					<div
						v-for="(user, idx) in users"
						:key="idx"
						class="user-group-item"
					>
						<img
							:src="user.avatar"
							alt="photo"
							class="user-group-photo"
						>
						<StructureInfo
							:info="{
								avatar: user.avatar,
								name: user.name,
								last_name: user.last_name,
								birthday: user.birthday,
								position: user.position_name,
								email: user.email,
							}"
						/>
						<div>
							<p class="user-group-full-name">
								{{ user.name }} {{ user.last_name }}
							</p>
							<p class="user-group-position">
								{{ user.position_name }}
							</p>
						</div>
					</div>
				</div>
			</template>
		</div>
		<div
			v-if="moreUsers"
			class="backdrop-structure-area"
			@click="showMoreUsers(null)"
		/>
	</div>
</template>

<script>
import {mapState, mapActions} from 'pinia'
import StructureInfo from './StructureInfo.vue'
import {useStructureStore} from '@/stores/Structure.js'

export default {
	name: 'StructureUsersMore',
	components: {
		StructureInfo,
	},
	props: {
		users: {
			type: Array,
			default: () => {},
		},
	},
	computed: {
		...mapState(useStructureStore, ['moreUsers']),
	},
	mounted() {},
	methods: {
		...mapActions(useStructureStore, ['showMoreUsers'])
	}
};
</script>

<style lang="scss">
.user-group-item{
	position: relative;

	&:nth-last-child(-n+4) {
		.StructureInfo{
			top: auto !important;
			bottom: calc(100% + 20px) !important;
			right: auto !important;
			left: 0 !important;
		}

		.user-group-photo{
			&:hover{
				+ .StructureInfo{
					top: auto !important;
					bottom: 100% !important;
					visibility: visible !important;
					opacity: 1 !important;
				}
			}
		}
	}
}
.user-group-photo{
	&:hover{
		+ .StructureInfo{
			top: 100% !important;
			visibility: visible !important;
			opacity: 1 !important;
		}
	}
}
.StructureUsersMore{
	.StructureInfo{
		top: calc(100% + 20px) !important;
		right: auto !important;
		left: 0 !important;
	}
}
</style>
