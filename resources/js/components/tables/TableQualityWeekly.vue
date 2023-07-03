<template>
	<div class="mt-5 quality">
		<div class="mb-3">
			Кол-во показателей: <b>{{ total_count }}</b> , Среднее значение: <b>{{ total_avg }}</b>
		</div>
		<div class="table-responsive table-container">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="b-table-sticky-column text-left t-name wd">
							<div>Сотрудник</div>
						</th>
						<th
							:class="field.klass"
							v-for="(field, key) in fields"
							:key="key"
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
									:place="item.show_cup"
									rotate
								/>
							</div>
						</td>
						<template v-for="(field, key) in fields">
							<td
								:class="field.klass"
								:key="key"
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
import JobtronCup from '@ui/Cup'

export default {
	name: 'TableQualityWeekly',
	components: {
		JobtronCup,
	},
	props: {
		monthInfo: Object,
		items: Array,
	},
	data() {
		return {
			users: [],
			fields: [],
			user_ids: {},
			total_avg: 0,
			total_count: 0,
			loader: null,
		};
	},

	created() {

		this.setWeeksTableFields()

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
			console.log('OKK avg is ' + total_count + ' - ' + total_avg);
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

		setWeeksTableFields() {

			let fieldsArray = []
			let weekNumber = 1;
			let order = 1;

			fieldsArray.push({
				key: 'total',
				name: 'Итог',
				order: order++,
				klass: ' text-center px-1 t-total'
			})


			for(let i = 1; i <= this.monthInfo.daysInMonth; i++) {

				let m = this.monthInfo.month.toString()
				let d = i
				if(d.toString().length == 1) d = '0' + d;
				if(m.length == 1) m = '0' + m;
				//console.log(this.currentYear + '-' + m + '-' + d)

				let date = this.$moment(this.monthInfo.currentYear + '-' + m + '-' + d);
				let dow = date.day();

				fieldsArray.push({
					key: i,
					name: i,
					order: order++,
					klass: 'text-center px-1',
					type: 'day'
				})



				if(dow == 0) {
					fieldsArray.push({
						key: 'avg' + weekNumber,
						name: 'Ср. ' + weekNumber ,
						order: order++,
						klass: 'text-center px-1 averages',
						type: 'avg'
					})
					weekNumber++
				}

				if(dow != 0 && i == this.monthInfo.daysInMonth) {
					fieldsArray.push({
						key: 'avg' + weekNumber,
						name: 'Ср. ' + weekNumber,
						order: order++,
						klass: 'text-center px-1 averages',
						type: 'avg'
					})
				}
			}

			this.fields = fieldsArray

		},

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
