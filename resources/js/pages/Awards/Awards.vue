<template>
	<div id="awards-page">
		<template v-if="canEdit">
			<BButton
				variant="success"
				class="mb-2"
				@click="addAwardButtonClickHandler"
			>
				Создать награду
			</BButton>
			<img
				v-b-popover.hover.right="'Нематериальная мотивация в виде сертификатов, грамот, отметок, которые будут доступны в профиле сотрудника'"
				src="/images/dist/profit-info.svg"
				class="img-info"
			>
		</template>

		<div
			v-if="tableItems && tableItems.length > 0"
			class="table-container"
		>
			<BTableSimple
				id="awards-table"
				striped
				:hover="false"
				@row-clicked="rowClickedHandler"
			>
				<BThead>
					<BTr>
						<BTh>№</BTh>
						<BTh>Название</BTh>
						<BTh class="text-left">
							Описание
						</BTh>
						<BTh>Тип</BTh>
						<BTh>Дата создания</BTh>
						<BTh>Постановщик</BTh>
						<BTh v-if="canEdit" />
					</BTr>
				</BThead>
				<BTbody>
					<BTr
						v-for="(tableItem, key) in tableItems"
						:key="tableItem.name + key"
					>
						<BTd>{{ key + 1 }}</BTd>
						<BTd>
							<div
								class="clickable"
								@click="rowClickedHandler(tableItem)"
							>
								{{ tableItem.name }}
							</div>
						</BTd>
						<BTd class="td-desc">
							<div class="desc">
								{{ tableItem.description }}
							</div>
							<div class="full-text">
								{{ tableItem.description }}
							</div>
						</BTd>
						<BTd>
							{{ typeNamesList[tableItem.type] }}
						</BTd>
						<BTd>{{ tableItem.created_at | splitDate(tableItem.created_at) }}</BTd>
						<BTd>{{ tableItem.creator.name }} {{ tableItem.creator.last_name }}</BTd>
						<BTd
							v-if="canEdit"
							@click.stop
						>
							<b-button
								class="btn btn-danger btn-icon"
								@click="modalShow(tableItem)"
							>
								<i class="fa fa-trash" />
							</b-button>
						</BTd>
					</BTr>
				</BTbody>
			</BTableSimple>
		</div>
		<div v-else>
			<hr class="my-4">
			<h4 class="no-awards-title">
				Пока нет ни одного сертификата
			</h4>
		</div>

		<EditAwardSidebar
			v-if="showEditAwardSidebar"
			:open.sync="showEditAwardSidebar"
			:item="item"
			@save-award="saveAward"
			@update-award="updateTable"
		/>
		<b-modal
			v-if="itemRemove"
			v-model="modal"
			centered
			:title="itemRemove.name"
		>
			Вы уверены, что хотите удалить награду?
			<template #modal-footer>
				<BButton
					variant="danger"
					@click="remove(itemRemove)"
				>
					Удалить
				</BButton>
				<BButton
					variant="light"
					@click="modal = !modal"
				>
					Отмена
				</BButton>
			</template>
		</b-modal>
	</div>
</template>

<script>
import EditAwardSidebar from './EditAwardSidebar.vue';
import {typeNamesList} from './helper'
// import RegenerateCertificates from "./types/RegenerateCertificates";

export default {
	name: 'SidebarAwards',
	components: {
		EditAwardSidebar,
	},
	filters: {
		splitDate: function(val){
			return val.split('T')[0]
		}
	},
	data() {
		return {
			modalRegenerate: false,
			modal: false,
			itemRemove: null,
			showEditAwardSidebar: false,
			item: null,
			tableItems: [],
			typeNamesList,
			canEdit: this.$can('awards_edit'),
		}
	},
	mounted() {
		this.getAwards()
	},
	methods: {
		modalShow(item) {
			this.itemRemove = item
			this.modal = !this.modal
		},
		async getAwards() {
			const loader = this.$loading.show()
			this.tableItems = []
			try {
				const {data} = await this.axios.get('/award-categories/get')
				this.tableItems = data.data
			}
			catch (error) {
				console.error(error)
			}
			loader.hide()
		},
		rowClickedHandler(data) {
			if(!this.canEdit) return
			this.showEditAwardSidebar = true
			this.item = data
		},
		addAwardButtonClickHandler() {
			if(!this.canEdit) return
			this.showEditAwardSidebar = true
			this.item = {}
		},
		saveAward() {
			this.getAwards()
		},
		updateTable() {
			this.getAwards()
		},
		async remove(item) {
			this.modal = !this.modal
			const loader = this.$loading.show()
			try {
				await this.axios.delete('/award-categories/delete/' + item.id)
				this.getAwards()
			}
			catch (error) {
				console.error(error)
			}
			loader.hide()
		},
	}
};
</script>

<style lang="scss">
#awards-page {
	.img-info{
		vertical-align: middle;
	}
	.no-awards-title{
		font-size: 20px;
		font-weight: 500;
		color: #999;
		text-transform: uppercase;
	}
}
#awards-table {
	thead{
		white-space: nowrap;
	}
	.td-desc{
		max-width: calc(100vw - 1000px);
		position: relative;
		.full-text{
			position: absolute;
			top: 20px;
			left: 10px;
			max-width: 400px;
			visibility: hidden;
			opacity: 0;
			padding: 10px 20px;
			background-color: #fff;
			font-size: 14px;
			border: 1px solid #999;
			line-height: 1.3;
			text-align: left;
			border-radius: 10px;
			box-shadow: rgb(0 0 0 / 10%) 0px 10px 15px -3px, rgb(0 0 0 / 5%) 0px 4px 6px -2px;
			transition: 0.2s all ease;
		}
		&:hover{
			.full-text{
				visibility: visible;
				opacity: 1;
				top: 40px;
				z-index: 11;
			}
		}
	}
	.desc{
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		padding: 5px 10px;
		text-align: left;
	}
	.clickable{
		cursor: pointer;
		height: 35px;
		padding: 0 15px;
		border-radius: 50px;
		display: flex;
		align-items: center;
		justify-content: center;
		transition: 0.15s all ease;
		&:hover{
			background-color: rgba(0,0,0,0.1);
		}
	}
}
#edit-award-sidebar{
	.ui-sidebar__body{
		transform: translateX(100%);
	}
	&.show{
		.ui-sidebar__body{
			transform: translateX(0);
		}
	}
}
</style>
