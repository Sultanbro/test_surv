<template>
	<div
		v-click-outside="onClickOutside"
		class="FilterStaffTurnover"
	>
		<JobtronButton
			small
			secondary
			@click="onClickButton"
		>
			<img
				src="/icon/news/filter/filter.svg"
				alt="фильтр"
				width="16"
			>
		</JobtronButton>
		<div
			v-if="isOpen"
			class="FilterStaffTurnover-filters"
		>
			<b-row class="mb-3">
				<b-col>
					<JobtronSelect
						v-model="localValue.position"
						:options="positionOptions"
						small
					/>
				</b-col>
			</b-row>
			<b-row class="mb-3">
				<b-col>
					<JobtronSelect
						v-model="localValue.formula"
						:options="formulaOptions"
						small
					/>
				</b-col>
			</b-row>
			<b-row>
				<b-col>
					<JobtronButton
						small
						@click="$emit('input', localValue)"
					>
						Применть
					</JobtronButton>
				</b-col>
			</b-row>
		</div>
	</div>
</template>

<script>
import JobtronButton from '@ui/Button.vue';
import JobtronSelect from '@ui/Select.vue';

import { mapGetters } from 'vuex'

export default {
	name: 'FilterStaffTurnover',
	components: {
		JobtronButton,
		JobtronSelect,
	},
	props: {
		value: {
			type: Object,
			required: true,
		}
	},
	data(){
		return {
			isOpen: false,
			touchpadfix: null,
			localValue: JSON.parse(JSON.stringify(this.value)),
			formulaOptions: [
				{
					value: 1,
					title: 'Формула 1',
				},
				{
					value: 2,
					title: 'Формула 2',
				},
				{
					value: 3,
					title: 'Формула 3',
				},
			],
		}
	},
	computed: {
		...mapGetters(['positions']),
		positionOptions(){
			return [
				{
					value: 0,
					title: 'Выберите должность',
					$disabled: true,
				},
				...this.positions.filter(pos => !pos.deleted_at).map(pos => ({
					value: pos.id,
					title: pos.position,
				})),
			]
		}
	},
	watch: {
		value: {
			handler(){
				this.localValue = JSON.parse(JSON.stringify(this.value))
			}
		},
	},
	created(){},
	mounted(){},
	methods: {
		onInput(){
			this.$emit('input', this.localValue)
		},
		onClickButton(){
			if(this.isOpen){
				this.isOpen = false
				return
			}
			this.touchpadfix = setTimeout(() => {
				this.isOpen = true
			}, 300)
		},
		onClickOutside(){
			this.isOpen = false
			if(this.touchpadfix) clearTimeout(this.touchpadfix)
		},
	},
}
</script>

<style lang="scss">
.FilterStaffTurnover{
	display: inline-flex;
	position: relative;
	&-filters{
		width: 500px;
		padding: 15px;
		margin-top: 4px;

		position: absolute;
		z-index: 100;
		top: 100%;

		background-color: #fff;
		border-radius: 12px;
		box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.15), 0px 15px 60px -40px rgba(45, 50, 90, 0.2);
	}
}
</style>
