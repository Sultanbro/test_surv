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
                <BTr v-for="(item, key) in tableData" :key="item.name + key" @click="rowClickedHandler(item)">
                    <BTd>{{ key + 1 }}</BTd>
                    <BTd>{{ item.name }}</BTd>
                    <BTd>{{ item.description }}</BTd>
                    <BTd v-if="item.award_type_id === 1">Картинка</BTd>
                    <BTd v-if="item.award_type_id === 2">Конструктор</BTd>
                    <BTd v-if="item.award_type_id === 3">Данные начислений</BTd>
                    <BTd>{{ item.created_at }}</BTd>
                    <BTd>{{ item.awardCreator }}</BTd>
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
    import EditAwardSidebar from "./EditAwardSidebar(delete).vue";

    export default {
        name: "Awards",
        components: {
            EditAwardSidebar,
        },
        data() {
            return {
                modal: false,
                itemRemove: null,
                showEditAwardSidebar: false,
                item: false,
                tableItems: [],
            };
        },
        mounted(){
            this.getAwards();
        },
        computed:{
            tableData() {
                return this.tableItems;
            },
        },
        methods: {
            modalShow(item){
                this.itemRemove = item;
                this.modal = !this.modal;
            },
            async getAwards(){
                let loader = this.$loading.show();
                this.tableItems = [];
                this.axios
                    .get("/awards/get")
                    .then(response => {
                        const data = response.data.data;
                        for(let i = 0; i < data.length; i++){
                            this.tableItems.push(data[i]);
                        }
                        loader.hide();
                    })
                    .catch(function (error) {
                        console.log("error");
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
                this.item = false;
            },
            saveAward(data) {
                // this.tableItems.push(data);
                this.getAwards();
            },
            updateTable() {
            this.getAwards();
            },
            async remove(item) {
                 this.modal = !this.modal;
                 let loader = this.$loading.show();
                await this.axios
                     .delete("/awards/delete/" + item.id)
                     .then(response =>  {
                         this.getAwards();
                         // for( let i = 0; i < this.tableItems.length; i++){
                         //     if ( this.tableItems[i].id === item.id) {
                         //         this.tableItems.splice(i, 1);
                         //     }
                         // }
                         loader.hide();
                     })
                     .catch(function (error) {
                         console.log("error");
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
            tbody {
                cursor: pointer;
            }
        }
    }
</style>