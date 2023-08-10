<template>
	<div id="awards-page">
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

		<!--        <BButton variant="danger" class="mb-2" @click="modalRegenerate = !modalRegenerate">Регенерация</BButton>-->
		<!--        <b-modal hide-footer no-close-on-backdrop no-close-on-esc no-enforce-focus v-model="modalRegenerate" size="lg" centered title="Принудительно обновление всех сертификатов по курсу">-->
		<!--            <RegenerateCertificates/>-->
		<!--        </b-modal>-->

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
						<BTh />
					</BTr>
				</BThead>
				<BTbody>
					<BTr
						v-for="(item, key) in tableItems"
						:key="item.name + key"
					>
						<BTd>{{ key + 1 }}</BTd>
						<BTd>
							<div
								class="clickable"
								@click="rowClickedHandler(item)"
							>
								{{ item.name }}
							</div>
						</BTd>
						<BTd class="td-desc">
							<div class="desc">
								{{ item.description }}
							</div>
							<div class="full-text">
								{{ item.description }}
							</div>
						</BTd>
						<BTd v-if="item.type === 1">
							Картинка
						</BTd>
						<BTd v-if="item.type === 2">
							Конструктор
						</BTd>
						<BTd v-if="item.type === 3">
							Данные начислений
						</BTd>
						<BTd>{{ item.created_at | splitDate(item.created_at) }}</BTd>
						<BTd>{{ item.creator.name }} {{ item.creator.last_name }}</BTd>
						<BTd @click.stop>
							<b-button
								class="btn btn-danger btn-icon"
								@click="modalShow(item)"
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
// import RegenerateCertificates from "./types/RegenerateCertificates";
export default {
	name: 'SidebarAwards',
	components: {
		EditAwardSidebar,
	},
	filters: {
		splitDate: function(val){
			return val.split('T')[0];
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
		};
	},
	mounted() {
		this.getAwards();
	},
	methods: {
		modalShow(item) {
			this.itemRemove = item;
			this.modal = !this.modal;
		},
		async getAwards() {
			let loader = this.$loading.show();
			this.tableItems = [];
			await this.axios
				.get('/award-categories/get')
				.then(response => {
					this.tableItems = response.data.data;
					loader.hide();
				})
				.catch(function (error) {
					console.error(error);
					loader.hide();
				});
		},
		rowClickedHandler(data) {
			this.showEditAwardSidebar = true;
			this.item = data;
		},
		addAwardButtonClickHandler() {
			this.showEditAwardSidebar = true;
			this.item = {};
		},
		saveAward() {
			this.getAwards();
		},
		updateTable() {
			this.getAwards();
		},
		async remove(item) {
			this.modal = !this.modal;
			let loader = this.$loading.show();
			await this.axios
				.delete('/award-categories/delete/' + item.id)
				.then(() => {
					this.getAwards();
					loader.hide();
				})
				.catch(function (error) {
					console.error(error);
					loader.hide();
				});
		},
	}
};
</script>

<style lang="scss">
    #awards-page {
			.img-info{
				vertical-align: middle;
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
        .no-awards-title{
            font-size: 20px;
            font-weight: 500;
            color: #999;
            text-transform: uppercase;
        }
        #awards-table {
            thead{
                white-space: nowrap;
            }
            tbody {
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
        }
    }
</style>
