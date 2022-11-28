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
        <b-button @click="modalClick" variant="dark">Генерировать</b-button>
        <div class="draggable-container" v-if="modaltest">
            <div>
                <div class="draggable-block" id="draggable-block">
                    <!--                <div class="draggable" :style="[{'color': styles.fullName.color}, {'font-size': style.fullName.size}, {'text-transform': style.fullName.uppercase}, transformFullName]">{{img.name}}</div>-->
                    <div class="draggable"
                         style="margin-top: 40px; position: absolute; top: 0; left: 0; z-index: 12; text-align: center; display: inline-block;"
                         :style="{color: styles.fullName.color, fontSize: styles.fullName.size + 'px', fontWeight: styles.fullName.fontWeight, textTransform: styles.fullName.uppercase, display: styles.fullName.fullWidth ? 'block' : 'inline-block', width: styles.fullName.fullWidth ? '100%' : 'auto', transform: transformFullName}"
                    >{{img.name}}
                    </div>
                    <div class="draggable"
                         style="margin-top: 120px; position: absolute; top: 0; left: 0; z-index: 12; text-align: center; display: inline-block;"
                         :style="{color: styles.courseName.color, fontSize: styles.courseName.size + 'px', fontWeight: styles.courseName.fontWeight, textTransform: styles.courseName.uppercase, display: styles.courseName.fullWidth ? 'block' : 'inline-block', width: styles.courseName.fullWidth ? '100%' : 'auto', transform: transformCourseName}"
                    >{{img.certificate}}
                    </div>
                    <div class="draggable"
                         style="margin-top: 200px; position: absolute; top: 0; left: 0; z-index: 12; text-align: center; display: inline-block;"
                         :style="{color: styles.hours.color, fontSize: styles.hours.size + 'px', fontWeight: styles.hours.fontWeight, textTransform: styles.hours.uppercase, display: styles.hours.fullWidth ? 'block' : 'inline-block', width: styles.hours.fullWidth ? '100%' : 'auto', transform: transformHoursName}"
                    >{{img.date}}
                    </div>
                    <div class="draggable"
                         style="margin-top: 280px; position: absolute; top: 0; left: 0; z-index: 12; text-align: center; display: inline-block;"
                         :style="{color: styles.date.color, fontSize: styles.date.size + 'px', fontWeight: styles.date.fontWeight, textTransform: styles.date.uppercase, display: styles.date.fullWidth ? 'block' : 'inline-block', width: styles.date.fullWidth ? '100%' : 'auto', transform: transformDateName}"
                    >{{img.time}}
                    </div>
                    <vue-pdf-embed v-if="imgToModal" :source="imgToModal"/>
                </div>
                <b-button variant="primary" @click="exportToPDF">Export to PDF</b-button>
            </div>
        </div>
    </div>
</template>

<script>
    import EditAwardSidebar from "./EditAwardSidebar(delete).vue";
    import VuePdfEmbed from "vue-pdf-embed/dist/vue2-pdf-embed";
    import html2pdf from "html2pdf.js";

    export default {
        name: "Awards",
        components: {
            EditAwardSidebar,
            VuePdfEmbed
        },
        data() {
            return {
                img: {
                    name: 'Хайруллин Тимур',
                    certificate: 'За лучшие заслуги лучших',
                    date: '24.11.2022',
                    time: 'Пройдено за 50 часа(ов) вместе с домашними заданиями'
                },
                transformFullName: {},
                transformCourseName: {},
                transformHoursName: {},
                transformDateName: {},
                styles: {},
                imgToModal: '',
                modaltest: false,
                modal: false,
                itemRemove: null,
                showEditAwardSidebar: false,
                item: false,
                tableItems: [],
            };
        },
        mounted() {
            this.getAwards();
        },
        computed: {
            tableData() {
                return this.tableItems;
            },
        },
        methods: {
            exportToPDF() {
                html2pdf(document.getElementById('draggable-block'), {
                    filename: "i-was-html.pdf",
                });
            },
            modalClick() {
                this.imgToModal = this.tableItems[5].path;

                this.styles = JSON.parse(JSON.parse(this.tableItems[5].styles).replace(/\\"/g, '\''));
                this.transformFullName = `translate(${this.styles.fullName.screenX}px, ${this.styles.fullName.screenY}px)`;
                this.transformCourseName = `translate(${this.styles.courseName.screenX}px, ${this.styles.courseName.screenY}px)`;
                this.transformHoursName = `translate(${this.styles.hours.screenX}px, ${this.styles.hours.screenY}px)`;
                this.transformDateName = `translate(${this.styles.date.screenX}px, ${this.styles.date.screenY}px)`;
                console.log(this.styles);
                this.modaltest = !this.modaltest;
            },
            modalShow(item) {
                this.itemRemove = item;
                this.modal = !this.modal;
            },
            async getAwards() {
                let loader = this.$loading.show();
                this.tableItems = [];
                this.axios
                    .get("/awards/get")
                    .then(response => {
                        const data = response.data.data;
                        for (let i = 0; i < data.length; i++) {
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
                    .then(response => {
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
        .draggable-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 11;
            width: 1000px;
            height: calc(100vh - 200px);
            overflow: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #333;

            .draggable-block {
                position: relative;
                max-width: 700px;
                overflow: auto;
                max-height: calc(100vh - 200px);

                canvas {
                    width: 600px !important;
                    height: auto !important;
                }
            }
        }

        .draggable {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 12;
            text-align: center;
            display: inline-block;
        }

        #awards-table {
            tbody {
                cursor: pointer;
            }
        }
    }
</style>