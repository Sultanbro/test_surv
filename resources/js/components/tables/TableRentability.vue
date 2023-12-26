<template>
	<div
		:key="skey"
		class="mb-3"
	>
		<RentabilityGauges
			v-if="activeRentability.length"
			:key="skey"
			:items="activeRentability"
			class="mb-5"
			@save="saveRenabilityGaguge"
		/>

		<div class="table-container">
			<table class="table table-bordered table-responsive whitespace-no-wrap custom-table-rentability">
				<thead>
					<tr>
						<th class="b-table-sticky-column" />
						<th />

						<template v-for="(m, key) in months">
							<th
								:key="key"
								colspan="2"
								class="text-center"
							>
								{{ m }}
							</th>
							<th
								:key="key + 'a'"
								class="br1 text-center"
							>
								{{ tops[key] }}
							</th>
						</template>
					</tr>

					<tr>
						<th class="b-table-sticky-column">
							<div class="d-flex align-items-center">
								<p
									class="mb-0 fz-12"
									@click="sort('name')"
								>
									Название <i class="fa fa-sort ml-1" />
								</p>
							</div>
						</th>
						<th>
							<div class="d-flex align-items-center">
								<p
									class="mb-0 fz-12"
									@click="sort('date')"
								>
									Дата <i class="fa fa-sort ml-1" />
								</p>
							</div>
						</th>
						<template v-for="i in 12">
							<th
								:key="i"
								class="font-bold text-center  bb1"
								@click="sort('l' + i)"
							>
								<div class="d-flex align-items-center">
									выручка <i class="fa fa-sort ml-1" />
								</div>
							</th>
							<th
								:key="i + 'a'"
								class="font-bold text-center bb1"
								@click="sort('c' + i)"
							>
								<div class="d-flex align-items-center">
									ФОТ <i class="fa fa-sort ml-1" />
								</div>
							</th>
							<th
								:key="i + 'b'"
								class="font-bold text-center br1 bb1"
								@click="sort('r' + i)"
							>
								<div class="d-flex align-items-center">
									Маржа <i class="fa fa-sort ml-1" />
								</div>
							</th>
						</template>
					</tr>
				</thead>
				<tbody>
					<tr
						v-for="(item, index) in items"
						:key="index"
					>
						<td class="table-primary b-table-sticky-column text-left px-2 t-name wdf d-flex aic">
							<div>{{ item.name }}</div>
							<i
								v-if="item.archived_date"
								v-b-popover.hover.right.html="'Аналитика архвирована ' + $moment(item.archived_date, 'YYYY-MM-DD').format('DD.MM.YYYY')"
								class="fa fa-info-circle"
							/>
						</td>

						<td class="text-center">
							<div>{{ item.date_formatted }}</div>
						</td>

						<template v-for="i in 12">
							<td
								:key="i"
								class="text-center"
								:class="{'p-0': index != 0}"
							>
								<input
									v-if="index != 0"
									v-model="item['l' + i]"
									class="input"
									:class="{'edited':item['ed' + i]}"
									type="number"
									@change="update(i, index)"
								>
								<div v-else>
									{{ item['l' + i] }}
								</div>
							</td>
							<td
								:key="i + 'a'"
								class="text-center"
							>
								{{ numberWithCommas( item['c' + i] ) }}
							</td>
							<td
								:key="i + 'b'"
								class="text-center br1"
								:class="{
									'c-red text-white': item['rc' + i] < 20 && item['rc' + i] != '',
									'c-orange': item['rc' + i] >= 20 && item['rc' + i] < 50,
									'c-yellow': item['rc' + i] >= 50 && item['rc' + i] < 75,
									'c-green text-white': item['rc' + i] >= 75,
								}"
							>
								{{ item['r' + i] }}
							</td>
						</template>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>

<script>
import { fetchRentabilityV2 } from '@/stores/api/analytics.js'
import { bus } from '@/bus'

const RentabilityGauges = () => import(/* webpackChunkName: "RentabilityGauges" */ '@/components/pages/Top/RentabilityGauges')  // TOП спидометры, есть и в аналитике

