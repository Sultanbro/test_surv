<template>
	<div class="mt-5 quality">
		<div class="mb-3">
			Кол-во показателей: <b>{{ totalCount }}</b> , Среднее значение: <b>{{ totalAvg }}</b>
		</div>
		<div class="table-responsive table-container">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="b-table-sticky-column text-left t-name wd">
							<div>Сотрудник</div>
						</th>
						<th
							v-for="(field, key) in fields"
							:key="key"
							:class="field.klass"
						>
							<div>{{ field.name }}</div>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr
						v-for="(item, index) in users"
						:key="index"
					>
						<td class="b-table-sticky-column text-left t-name wd bg-white">
							<div class="d-flex">
								{{ item.name }}

								<JobtronCup
									v-if="topUsers.includes(item.id)"
									:place="topUsers.indexOf(item.id) + 1"
									rotate
								/>
							</div>
						</td>
						<template v-for="(field, key) in fields">
							<td
								:key="key"
								:class="field.klass"
							>
								<div v-if="item[field.key] != 0">
									{{ item[field.key] }}
								</div>
							</td>
						</template>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>

<script>
/* eslint-disable camelcase */
import JobtronCup from '@ui/Cup'

export default {
	name: 'TableQualityWeekly',
	components: {
		JobtronCup,
	},
	props: {
		monthInfo: {
			type: Object,
			default: null
		},
		items: {
			type: Array,
			default: () => []
		},
		weeks: {
			type: Array,
			default: () => []
		},
	},
	data() {
		return {
			users: [],
			user_ids: {},
			total_avg: 0,
			total_count: 0,
			loader: null,
		}
	},

	computed: {
		totalCount(){
			return this.users.reduce((result, user) => user.total > 0 ? result + 1 : result, 0)
		},
		totalAvg(){
			if(this.totalCount <= 0) return 0
			return Math.round((this.users.reduce((result, user) => {
				if(user.total > 0) {
					result += user.total
				}
				return result
			}, 0) / this.totalCount) * 100) / 100
		},
		sortedUsers(){
			return this.users.slice().sort((a, b) => b.total - a.total)
		},
		topUsers(){
			return this.sortedUsers.slice(0, 3).map(user => user.id)
		},
		fields(){
			let order = 1
			const fieldsArray = [{
				key: 'total',
				name: 'Итог',
				order: order++,
				klass: ' text-center px-1 t-total'
			}]

			this.weeks.forEach((week, weekIndex) => {
				week.forEach((day, dayIndex) => {
					fieldsArray.push({
						key: day,
						name: day,
						order: order++,
						klass: 'text-center px-1',
						type: 'day'
					})

					if(dayIndex + 1 === week.length){
						fieldsArray.push({
							key: 'avg' + (weekIndex + 1),
							name: 'Ср. ' + (weekIndex + 1),
							order: order++,
							klass: 'text-center px-1 averages',
							type: 'avg'
						})
					}
				})
			})
			return fieldsArray
		}
	},

	created() {
		this.users = this.items;
		this.setLeaders();
	},

	methods: {
		setLeaders() {
			this.users.forEach(item => {
				item.show_cup = 0;
			});

			let arr = this.users;
			arr.sort((a, b) => Number(a.total) < Number(b.total)  ?
				1 : Number(a.total) > Number(b.total) ? -1 : 0);

			if(this.users.length > 3) {
				arr[0].show_cup = 1;
				arr[1].show_cup = 2;
				arr[2].show_cup = 3;
			}

			let total_avg = 0;
			let total_count = 0;

			arr.forEach(el => {
				if(Number(el.total) > 0) {
					total_avg += Number(el.total);
					total_count++;
				}
			})

			total_avg = total_count > 0 ? total_avg / total_count : 0;

			this.total_avg = total_avg
			this.total_count = total_count
		},

		// createUserIdList() {
		//     this.items.forEach((item, index) => {
		//         this.user_ids[item.id] = item.name
		//     })
		// },


		// editMode(item) {
		//     this.records.data.forEach((record, index) => {
		//         record.editable = false
		//     })
		//     item.editable = true
		// },

		// updateWeekValue(item, key) {

		//     let loader = this.$loading.show();

		//     axios.post("/timetracking/quality-control/saveweekly", {
		//             'day': key,
		//             'month': this.monthInfo.month,
		//             'year': this.currentYear,
		//             'total': item.weeks[key],
		//             'user_id': item.id,
		//         })
		//         .then((response) => {
		//             console.log(response)
		//             this.$toast.success('Сохранено');
		//             loader.hide();
		//         }).catch(function(e){
		//             loader.hide()
		//             alert(e)
		//         })
		// },

		// exportData() {
		//     var link = "/timetracking/quality-control/export";
		//     link += "?group_id=" + this.currentGroup;
		//     link += "&month=" + this.monthInfo.month;
		//     link += "&year=" + this.currentYear;
		//     window.location.href = link;
		// },

		// exportAll() {
		//     var link = "/timetracking/quality-control/exportall";
		//     link += "?month=" + this.monthInfo.month;
		//     link += "&year=" + this.currentYear;
		//     window.location.href = link;
		// },
	},
};
</script>

<style lang="scss">
.table {

	th.averages,
	td.averages {
		background-color:#28a745 !important;
		color: #fff;
	}
	.t-total {
		background-color: #28a745 !important;
		color: #fff;
	}

	.bg-white {
		background: white;
	}
}
.quality .t-name {
	min-width: 200px;
}
</style>
