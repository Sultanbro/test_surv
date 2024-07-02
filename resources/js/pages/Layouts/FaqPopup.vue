<template>
	<div class="popup__con faq-con">
		<div class="faq-list">
			<div class="faq-search">
				<b-form-input
					v-model="search"
					@input="onSearch"
				/>
				<i class="fa fa-search" />
			</div>
			<FaqSearch
				v-if="search"
				:active="active"
				:items="itemsSearch"
				@select="onSelect"
			/>
			<FaqList
				v-else
				:active="active"
				:list="items"
				@select="onSelect"
			/>
		</div>
		<FaqContent
			:active="active"
			:search="search"
		/>
	</div>
</template>

<script>
import FaqList from './faq/FaqList';
import FaqContent from './faq/FaqContent';
import FaqSearch from './faq/FaqSearch';

const divider = '___';

export default {
	name: 'FaqPopup',
	components: {
		FaqList,
		FaqContent,
		FaqSearch,
	},
	props: {},
	data: function () {
		return {
			search: '',
			seachTimeout: null,
			searchResult: [],

			active: null,
			items: [],
		};
	},
	computed: {
		itemsFlat() {
			return this.getItems(this.items, []);
		},
		itemsSearch() {
			return this.itemsFlat.filter((item) =>
				this.searchResult.includes(item.id)
			);
		},
	},
	created() {
		this.fetchFAQ();
	},
	mounted() {},
	methods: {
		getItems(items, result) {
			items.forEach((item) => {
				result.push(item);
				if (item.children?.length) this.getItems(item.children, result);
			});
			return result;
		},
		async fetchFAQ() {
			try {
				const { data } = await this.axios.get('/profile/faq');
				this.items = data.data;
				const path = location.pathname;
				const dialog = '';
				const item = this.itemsFlat.find((item) => {
					const [itemPath, itemDialog] = item.page.split(divider);
					return path === itemPath && (itemDialog || '') === dialog;
				});
				if (item) this.onSelect(item);
			} catch (error) {
				this.$onError({ error });
			}
		},
		async onSelect(item) {
			try {
				const { data } = await this.axios.get(`/profile/faq/get/${item.id}`);
				this.active = data.data;
			} catch (error) {
				this.$onError({ error });
			}
		},
		async seachFAQ() {
			if (!this.search) return;

			const { data } = await this.axios.get('/profile/faq/search', {
				params: { query: this.search },
			});

			this.searchResult = data.data;
		},
		onSearch() {
			clearTimeout(this.seachTimeout);
			this.seachTimeout = setTimeout(this.seachFAQ, 750);
		},
	},
};
</script>

<style lang="scss">
.faq-con {
	margin: 0 -40px;
	display: flex;
	border-top: 1px solid #ddd;
	.faq-search {
		padding: 16px 20px 17px 0;
		margin-bottom: 20px;
		border-bottom: 1px solid #ddd;
		position: sticky;
		top: 0;
		background-color: #ecf0f9;
		i {
			position: absolute;
			top: 25px;
			right: 35px;
			z-index: 1;
			font-size: 16px;
			color: #999;
		}
	}
}
</style>
