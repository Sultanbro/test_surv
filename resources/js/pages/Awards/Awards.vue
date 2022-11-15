<template>
    <div id="awards-page">
        <BButton variant="success" class="mb-2" @click="addAwardButtonClickHandler"
        >Создать награду
        </BButton
        >
        <BButton variant="success" class="mb-2" @click="loglog"
        >Консоль
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
                    <BTd>{{ item.date }}</BTd>
                    <BTd>{{ item.user }}</BTd>
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

        <EditAwardSidebar
                v-if="showEditAwardSidebar"
                :open.sync="showEditAwardSidebar"
                :item="item"
                @save-award="addTable"
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
                returnObjectValues: {
                    id: 2,
                    name: "New Sertificate",
                    description: "lorem ipsum dolor",
                    awardTypeId: 2,
                    format: "png",
                    path: "upload/sertificates/SL_012519_18110_94.jpg",
                    visibleToOthers: false,
                    user: "Адиль Каримов",
                    date: "06.10.2022",
                },
                tableItems: [],
            };
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
            addTable(data) {
                this.tableItems.push(data);
            },
            updateTable(data) {
                this.tableItems.map(el => {
                    if (el.id === data.id) {
                        el.id = data.id;
                        el.name = data.name;
                        el.description = data.description;
                        el.awardTypeId = data.awardTypeId;
                        el.format = data.format;
                        el.fileType = data.fileType;
                        el.image = data.image;
                        el.imageData = data.imageData;
                        el.path = data.path;
                        el.visibleToOthers = data.visibleToOthers;
                        el.user = data.user;
                        el.date = data.date;
                    }
                });
            },
            remove(item) {
                this.tableItems = this.tableItems.filter(i => i.id !== item.id);
            },
            loglog(){
                console.log(this.tableItems);
            }
        },
    };
</script>

<style lang="scss">
    .chat.noscrollbar {
        display: none !important;
    }

    #awards-page {
        #awards-table {
            tbody {
                cursor: pointer;
            }
        }
    }
</style>
