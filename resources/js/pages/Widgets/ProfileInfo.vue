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
				{{ fullName }}
			</div>
			<div class="profile__job profile-border">
				{{ data.position != null ? data.position.position : 'Без должности' }}
				<router-link
					v-if="isAdmin && moreThanDays"
					to="/timetracking/settings?tab=2#nav-home"
				>
					<span>
						<i
							class="fa fa-pen pulse-anim"
							id="info-position"
						/>
						<b-popover
							target="info-position"
							triggers="hover"
							placement="top"
						><p style="font-size: 15px">Добавить\создать новые должности</p></b-popover>
					</span>
				</router-link>
			</div>
			<div
				class="profile__job profile-border py-2"
			>
				<span v-html="data.groups" />
				<router-link
					v-if="isAdmin && moreThanDays"
					to="/timetracking/settings?tab=2&tabswitch=3"
				>
					<span>
						<i
							class="fa fa-pen pulse-anim"
							id="info-groups"
						/>
						<b-popover
							target="info-groups"
							triggers="hover"
							placement="top"
						><p style="font-size: 15px">Добавить\создать новые отделы</p></b-popover>
					</span>
				</router-link>
			</div>
			<div class="profile__salary profile-border">
				ОКЛАД: {{ data.salary }}
			</div>
			<div
				class="profile__wrapper"
				v-if="workChartUser"
			>
				<p class="profile-border">
					{{ workChartUser.name }}
				</p>
				<p class="profile-border">
					{{ workChartUser.start_time }} - {{ workChartUser.end_time }}
				</p>
				<p class="profile-border">
					{{ hour }} часов
				</p>
			</div>
		</template>

		<!-- <select class="select-css" v-model="data.currency">
					<option v-for="key in Object.keys(data.currencies)" :value="key">
						{{ key }} {{ data.currencies[key] }}
					</option>ProfileStatus.jsProfileStatus.js
			</select> -->
	</div>
</template>
<script>
export default {
	name: 'ProfileInfo',
	props: {
		data: Object
	},
	data: function () {
		return {
			loading: false,
			workCharts: null
		}
	},
	mounted() {
		this.axios.get('/work-chart').then(res => {
			this.workCharts = res.data.data;
		}).catch(err => {
			console.log(err);
		})
	},
	computed: {
		hour() {
			return this.$moment.utc(this.$moment.duration(this.workChartUser.end_time) - this.$moment.duration(this.workChartUser.start_time)).format('H');
		},
		workChartUser() {
			return this.data.user && this.workCharts ? this.workCharts.find(w => w.id === this.data.user.work_chart_id) : null;
		},
		fullName(){
			return this.data && this.data.user ? `${this.data.user.name} ${this.data.user.last_name || ''}` : '';
		},
		isAdmin(){
			return this.$store.state.user.user.is_admin === 1;
		},
		moreThanDays(){
			if(this.data){
				const admission = this.$moment(this.data.user.created_at, 'YYYY-MM-DD');
				const discharge = this.$moment(new Date(), 'YYY-MM-DD');
				const days = discharge.diff(admission, 'days');
				return days <= 5;
			} else {
				return false;
			}
		},
	}
}
</script>
