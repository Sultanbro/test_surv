<template>
    <div id="awards-page">
        <BButton variant="success" class="mb-2" @click="addAwardButtonClickHandler"
        >Создать награду
        </BButton
        >
        <BButton variant="success" class="mb-2" @click="testest"
        >Тест отправка
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
                tableItems: [],
                award:{
                    awardTypeId: 1,
                    courseIds: 1,
                    name: 'lorem',
                    description: 'lorem ipsum dolor',
                    hide: true,
                    file: '/upload/sertificates/SL_012519_18110_94.jpg',
                    styles: {
                        fullName: {
                            screenX: 0,
                            screenY: 0,
                            text: 'Иван Иванович Иванов',
                            size: 32,
                            fontWeight: 700,
                            uppercase: 'lowercase',
                            fullWidth: false,
                            color: '#000000'
                        },
                        courseName: {
                            screenX: 0,
                            screenY: 0,
                            text: 'Название курса',
                            size: 20,
                            fontWeight: 400,
                            uppercase: 'lowercase',
                            fullWidth: false,
                            color: '#000000'
                        },
                        hours: {
                            screenX: 0,
                            screenY: 0,
                            text: 'Название курса',
                            size: 16,
                            fontWeight: 400,
                            uppercase: 'lowercase',
                            fullWidth: false,
                            color: '#000000'
                        },
                    }
                },
                awardType: {
                    name: 'afsafsfsaf',
                    description: 'asdfadsddad asdg asdgasg asd asd gasd gdssaf'
                }
            };
        },
        methods: {
            testest(){
                this.axios
                    .post("/awards/store", {
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        data: JSON.stringify(this.award)
                    })
                    .then(function (response) {
                        console.log(response);
                    })
                    .catch(function (error) {
                        console.log("error");
                        console.log(error);
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
                        el.images = data.images;
                        el.imagesData = data.imagesData;
                        el.visibleToOthers = data.visibleToOthers;
                        el.awardCreator = data.awardCreator;
                        el.date = data.date;
                    }
                });
            },
            async remove(item) {
                let loader = this.$loading.show();
                await this.removeFiles(item);
                this.tableItems = this.tableItems.filter(i => i.id !== item.id);
                loader.hide();
            },
            async removeFiles(item) {
                await this.axios
                    .post("/delete.php", {
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        data: JSON.stringify(item.images)
                    })
                    .then(function (response) {
                        console.log('deleted');
                    })
                    .catch(function (error) {
                        console.log("error");
                        console.log(error);
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
