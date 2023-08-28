<template>
	<div class="profile-salary-info">
		<div class="col-xl-12">
			<div
				class="ublock"
				style="border-radius:5px 5px 0 0;"
			>
				<h2 class="big-title">
					Ваш баланс
				</h2>
				<user-earnings
					:data="userEarnings"
					:activeuserid="user_id"
					:has_quartal_premiums="hasQp"
					:month="month"
				/>
			</div>
		</div>
		<!-- Таблица начислений -->
		<div class="col-xl-12">
			<div
				id="pulse"
				class="ublock pt-0 relative"
				style="border-top: 1px solid transparent;border-radius:0 0 5px 5px"
			>
				<div class="row mb-3 mt-3">
					<div class="col-9">
						<h5>Начисления</h5>
					</div>
					<div class="col-3">
						<select
							v-model="month"
							class="form-control"
							@change="fetch()"
						>
							<option
								v-for="monthName in $moment.months()"
								:key="monthName"
								:value="monthName"
							>
								{{ monthName }}
								>
							</option>
						</select>
					</div>
				</div>

				<t-usersalary
					:activeuserid="user_id"
					:date="date"
					:month="month"
				/>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'ProfileSalaryInfo',

	props: {
		/* eslint-disable-next-line camelcase, vue/prop-name-casing */
		user_id: {
			type: Number,
			required: true
		},
	},

	data() {
		return {
			page: 1,
			date: new Date(),
			month: null,
			userEarnings: {
				'quarter_bonus' : 0,
				'oklad' : 0,
				'bonus' : 0,
				'kpis' : [],
				'bonusHistory' : [],
				'editedBonus' : [],
				'editedKpi' : [],
				'potential_bonuses' : [],
				'salary_percent' : 0,
				'kpi_percent' : 0,
				'kpi' : 0,
				'salary' : 0,
				'salary_info' : {
					'worked_days' :0,
					'indexation_sum' : 0,
					'days_before_indexation' : 0,
					'oklad' : 0,
				},
				awards: [{
					name: 'Сертификаты',
					id: 1
				}]
			},
			quarters: [],
			hasQp: false
		};
	},

	created() {
		this.month = this.$moment().format('MMMM')
		this.fetch()
	},

	mounted() {
		document.addEventListener('keyup', this.keyup);
	},

	methods: {

		fetch() {
			let loader = this.$loading.show();

			this.axios.post('/profile/salary/get', {
				month: this.$moment(this.month, 'MMMM').format('M')
			}).then(response => {
				this.userEarnings = response.data.user_earnings
				this.hasQp = response.data.has_qp
				loader.hide()
			}).catch(error => {
				loader.hide()
				alert(error)
			});
		},

	},
};
</script>

<style>

</style>