export default {
	name: 'TableRentability',
	components: {
		RentabilityGauges,
	},
	props: {
		year: {
			type: Number,
			default: 0
		},
		month: {
			type: Number,
			default: 0
		},
		rentabilitySwitch: {
			type: Object,
			default: () => ({}),
		},
	},
	data() {
		return {
			items: [],
			months: {
				1: 'Январь',
				2: 'Февраль',
				3: 'Март',
				4: 'Апрель',
				5: 'Май',
				6: 'Июнь',
				7: 'Июль',
				8: 'Август',
				9: 'Сентябрь',
				10: 'Октябрь',
				11: 'Ноябрь',
				12: 'Декабрь',
			},
			tops: {},
			skey: 1,
			sorts: {},
			speedometers: [],
		};
	},
	computed: {
		activeRentability(){
			return this.speedometers.filter(rent => this.isActiveRentability(rent.group_id))
		},
	},
	watch: {
		year: function() {
			this.fetchData();
		},
		month: function() {
			this.fetchData();
		},
	},

	created() {
		this.fetchData();
	},

	mounted(){
		this.skey++;
		setTimeout(() => { this.skey++ }, 100)
		bus.$on('tt-top-update', this.fetchData)
	},

	beforeUnmount(){
		bus.$off('tt-top-update', this.fetchData)
	},

	methods: {

		countTop() {
			Object.keys(this.months).forEach(key => {
				let s = this.items[0]['c' + key];
				let a = (this.items[0]['l' + key] - s) / s * 100;
				this.tops[key] = isNaN(a) ? '' : Number(a).toFixed(1) + '%';
			});
		},

		countRents() {
			this.items.forEach(item => {
				for(let i = 1;i<=12;i++) {
					let l = item['l' + i];
					let c = item['c' + i];
					let a = (l- c) / l * 100;
					item['r' + i] = !isFinite(a)  ? '' : Number(a).toFixed(1) + '%';
					item['rc' + i] = !isFinite(a) ? 0 : Number(a);
				}
			});
		},

		async fetchData() {
			const loader = this.$loading.show()
			try {
				const {table, speedometers, staticRent} = await fetchRentabilityV2({
					year: this.year,
					month: this.month,
				})
				this.items = table
				this.speedometers = this.actualSpeedmeters(speedometers, staticRent)
				this.countRents();
				this.countTop();
				this.skey++
				setTimeout(() => { this.skey++ }, 1000)
			}
			catch (error) {
				console.error('[TableRentability.fetchData]', error)
			}
			loader.hide()
		},

		actualSpeedmeters(speedometers, staticRent){
			return staticRent.map(old => {
				const _new = speedometers.find(_new => _new.group_id === old.group_id)
				if(_new) {
					return {
						..._new,
						sections: Array.isArray(_new.sections) ? JSON.stringify(_new.sections) : _new.sections
					}
				}
				return {
					...old,
					sections: Array.isArray(old.sections) ? JSON.stringify(old.sections) : old.sections
				}
			})
		},

		update(month, index) {

			let item = this.items[index];

			this.axios
				.post('/timetracking/top/top_edited_value/update', {
					year: this.year,
					month: month,
					value: item['l' + month],
					/* eslint-disable-next-line camelcase */
					group_id: item.group_id,
				})
				.then(() => {
					let i = month;

					item['r' + i] = Number(item['c' + i]) > 0 ? Number(Number(item['l' + i]) * 1000 / Number(item['c' + i]) ).toFixed(1) : 0;
					item['rc' + i] = item['r' + i] + '%';

					item['ed' + i] = true;

					this.$toast.success('Сохранено');
				});
		},

		numberWithCommas(x) {
			return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
		},

		sort(field) {

			if(this.sorts[field] === undefined) {
				this.sorts[field] = 'asc';
			}

			let item = this.items[0];

			this.items.shift();
			if(this.sorts[field] === 'desc') {
				this.items.sort((a, b) => (a[field] > b[field]) ? 1 : -1);
				this.sorts[field] = 'asc';
			} else {
				this.items.sort((a, b) => (a[field] < b[field]) ? 1 : -1);
				this.sorts[field] = 'desc';
			}

			this.items.unshift(item);
		},

		async saveRenabilityGaguge(gauge){
			const loader = this.$loading.show()
			if(typeof gauge.options === 'string') gauge.options = JSON.parse(gauge.options)
			gauge = this.gaugeSectionsCrutch(gauge)
			try {
				await this.axios.post('/v2/analytics-page/rentability/speedometers', {
					gauge: {
						...gauge,
						reversed: false,
						date: `${this.year}-${this.month < 10 ? '0' + this.month : this.month}-01`,
					},
					type: 2,
				})
				this.$toast.success('Успешно сохранено')
				this.fetchData()
			}
			catch (error) {
				console.error('[TableRentability.saveRenabilityGaguge]', error)
				alert(error)
			}
			loader.hide()
		},

		isActiveRentability(groupId){
			return this.rentabilitySwitch[groupId] && this.rentabilitySwitch[groupId].value
		},

		gaugeSectionsCrutch(gauge){
			if(!gauge.options) gauge.options = {}
			if(!gauge.options.staticLabels) gauge.options.staticLabels = {}
			const sections = JSON.parse(gauge.sections)
			gauge.options.staticLabels.labels = sections
			gauge.options.staticZones = this.createZones(sections)
			return gauge
		},

		createZones(sections){
			switch(sections.length){
			case 2:
				return [
					{
						// green
						strokeStyle: '#30B32D',
						min: sections[0],
						max: sections[1],
					}
				]
			case 3:
				return [
					{
						// red
						strokeStyle: '#F03E3E',
						min: sections[0],
						max: sections[1],
					},
					{
						// green
						strokeStyle: '#30B32D',
						min: sections[1],
						max: sections[2],
					},
				]
			case 4:
				return [
					{
						// red
						strokeStyle: '#F03E3E',
						min: sections[0],
						max: sections[1],
					},
					{
						// yellow
						strokeStyle: '#FFDD00',
						min: sections[1],
						max: sections[2],
					},
					{
						// green
						strokeStyle: '#30B32D',
						min: sections[2],
						max: sections[3],
					},
				]
			case 5:
				return [
					{
						// red
						strokeStyle: '#F03E3E',
						min: sections[0],
						max: sections[1],
					},
					{
						// orange
						strokeStyle: '#fd7e14',
						min: sections[1],
						max: sections[2],
					},
					{
						// yellow
						strokeStyle: '#FFDD00',
						min: sections[2],
						max: sections[3],
					},
					{
						// green
						strokeStyle: '#30B32D',
						min: sections[3],
						max: sections[4],
					},
				]
			case 6:
				return [
					{
						// red
						strokeStyle: '#F03E3E',
						min: sections[0],
						max: sections[1],
					},
					{
						// orange
						strokeStyle: '#fd7e14',
						min: sections[1],
						max: sections[2],
					},
					{
						// yellow
						strokeStyle: '#FFDD00',
						min: sections[2],
						max: sections[3],
					},
					{
						// green light
						strokeStyle: '#fd7e14',
						min: sections[3],
						max: sections[4],
					},
					{
						// green
						strokeStyle: '#30B32D',
						min: sections[4],
						max: sections[5],
					},
				]
			case 7:
				return [
					{
						// red
						strokeStyle: '#F03E3E',
						min: sections[0],
						max: sections[1],
					},
					{
						// orange
						strokeStyle: '#fd7e14',
						min: sections[1],
						max: sections[2],
					},
					{
						// orange light
						strokeStyle: '#ffc107',
						min: sections[2],
						max: sections[3],
					},
					{
						// yellow
						strokeStyle: '#FFDD00',
						min: sections[3],
						max: sections[4],
					},
					{
						// green light
						strokeStyle: '#fd7e14',
						min: sections[4],
						max: sections[5],
					},
					{
						// green
						strokeStyle: '#30B32D',
						min: sections[5],
						max: sections[6],
					},
				]
			}
			return []
		}
	},
};
</script>
<style lang="scss" scoped>
    .custom-table-rentability{
        th,td {
            padding: 0 15px!important;
            height: 40px;
        }
    }
.br1 {
    border-right: 1px solid #bababa;
}
.bb1 {
    border-bottom: 1px solid #a7a7a7;
}
.c-red {background: rgb(247, 88, 88);}
.c-orange {background: rgb(255, 196, 85);}
.c-yellow {background: rgb(255, 255, 107);}
.c-green {background: rgb(86, 172, 86);}
.edited {background: rgb(239, 236, 130);}
.input{
        border: 0;
    margin: 0;
    width: 110px;
    padding: 5px 0px 5px 13px;
    text-align: center;
}
.fz-12 {
    font-size: 12px;
}
</style>
