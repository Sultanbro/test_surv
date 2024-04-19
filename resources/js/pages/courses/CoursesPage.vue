<!-- eslint-disable -->
<template>
	<div class="section__course">
		<div class="section__course__table">
			<CourseFilter @search="searchInput" :positions="positions" :department="profile_groups" :type="type"
				:selectDepartment="selectDepartment" :selectPosition="selectPosition" :selectType="selectType"
				:selectSale="selectSale" :sale="sale" :author="author" @get-course="handleChange" />
			<CourseTable :items="items" @child-event="handleChange" />
		</div>


		<div class="section__course__footer">
			<span class="section__course__footer__total">Всего: {{ total }}</span>
			<div class="section__course__footer__select">
				<label for="">На странице </label>
				<select class="selectCount" v-model.number="itemPerPage" @change="handleChange">
					<option selected value="5">5</option>
					<option value="10">10</option>
					<option value="100">100</option>
					<option value="500">500</option>
					<option value="1000">1000</option>
				</select>
			</div>
		</div>
	</div>
</template>

<script>
/* eslint-disable*/
import CourseFilter from './components/CourseFilter.vue';
import CourseTable from './components/CourseTable.vue';
import { mapGetters } from 'vuex'

export default {
	components: {
		CourseFilter,
		CourseTable
	},
	data() {
		return {
			items: [],
			total: 0,
			itemPerPage: 5,
			search: '',
			type: [
				{ title: 'Тип курсы', value: null },
				{ title: 'автоматический', value: '1' },
				{ title: 'индивидуальный', value: '2' },
				{ title: 'купленный', value: '3' }
			],
			sale: [
				{ title: 'Продается', value: null },
				{ title: 'В продаже', value: '0' },
				{ title: 'Не продается', value: '1' }
			],
			selectDepartment: null,
			selectPosition: null,
			selectType: null,
			selectSale: null,
			author: null,

		}
	},
	mounted() {
		this.getCourse();
	},
	computed: {
		...mapGetters([
			'accessDictionaries',
		]),
		profile_groups() {
			if (!this.accessDictionaries.profile_groups) return [];
			const modifiedProfile_groups = this.accessDictionaries.profile_groups.map(item => ({
				title: item.name, // замена 'name' на 'title'
				value: item.id  // замена 'id' на 'value'
			}));

			const newItem = {
				title: "Назначен отделу",
				value: null
			};
			return [newItem, ...modifiedProfile_groups];
		},
		positions() {
			if (!this.accessDictionaries.positions) return [];
			const modifiedPositions = this.accessDictionaries.positions.map(item => ({
				title: item.name, // замена 'name' на 'title'
				value: item.id  // замена 'id' на 'value'
			}));

			const newItem = {
				title: "Назначен должности",
				value: null
			};
			return [newItem, ...modifiedPositions];
		}
	},

	methods: {
		searchInput(value) {
			this.search = value;
			this.getCourse()
		},
		async getCourse() {
			let params = new URLSearchParams(
				{
					'per_page': this.itemPerPage,
					'search': this.search
				}
			);
			const res = await this.axios.get(`/v2/courses/index?${params.toString()}`);
			this.items = res.data.data.data.map(item => ({
				...item,
				checked: false,
			}));
			this.total = res.data.data.total
		},
		handleChange() {
			this.getCourse()
		}
	},

};
/* eslint-enable */
</script>

<style lang="scss" scoped>
.section__course {
	height: calc(100vh - 80px);
	display: flex;
	flex-direction: column;
	justify-content: space-between;

	&__footer {
		display: flex;
		justify-content: space-between;

		&__total {
			font-family: "Inter", sans-serif;
			font-size: 16px;
			color: #8DA0C1;

		}

		&__select {
			font-family: "Inter", sans-serif;
			display: flex;
			align-items: center;

			label {
				margin: 0;
				color: #8DA0C1;
				margin-right: 10px;
				font-size: 16px;
			}

			select {
				border: 1px solid #8DA0C1;
				border-radius: 5px;
				color: #8DA0C1;
				font-size: 16px;

			}
		}
	}
}
</style>
