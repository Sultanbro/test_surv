<template>
	<div
		class="popup__con faq-con"
		:class="{'v-loading': loading}"
	>
		<div class="faq-list">
			<div class="faq-search">
				<b-form-input />
				<i class="fa fa-search" />
			</div>
			<FaqList
				:list="data"
				:is-open="true"
				:active-item-id="itemId"
				@update-active-id="updateActiveId"
				@item-clicked="handleItemClick"
			/>
		</div>
		<FaqContent :item-content="itemContent" />
	</div>
</template>

<script>
import FaqList from './faq/FaqList';
import FaqContent from './faq/FaqContent';
export default {
	name: 'FaqPopup',
	props: {},
	components: {
		FaqList,
		FaqContent
	},
	data: function () {
		return {
			itemId: 1,
			itemContent: {
				id: 1,
				title: 'С чего начать?',
				contentId: 1001,
			},
			data: [
				{
					id: 1,
					title: 'С чего начать?',
					contentId: 1001,
				},
				{
					id: 2,
					title: 'Профиль',
					contentId: 1002,
					children: [
						{
							id: 21,
							title: 'Начало',
							contentId: 1003,
						},
						{
							id: 22,
							title: 'Показатели',
							contentId: 1004,
							children: [
								{
									id: 21,
									title: 'Еще что-то',
									contentId: 1008,
								},
								{
									id: 22,
									title: 'И еще чуть-чуть',
									contentId: 1009
								}
							]
						}
					]
				},
				{
					id: 3,
					title: 'Новости',
					contentId: 1005,
					children: [
						{
							id: 31,
							title: 'Новость',
							contentId: 1006,
						},
						{
							id: 32,
							title: 'Дни рождения',
							contentId: 1007
						}
					]
				},
			],
			loading: false
		};
	},
	created() {
		// this.fetchData()
	},
	methods: {
		updateActiveId(itemId) {
			this.itemId = itemId;
		},
		handleItemClick(item) {
			this.itemContent = item;
		}
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
		.faq-content{
			padding: 20px;
			height: calc(100vh - 130px);
			overflow: auto;
			&-title{
				font-size: 20px;
				font-weight: 700;
				text-align: center;
				padding-bottom: 20px;
				border-bottom: 1px solid #ddd;
			}
			&-body{
				font-size: 16px;
				line-height: 1.3;
			}
		}
		.faq-list {
			width: 350px;
			min-width: 350px;
			height: calc(100vh - 130px);
			overflow: auto;
			background-color: #ecf0f9;
			padding: 0 0 0 20px;
			&-content{
				display: none;
				&.opened{
					display: block;
				}
				&.nest{
					padding-left: 20px;
				}
			}
			&-item {
				margin-top: 6px;
				&-title {
					height: 40px;
					padding: 0 20px;
					display: flex;
					align-items: center;
					justify-content: space-between;
					cursor: pointer;
					font-size: 16px;
					border-radius: 20px 0 0 20px;
					i{
						color: #999;
						font-size: 12px;
					}

					&:hover {
						background-color: #e2e5ee;
					}
					&.active{
						background-color: rgba(96, 142, 233, 0.2);
						color: #333333;
						i{
							color: #fff;
						}
						&.parent{
							background-color: #608EE9;
							color: #fff;
						}
					}
				}
			}
		}
	}
</style>
