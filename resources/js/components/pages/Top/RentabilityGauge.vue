<template>
	<div
		v-click-outside="colseSettings"
		class="RentabilityGauge"
	>
		<div
			class="RentabilityGauge-gauge"
			@click="openSettings"
		>
			<VGauge
				:value="gauge.value"
				unit="%"
				:options="gauge.options"
				:max-value="Number(gauge.max_value)"
				:top="true"
				height="75px"
				width="125px"
				gauge-value-class="gauge-span"
			/>
		</div>
		<div class="RentabilityGauge-meta">
			<div class="RentabilityGauge-title">
				<a
					:href="'/timetracking/an?group='+ gauge.group_id + '&active=1&load=1'"
					target="_blank"
				>{{ gauge.name }}</a>
			</div>
			<div class="RentabilityGauge-value">
				{{ gauge.value }}%
			</div>
		</div>
		<div
			v-if="isSettings"
			class="RentabilityGauge-settings"
		>
			<template v-if="activities">
				<div class="d-flex justify-content-between align-items-center">
					<span class="pr-2 l-label">Min</span>
					<input
						v-model="gauge.min_value"
						type="text"
						class="form-control form-control-sm w-250 wiwi"
					>
				</div>
				<div class="d-flex justify-content-between align-items-center">
					<span class="pr-2 l-label">Max</span>
					<input
						v-model="gauge.max_value"
						type="text"
						class="form-control form-control-sm w-250 wiwi"
					>
				</div>
				<div class="d-flex justify-content-between align-items-center">
					<span class="pr-2 l-label">Сег</span>
					<input
						v-model="gauge.sections"
						type="text"
						class="form-control form-control-sm w-250 wiwi"
					>
				</div>
				<div class="d-flex justify-content-between align-items-center">
					<span class="pr-2 l-label">Акт</span>
					<select
						v-model="gauge.activity_id"
						class="form-control form-control-sm h-23"
					>
						<option
							:key="-1"
							:value="-1"
						>
							Ячейка из сводной
						</option>
						<option
							v-for="(activity, key) in activities"
							:key="key"
							:value="activity.id"
						>
							{{ activity.name }}
						</option>
					</select>
				</div>
				<div
					v-if="gauge.activity_id > 0"
					class="d-flex justify-content-between align-items-center"
				>
					<span class="pr-2 l-label">Тип</span>
					<select
						v-model="gauge.value_type"
						class="form-control form-control-sm h-23"
					>
						<option value="sum">
							Сумма выполненного
						</option>
						<option value="avg">
							Среднее значение
						</option>
					</select>
				</div>
				<div
					v-if="gauge.activity_id == -1"
					class="d-flex justify-content-between align-items-center mt-1"
				>
					<span class="pr-2">Ячейка</span>
					<input
						v-model="gauge.cell"
						type="text"
						class="form-control form-control-sm wiwi text-uppercase"
					>
				</div>
				<div class="d-flex mt-3">
					<button
						class="btn btn-primary rounded mt-1 mr-2"
						@click="$emit('save', gauge)"
					>
						Сохранить
					</button>
				</div>
			</template>
			<i
				v-else
				class="fa fa-spinner fa-pulse"
			/>
		</div>
	</div>
</template>

<script>
const VGauge = () => import(/* webpackChunkName: "VGauge" */ 'vgauge')
export default {
	name: 'RentabilityGauge',
	components: {
		VGauge
	},
	props: {
		item: {
			type: Object,
			required: true,
		},
	},
	data(){
		return {
			isSettings: false,
			gauge: JSON.parse(JSON.stringify(this.item)),
			activities: null,
		}
	},
	computed: {},
	watch: {
		item(){
			this.gauge = JSON.parse(JSON.stringify(this.item))
		}
	},
	created(){},
	mounted(){},
	methods: {
		openSettings(){
			this.isSettings = true
			if(!this.activities) this.fetchActivities()
		},
		colseSettings(){
			this.isSettings = false
		},
		async fetchActivities(){
			try {
				const {data} = await this.axios.post('/timetracking/top/get_activities', {
					/* eslint-disable-next-line camelcase */
					group_id: this.gauge.group_id,
				})
				this.activities = data
			}
			catch (error) {
				console.error('[RentabilityGauge.fetchActivities]', error)
			}
		},
	},
}
</script>

<style lang="scss">
.RentabilityGauge{
	position: relative;
	&-gauge{
		display: flex;
		justify-content: center;
	}
	&-meta{
		text-align: center;
		font-size: 14px;
		font-weight: 700;
	}
	// &-title{}
	// &-valie{}
	&-settings{
		width: 300px;
		padding: 10px;

		position: absolute;
		z-index: 10;
		top: 100%;

		background-color: #fff;
		box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.05), 0px 15px 60px -40px rgba(45, 50, 90, 0.2);
	}
}
</style>
