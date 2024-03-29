<template>
	<div
		class="popup__con faq-con"
	>
		<div class="faq-list">
			<div class="faq-search">
				<b-form-input />
				<i class="fa fa-search" />
			</div>
			<FaqList
				:active="active"
				:list="items"
				@select="onSelect"
			/>
		</div>
		<FaqContent :active="active" />
	</div>
</template>

<script>
import FaqList from './faq/FaqList';
import FaqContent from './faq/FaqContent';
export default {
	name: 'FaqPopup',
	components: {
		FaqList,
		FaqContent
	},
	props: {},
	data: function () {
		return {
			active: null,
			items: [],
		};
	},
	created() {
		this.fetchFAQ()
	},
	mounted(){},
	methods: {
		async fetchFAQ(){
			try {
				const {data} = await this.axios.get('/profile/faq')
				this.items = data.data
			}
			catch (error) {
				this.$onError({error})
			}
		},
		async onSelect(item){
			try {
				const {data} = await this.axios.get(`/profile/faq/get/${item.id}`)
				this.active = data.data
			}
			catch (error) {
				this.$onError({error})
			}
		},
	}
};
</script>

<style lang="scss">
	.faq-con {
		margin: 0 -40px;
		display: flex;
		border-top: 1px solid #ddd;
		.faq-search{
			padding: 16px 20px 17px 0;
			margin-bottom: 20px;
			border-bottom: 1px solid #ddd;
			position: sticky;
			top: 0;
			background-color: #ecf0f9;
			i{
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
