<template>
	<div
		v-if="users.length"
		id="RefStat"
		class="RefStat index block _anim _anim-no-hide"
		:class="{
			'v-loading': !isReady,
		}"
	>
		<div class="title index__title mt-6 mb-4">
			Реферальная программа «Business&nbsp;Friends»
		</div>

		<div
			v-if="refUser"
			class="RefStat-table index__table"
		>
			<div class="ovx">
				<div class="RefStat-diff">
					<div class="RefStat-self">
						<div class="RefStat-subtitle">
							Вы
						</div>
						<div class="RefStat-users">
							<div class="RefStat-user RefStat-user_self">
								<JobtronAvatar
									:image="`users_img/${user.img_url}`"
									:title="`${user.name} ${user.last_name}`"
								/>
								<div class="RefStat-userName">
									{{ user.name }}
								</div>
								<div class="RefStat-userLeads">
									Принято: {{ accepted }}
								</div>
							</div>
						</div>
					</div>
					<div class="RefStat-tops">
						<div class="RefStat-subtitle">
							Топчики
						</div>
						<div class="RefStat-users">
							<template v-for="topUser in tops">
								<div
									v-if="topUser.accepted"
									:key="topUser.id"
									class="RefStat-user RefStat-user_top"
								>
									<JobtronAvatar
										:image="topUser.avatar"
										:title="`${topUser.name} ${topUser.lastName}`"
									/>
									<div class="RefStat-userName">
										{{ topUser.name }}
									</div>
									<div class="RefStat-userLeads">
										Принято: {{ topUser.accepted }}
									</div>
								</div>
							</template>
						</div>
					</div>
				</div>
				<RefStatsTable
					:fields="tableFields"
					:items="[refUser]"
					single
				/>
			</div>
		</div>
	</div>
</template>

<script>
import { mapGetters } from 'vuex'
import { mapState } from 'pinia'
import { useReferralStore } from '@/stores/Referral'
import {
	tableFieldsProfile,
	// getFakeReferer,
} from '@/components/pages/Analytics/helper'

import RefStatsTable from '@/components/pages/Analytics/RefStatsTable.vue'
import JobtronAvatar from '@ui/Avatar.vue'

export default {
	name: 'RefStat',
	components: {
		RefStatsTable,
		JobtronAvatar,
	},
	data(){
		return {
			tableFields: tableFieldsProfile,
			// uncollapsed: [],

			sortSubCol: 'title',
			sortSubOrder: 'desc',
			sortFn: {
				str: (a, b) => a.localeCompare(b),
				int: (a, b) => (parseInt(a) || 0) - (parseInt(b) || 0),
				float: (a, b) => (parseFloat(a) || 0) - (parseFloat(b) || 0),
			},
		}
	},
	computed: {
		...mapGetters(['user']),
		...mapState(useReferralStore, [
			'users',
			'tops',
			'accepted',
			'isReady',
		]),
		// tops(){
		// 	return [
		// 		getFakeReferer(),
		// 		getFakeReferer(),
		// 		getFakeReferer(),
		// 		getFakeReferer(),
		// 		getFakeReferer(),
		// 	]
		// },
		// refUser(){
		// 	return getFakeReferer()
		// },
		refUser(){
			/* global Laravel */
			if(!this.users) return null
			return this.users.find(user =>  user.id === Laravel.userId)
		},
	},
	mounted(){},
	methods: {},
}
</script>

<style lang="scss">
.ovx{
	overflow-x: auto;
}
.RefStat{
	$cellpadding: 8px 10px;
	$bgmargin: -8px -10px;

	font-size: 14px;

	&-table{
		// overflow-x: auto;
		overflow-x: auto;
		> .JobtronTable{
			width: auto;
		}
	}
	&-header{
		user-select: none;
	}



	&-title{
		width: 200px;
		min-width: 200px;
		max-width: 250px;

		overflow: hidden;

		white-space: nowrap;
		text-overflow: ellipsis;
	}
	&-money{
		padding: $cellpadding;
		margin: $bgmargin;
		background-color: #fdd;
		&_paid{
			background-color: #dfd;
		}
	}
	&-switch{
		padding: $cellpadding;
		margin: $bgmargin;
	}
	&-diff{
		display: flex;
		flex-flow: row nowrap;
		align-items: flex-start;
		gap: 40px;
		margin-bottom: 20px;
	}
	// &-self,
	// &-tops{}
	&-self{
		flex: 0 0 150px;
	}
	&-tops{
		flex: 1 0 auto;
		margin-left: 30px;
	}
	&-subtitle{
		margin-bottom: 20px;
		font-size: 20px;
		font-weight: 700;
		text-align: center;
	}
	&-users{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		justify-content: flex-start;
		// gap: 20px;
	}
	&-user{
		flex: 0 0 150px;
		display: flex;
		flex-flow: column nowrap;
		align-items: center;
		gap: 10px;
	}
	.RefStatsTable-title{
		width: 298px;
		min-width: 298px;
		max-width: 298px;
	}

	.JobtronTable-head .JobtronTable-row:first-child .JobtronTable-th:first-child::before{
		display: none;
	}
	@media (min-width: 1480px) {
		.RefStat{
			&-users{
				justify-content: center;
			}
		}
	}
}
</style>
