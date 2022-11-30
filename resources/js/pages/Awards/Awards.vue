<template>
    <div id="awards-page">
        <BButton variant="success" class="mb-2" @click="addAwardButtonClickHandler"
        >Создать награду
        </BButton
        >
        <BTableSimple
                id="awards-table"
                class="mb-3"
                bordered
                hover
                small
                @row-clicked="rowClickedHandler"
                v-if="tableItems.length > 0"
                :hover="false"
        >
            <BThead>
                <BTr>
                    <BTh>№</BTh>
                    <BTh>Название</BTh>
                    <BTh>Описание</BTh>
                    <BTh>Тип</BTh>
                    <BTh>Дата создания</BTh>
                    <BTh>Постановщик</BTh>
                </BTr>
            </BThead>
            <BTbody>
                <BTr v-for="(item, key) in tableItems" :key="item.name + key">
                    <BTd>{{ key + 1 }}</BTd>
                    <BTd><div class="clickable" @click="rowClickedHandler(item)">{{ item.name }}</div></BTd>
                    <BTd class="td-desc"><div class="desc">{{ item.description }}</div></BTd>
                    <BTd v-if="item.type === 1">Картинка</BTd>
                    <BTd v-if="item.type === 2">Конструктор</BTd>
                    <BTd v-if="item.type === 3">Данные начислений</BTd>
                    <BTd>{{ item.created_at | splitDate(item.created_at) }}</BTd>
                    <BTd>{{ item.creator.name }} {{ item.creator.last_name }}</BTd>
                    <BTd @click.stop>
                        <bButton size="sm" pill variant="danger" @click="modalShow(item)"><i class="fa fa-trash"></i>
                        </bButton>
                    </BTd>
                </BTr>
            </BTbody>
        </BTableSimple>

        <div v-else>
            <hr>
            <h4>Пока нет ни одного сертификата.</h4>
        </div>
        <!--        <Draggable/>-->

        <EditAwardSidebar
                v-if="showEditAwardSidebar"
                :open.sync="showEditAwardSidebar"
                :item="item"
                @save-award="saveAward"
                @update-award="updateTable"
        />
        <b-modal v-if="itemRemove" centered v-model="modal" :title="itemRemove.name">
            Вы уверены, что хотите удалить награду?
            <template #modal-footer>
                <BButton variant="danger" @click="remove(itemRemove)">Удалить</BButton>
                <BButton variant="light" @click="modal = !modal">Отмена</BButton>
            </template>
        </b-modal>
    </div>
</template>

<script>
    import EditAwardSidebar from "./EditAwardSidebar.vue";

    export default {
        name: "Awards",
        components: {
            EditAwardSidebar
        },
        data() {
            return {
                modal: false,
                itemRemove: null,
                showEditAwardSidebar: false,
                item: null,
                tableItems: [],
            };
        },
        filters: {
            splitDate: function(val){
                return val.split('T')[0];
            }
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
                    .get("/award-categories/get")
                    .then(response => {
                        this.tableItems = response.data.data;
                        loader.hide();
                    })
                    .catch(function (error) {
                        console.log(error);
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
                    .delete("/award-categories/delete/" + item.id)
                    .then(response => {
                        this.getAwards();
                        loader.hide();
                    })
                    .catch(function (error) {
                        console.log(error);
                        loader.hide();
                    });
            },
        }
    };
</script>

<style lang="scss">
    #awards-page {
        #awards-table {
            thead{
                white-space: nowrap;
            }
            tbody {
                tr{
                    cursor: default;
                }
                cursor: pointer;
                .td-desc{
                    max-width: calc(100vw - 1000px);
                }
                .desc{
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                    padding: 5px 0;
                    text-align: left;
                }
                .clickable{
                    cursor: pointer;
                    height: 35px;
                    display: inline-flex;
                    align-items: center;
                    &:hover{
                        color: #ed2353;
                    }
                }
            }
        }
    }
</style>