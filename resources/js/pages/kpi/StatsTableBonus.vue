<template>
	<div class="bonuses 1">
		<table class="table j-table table-bordered table-sm mb-3 collapse-table">
			<tr>
				<th class="b-table-sticky-column text-center px-1">
					<!-- <i
						class="fa fa-cog"
						@click="adjustFields"
					/> -->
				</th>
				<th class="text-left">
					Кому
				</th>
			</tr>
			<tr />
			<template v-for="(page_item, index) in groups">
				<tr :key="index">
					<td
						class="pointer b-table-sticky-column"
						@click="page_item.expanded = !page_item.expanded"
					>
						<div class="d-flex px-2">
							<i
								v-if="page_item.expanded"
								class="fa fa-minus mt-1"
							/>
							<i
								v-else
								class="fa fa-plus mt-1"
							/>
							<span class="ml-2">{{ index + 1 }}</span>
						</div>
					</td>
					<td class="text-left">
						<div class="d-flex aic p-1">
							<span class="ml-2">{{ page_item.name }}</span>
						</div>
					</td>
				</tr>
				<template v-if="page_item.expanded">
					<tr
						:key="'tr' + index"
						class="collapsable"
						:class="{'active': page_item.expanded }"
					>
						<td :colspan="fields.length + 2">
							<StatsTableBonusUser
								v-if="page_item.targetable_type === 'App\\User'"
								:user="page_item"
								class="table__wrapper"
							/>
							<StatsTableBonusGroup
								v-else
								:group="page_item"
								class="table__wrapper"
							/>
						</td>
					</tr>
				</template>
			</template>
			<tr />
		</table>
	</div>
</template>

<script>
import StatsTableBonusGroup from './StatsTableBonusGroup.vue'
import StatsTableBonusUser from './StatsTableBonusUser.vue'

export default {
	name: 'StatsTableBonus',
	components: {
		StatsTableBonusGroup,
		StatsTableBonusUser,
	},
	props: {
		groups: {
			type: Array,
			default: () => []
		},
		/* eslint-disable-next-line camelcase, vue/prop-name-casing */
		group_names: {
			type: Object,
			default: null,
		},
		month: {
			type: Number,
			default: 0
		}
	},
	data() {
		return {
			bonuses: [],
			obtainedBonuses: [],
			users:[],
			// activities: [],
			fields: [],
			items: [],
			myGroups: []
		}
	},
	computed: {
		// filteredBonuses(){
		// 	return this.obtainedBonuses.filter(bonus => bonus.user_id === this.user.id)
		// }
	},
	watch: {},

	created() {
		// this.getActivities();
	},

	mounted() {},
	methods: {
		// getActivities(){
		// 	this.axios.get('/statistics/activities').then(response => {
		// 		this.activities = response.data;
		// 	});
		// },
		adjustFields(){},
		expand(item){
			item.expanded = !item.expanded;
		},
	},

}
</script>
