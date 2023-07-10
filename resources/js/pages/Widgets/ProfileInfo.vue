<template>
	<div
		class="ProfileInfo"
		:class="{
			'hidden': data.user === undefined || data.user === null,
			'v-loading': loading
		}"
	>
		<template v-if="data.user !== undefined && data.user !== null">
			<div class="ProfileInfo-name">
				{{ fullName }}
			</div>
			<div class="ProfileInfo-position ProfileInfo-border">
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
			<div class="ProfileInfo-group ProfileInfo-border py-2">
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
			<div class="ProfileInfo-salary ProfileInfo-border">
				ОКЛАД: {{ data.salary }} {{ currency }}
			</div>
			<div
				class="ProfileInfo-worktime"
				v-if="workChartUser"
			>
				<p class="ProfileInfo-border wsnw">
					{{ workChartUser.name }}
				</p>
				<p class="ProfileInfo-border wsnw">
					{{ workChartUser.start_time }} - {{ workChartUser.end_time }}
				</p>
				<p class="ProfileInfo-border wsnw">
					{{ hour }} часов
				</p>
			</div>
		</template>
	</div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useWorkChartStore } from '@/stores/WorkChart.js'
export default {
	name: 'ProfileInfo',
	props: {
		data: {
			type: Object,
			default: () => ({}),
		},
	},
	data: function () {
		return {
			loading: false,
		}
	},
	computed: {
		...mapState(useWorkChartStore, ['workChartList']),
		...mapState(useWorkChartStore, {isWorkChartLoading: 'isLoading'}),
		hour() {
			const $start = this.$moment.utc(this.workChartUser.start_time, 'HH: mm')
			const $end = this.$moment.utc(this.workChartUser.end_time, 'HH: mm')

			if($end > $start) return this.$moment.duration($end.diff($start)).asHours()
			const before = this.$moment.duration(this.$moment.utc('24:00', 'HH: mm').diff($start)).asHours()
			const after = this.$moment.duration($end.diff(this.$moment.utc('00:00', 'HH: mm'))).asHours()
			return before + after
		},
		workChartUser() {
			if(!this.workChartList) return null
			if(!this.data?.user) return null
			return this.workChartList.find(w => w.id === this.data.user.work_chart_id)
		},
		fullName(){
			if(!this.data?.user) return ''
			return `${this.data.user.name} ${this.data.user.last_name || ''}`
		},
		isAdmin(){
			return this.$store.state.user.user.is_admin === 1;
		},
		currency(){
			if(!this.data?.user) return ''
			return this.data.user.currency?.toUpperCase() || ''
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
	},
	mounted() {
		if(!this.workChartList && !this.isWorkChartLoading) this.fetchWorkChartList()
	},
	methods: {
		...mapActions(useWorkChartStore, ['fetchWorkChartList'])
	}
}
</script>

<style lang="scss">
.ProfileInfo{
	padding: 1.7rem 1rem;
	margin-top: 20px;

	position: relative;

	background: #fff;
	border-radius:1rem;

	.select-css{
		margin-top: 0;
		margin-bottom: 0;
	}
	&-name{
		margin-bottom: 1rem;

		color:#5B6166;
		font-size:1.8rem;
		font-weight: 600;
		text-align: center;
	}

	&-worktime{
		display: flex;
		justify-content: space-between;
		gap: 1rem;

		width: 100%;
		text-align: center;

		p{
			width: unset;
			padding: 0 1.2rem;
		}
	}
	&-border{
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 0 1.5rem;
		font-size:1.2rem;
		width: 100%;
		min-height: 4rem;
		color:#62788B;
		margin-bottom: 1rem;
		border: 1px solid #E7EAEA;
		border-radius:1rem;
		i{
			font-size: 12px;
			transition: 0.15s all ease;
			cursor: pointer;
			width: 30px;
			height: 30px;
			border-radius: 50%;
			text-align: center;
			color: rgb(72,145,243);
			line-height: 30px;
			&:hover{
				background-color: rgba(72,145,243, 0.2);
			}
		}
	}
}

.pulse-anim {
  animation: pulse-animation 2s infinite;
}

@keyframes pulse-animation {
  0% {
    box-shadow: 0 0 0 0px rgba(0, 0, 0, 0.2);
  }
  100% {
    box-shadow: 0 0 0 20px rgba(0, 0, 0, 0);
  }
}


@media(min-width:1360px){
	.ProfileInfo{
		transition: all 1s .6s;
		opacity:0;
		visibility: hidden;
		transform:translateY(10px);
	}
}
</style>
