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
                <BTr v-for="(item, key) in tableItems" :key="item.name + key" @click="rowClickedHandler(item)">
                    <BTd>{{ key + 1 }}</BTd>
                    <BTd>{{ item.name }}</BTd>
                    <BTd>{{ item.description }}</BTd>
                    <BTd v-if="item.awardTypeId === 1 || item.awardTypeId === 2">Картинка</BTd>
                    <BTd v-else>Данные начислений</BTd>
                    <BTd>{{ item.created_at }}</BTd>
                    <BTd>{{ item.awardCreator }}</BTd>
                    <BTd @click.stop>
                        <bButton size="sm" pill variant="danger" @click="remove(item)"><i class="fa fa-trash"></i>
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
                showEditAwardSidebar: false,
                item: false,
                tableItems: [],
            };
        },
        mounted(){
            let loader = this.$loading.show();
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
        methods: {
            rowClickedHandler(data) {
                this.showEditAwardSidebar = true;
                this.item = data;
            },
            addAwardButtonClickHandler() {
                this.showEditAwardSidebar = true;
                this.item = false;
            },
            async saveAward() {

            },
            updateTable(data) {
                this.tableItems.map(el => {
                    if (el.id === data.id) {
                        el.id = data.id;
                        el.name = data.name;
                        el.description = data.description;
                        el.awardTypeId = data.awardTypeId;
                        el.images = data.images;
                        el.imagesData = data.imagesData;
                        el.visibleToOthers = data.visibleToOthers;
                        el.awardCreator = data.awardCreator;
                        el.date = data.date;
                    }
                });
            },
             remove(item) {
                 console.log(item.id);
                 let loader = this.$loading.show();
                 this.axios
                     .delete("/awards/delete/" + item.id)
                     .then(response =>  {
                         console.log(response);
                         loader.hide();
                     })
                     .catch(function (error) {
                         console.log("error");
                         console.log(error);
                         loader.hide();
                     });
                loader.hide();
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