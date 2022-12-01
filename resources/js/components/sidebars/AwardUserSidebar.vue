<template>
    <sidebar
            id="award-user-sidebar"
            title="Наградить пользователя"
            v-if="open"
            :open="open"
            @close="open = false"
            width="45%"
    >
        <b-button variant="primary" class="mx-auto d-block my-3" @click="openModalAdd">
            Загрузить файл награды
        </b-button>
        <p class="or">или</p>
        <p class="or2">Выберите один из шаблонов</p>
        <hr>
       <div class="available-container">
           <div v-for="award in awards" :key="award.name">
               <b-card :title="award.name"
                       border-variant="secondary"
                       header-border-variant="secondary"
               >
                   <b-row>
                       <b-col cols="12" md="3" class="mb-4" v-for="item in award.available" :key="item.id + item.format">
                           <div class="award-image" @click="openModalSelect(item, award.name)">
                               <img :src="item.path" alt="" v-if="item.format !== 'pdf'">
                               <vue-pdf-embed v-else :source="item.path"/>
                           </div>
                       </b-col>
                   </b-row>
               </b-card>
               <hr>
           </div>
       </div>
        <hr>
       <div class="my-container">
           <b-card title="Выданные награды"
                   border-variant="secondary"
                   header-border-variant="secondary"
           >
               <b-row v-if="myAwards.length > 0">
                   <b-col cols="12" md="3" class="mb-4" v-for="item in myAwards" :key="item.id + item.format">
                       <div class="award-image" @click="removeReward(item)">
                           <img :src="item.path" alt="" v-if="item.format !== 'pdf'">
                           <vue-pdf-embed :source="item.path" v-else/>
                           <i class="fa fa-trash"></i>
                       </div>
                   </b-col>
               </b-row>
               <div v-else>
                   <p class="title-not-rewards">Нет ни одной выданной награды</p>
               </div>
           </b-card>
       </div>

        <BModal v-model="modalRemoveReward" modal-class="remove-award-modal" title="Отмена награды"
                size="lg" centered>
            <h4 class="title-remove">Вы действительно хотите отозвать награду?</h4>
            <hr class="my-4">
            <div class="award-image-remove" v-if="modalRemoveRewardData">
                <img :src="modalRemoveRewardData.path" alt="" v-if="modalRemoveRewardData.format !== 'pdf'">
                <vue-pdf-embed :source="modalRemoveRewardData.path" v-else/>
            </div>
            <template #modal-footer>
                <b-button variant="secondary" @click="modalRemoveReward = !modalRemoveReward">Отмена</b-button>
                <b-button variant="danger" @click="removeRewardUser(modalRemoveRewardData)">Отозвать награду</b-button>
            </template>
        </BModal>


        <BModal v-model="modalAdd" modal-class="selected-modal" title="Добавление новой награды"
                size="lg" centered>
            <p class="selected-modal-title">Выберите вид награды, в которую будет добавлена картинка</p>
            <hr class="my-4">
           <b-row class="mb-4">
               <b-col cols="12" md="10" offset-md="1">
                   <Multiselect
                           v-model="value"
                           :options="awardCategories"
                           :multiple="false"
                           :close-on-select="true"
                           :clear-on-select="false"
                           :preserve-search="true"
                           placeholder="Выберите вид награды"
                           label="name"
                           track-by="name"
                           :preselect-first="false"

                   />
               </b-col>
           </b-row>
            <label for="file-add" class="custom-file-upload" ref="inputFileAdd"></label>
            <input type="file" accept="application/pdf, image/jpeg, image/png" id="file-add"
                   @change="modalAddEvent" style="display: none;">
            <div class="result-container" v-if="modalAddFile">
                <img :src="modalAddBase64" alt="" v-if="modalAddFile.type !== 'application/pdf'">
                <vue-pdf-embed v-else :source="modalAddBase64"/>
            </div>
            <template #modal-footer>
                <b-button variant="secondary" @click="modalAdd = !modalAdd">Отмена</b-button>
                <b-button variant="success" v-if="modalAddBase64 && value" @click="addAndSaveReward">Наградить</b-button>
            </template>
        </BModal>


        <BModal v-model="modalSelect" modal-class="selected-modal" :title="modalSelectData.name"
                size="lg" centered>
            <p class="selected-modal-title">Нажмите на картинку для скачивания. <br>Отредактируйте её и загрузите
                обратно</p>
            <hr class="my-4">
            <div class="download-image-container" @click="downloadItem(modalSelectData)" ref="downloadFile">
                <img :src="modalSelectData.path" alt="" v-if="modalSelectData.format !== 'pdf'">
                <vue-pdf-embed v-else :source="modalSelectData.path"/>
            </div>

            <label for="file" class="custom-file-upload" ref="inputFile" style="display: none;"></label>
            <input type="file" accept="application/pdf, image/jpeg, image/png" id="file"
                   @change="modalSelectDataUploadEvent" style="display: none;">

            <div class="result-container" v-if="modalSelectFile">
                <img :src="modalSelectBase64" alt="" v-if="modalSelectFile.type !== 'application/pdf'">
                <vue-pdf-embed v-else :source="modalSelectBase64"/>
            </div>
            <template #modal-footer>
                <b-button variant="secondary" @click="modalSelect = !modalSelect">Отмена</b-button>
                <b-button variant="success" v-if="modalSelectBase64" @click="reward">Наградить</b-button>
            </template>
        </BModal>
    </sidebar>
</template>

<script>
    const base64Encode = (data) =>
        new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(data);
            reader.onload = () => resolve(reader.result);
            reader.onerror = (error) => reject(error);
        });
    // import AwardsCard from '../profile/UserEarnings/AwardsCard.vue'
    import UploadModal from '../modals/Upload'
    import VuePdfEmbed from "vue-pdf-embed/dist/vue2-pdf-embed";
    import Multiselect from "vue-multiselect";

    export default {
        name: 'AwardUserSidebar',
        components: {UploadModal, VuePdfEmbed, Multiselect},
        data() {
            return {
                modalRemoveReward: false,
                modalRemoveRewardData: null,
                open: false,
                uploadModalOpen: false,
                userId: null,
                awards: [],
                modalSelect: false,
                modalSelectData: {},
                modalSelectFile: null,
                modalSelectBase64: null,
                myAwards: [],
                modalAdd: false,
                modalAddFile: null,
                modalAddBase64: null,
                awardCategories: [],
                value: null,
                responseAward: []

            }
        },
        mounted() {
            document.addEventListener('award-user-sidebar', (e) => {
                this.open = true;
                console.log('USER ID:', e.detail);
                this.userId = e.detail;
                this.getAll();
            });
        },
        watch: {
            modalAdd(val){
                if(!val){
                    this.value = null;
                    this.modalAddFile = null;
                    this.modalAddBase64 = null;
                }
                return this.modalAdd;
            }
        },
        methods: {
            async getAll(){
                let loader = this.$loading.show();
                await this.axios
                    .get('/awards/type?key=nomination&user_id=' + this.userId)
                    .then(response => {
                        this.awards = [];
                        this.myAwards = [];
                        this.awards = response.data.data;
                        for (let i = 0; i < this.awards.length; i++) {
                            if(this.awards[i].my.length > 0){
                                this.myAwards = this.myAwards.concat(this.awards[i].my);
                            }
                        }
                        loader.hide();
                    })
                    .catch(error => {
                        console.log(error);
                        loader.hide();
                    })
            },
            removeRewardUser(item){
                // const formDataRemove = new FormData();
                // formDataRemove.append('user_id', this.userId);
                // formDataRemove.append('award_id', data.id);
                this.axios
                .delete('/awards/reward-delete', {data: {user_id: this.userId, award_id: item.id}})
                .then(response => {
                    this.modalRemoveReward = false;
                    this.$toast.success('Награда убрана');
                    console.log(response);
                    this.getAll();
                })
                .catch(error => {
                    console.log(error);
                })
            },
            removeReward(item){
                this.modalRemoveReward = !this.modalRemoveReward;
                this.modalRemoveRewardData = item;
            },
            async addAndSaveReward(){
                const formData = new FormData();
                formData.append('award_category_id', this.value.id);
                formData.append('file[]', this.modalAddFile);
                await this.axios
                    .post("/awards/store", formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                    })
                    .then(response => {
                        this.responseAward = response.data.data;
                        this.$toast.success('Добавлено');
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

                const formDataReward = new FormData();
                formDataReward.append('user_id', this.userId);
                formDataReward.append('award_id', this.responseAward[0].id);
                formDataReward.append('file', this.modalAddFile);
                await this.axios
                    .post('/awards/reward', formDataReward, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    })
                    .then(response => {
                        console.log(response);
                        this.modalAdd = false;
                        this.$toast.success('Награжден');
                        setTimeout(function () {
                            this.modalAddFile = null;
                            this.modalAddBase64 = null;
                        }, 300)
                        this.getAll();
                    })
                    .catch(function (error) {
                        console.log("error");
                        console.log(error);
                    });
            },
             openModalAdd(){
                 this.modalAdd = !this.modalAdd;
                if(this.awardCategories.length === 0){
                    let loader = this.$loading.show();
                    this.axios
                        .get("/award-categories/get")
                        .then(response => {
                            const data = response.data.data;
                            this.awardCategories = data.filter(n => n.type === 1);
                            console.log(this.awardCategories);
                            loader.hide();
                        })
                        .catch(function (error) {
                            console.log(error);
                            loader.hide();
                        });
                }
            },
            modalAddEvent(e){
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length) return;
                this.modalAddFile = files[0];
                if(this.modalAddFile.size > 2097152){
                    this.$toast.error('Максимальный размер файла - 2 МБ', {
                        timeout: 5000
                    });
                } else {
                    base64Encode(this.modalAddFile)
                        .then((val) => {
                            this.modalAddBase64 = val;
                            this.$refs.inputFileAdd.style.display = 'none';
                        })
                        .catch(() => {
                            this.modalAddBase64 = null;
                        });
                }

            },
            modalSelectDataUploadEvent(e) {
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length) return;
                this.modalSelectFile = files[0];
                if(this.modalSelectFile.size > 2097152){
                    this.$toast.error('Максимальный размер файла - 2 МБ', {
                        timeout: 5000
                    });
                } else{
                    base64Encode(this.modalSelectFile)
                        .then((val) => {
                            this.$refs.inputFile.style.display = 'none';
                            this.modalSelectBase64 = val;
                        })
                        .catch(() => {
                            this.modalSelectBase64 = null;
                        });
                }

            },
            downloadItem(item) {
                this.$refs.downloadFile.style.display = 'none';
                this.$refs.inputFile.style.display = 'block';
                console.log(item);
                this.axios({
                    url: item.path,
                    method: 'GET',
                    responseType: 'blob',
                }).then((res) => {
                    const FILE = window.URL.createObjectURL(new Blob([res.data]));

                    const docUrl = document.createElement('x');
                    docUrl.href = FILE;
                    docUrl.setAttribute('download', 'file.pdf');
                    document.body.appendChild(docUrl);
                    docUrl.click();
                });
            },
            openModalSelect(item, name) {
                this.modalSelect = !this.modalSelect;
                this.modalSelectData = item;
                this.modalSelectData.name = name;
                this.modalSelectFile = null;
                this.modalSelectBase64 = null;
            },
            reward(award, index) {
                const formData = new FormData();
                formData.append('user_id', this.userId);
                formData.append('award_id', this.modalSelectData.id);
                formData.append('file', this.modalSelectFile);
                this.axios
                    .post('/awards/reward', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    })
                    .then(response => {
                        console.log(response);
                        this.$toast.success('Добавлено');
                        setTimeout(function () {
                            this.$refs.downloadFile.style.display = 'block';
                            this.$refs.inputFile.style.display = 'none';
                            this.modalSelectData = {};
                            this.modalSelectFile = null;
                            this.modalSelectBase64 = null;
                        }, 300);
                        this.modalSelect = false;
                        this.getAll();
                    })
                    .catch(function (error) {
                        console.log("error");
                        console.log(error);
                    });
            }
        }
    }
</script>

<style lang="scss">
    .remove-award-modal{
        .title-remove{
            font-size: 20px;
            color: red;
            text-align: center;
        }
        .award-image-remove{
            img, canvas{
                width: 100% !important;
                height: auto!important;
            }
        }
    }
    .selected-modal {
        .selected-modal-title {
            text-align: center;
            font-size: 20px;
            color: #888;
        }

        .result-container {
            overflow: hidden;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            border-radius: 10px;

            img {
                width: 100%;
                height: auto;
            }

            canvas {
                width: 100% !important;
                height: auto !important;
            }
        }

        .download-image-container {
            overflow: hidden;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            border-radius: 10px;

            img {
                width: 100%;
                height: auto;
                transition: 0.2s all ease;
            }

            canvas {
                width: 100% !important;
                height: auto !important;
            }

            &:before {
                content: 'Скачать';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1;
                opacity: 0;
                background-color: #333;
                transition: 0.2s all ease;
            }

            &:after {
                content: 'Скачать';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                text-transform: uppercase;
                font-size: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 2;
                color: #fff;
                opacity: 0;
                transition: 0.2s all ease;
            }

            &:hover {
                img {
                    transform: scale(1.1);
                }

                &:before {
                    opacity: 0.5;
                }

                &:after {
                    opacity: 1;
                }
            }
        }

        .custom-file-upload {
            width: 90%;
            margin: 0 auto;
            height: 300px;
            border-radius: 20px;
            border: 3px dashed #ddd;
            position: relative;
            cursor: pointer;
            transition: 0.2s all ease;

            &:before {
                content: 'Нажмите, чтобы загрузить файл';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                color: #999;
                text-transform: uppercase;
                transition: 0.2s all ease;
            }

            &:hover {
                background-color: #f2f2f2;

                &:before {
                    color: #333;
                    transform: scale(1.1);
                }
            }
        }
    }

    #award-user-sidebar {
        .my-container{
            max-height: 200px;
            overflow: auto;
            .award-image{
                border: 2px solid green;
                position: relative;
                img{
                    transition: 0.15s all ease;
                }
                i{
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    font-size: 16px;
                    transform: translate(-50%,-50%) scale(0.9);
                    opacity: 0;
                    transition: 0.2s all ease;
                    color: red;
                }
                &:hover{
                    border: 2px solid red;
                    img{
                        filter: grayscale(1);
                    }
                    i{
                        transform: translate(-50%,-50%) scale(1.1);
                        opacity: 1;
                    }
                }
            }
        }
        .available-container{
            max-height: calc(100vh - 400px);
            overflow: auto;
        }
        .or {
            font-size: 18px;
            color: #999;
            text-align: center;
            margin: 15px 0 10px 0;
        }

        .title-not-rewards {
            font-size: 16px;
            color: #aaa;
        }

        .or2 {
            line-height: 1;
            margin-bottom: 20px;
            font-size: 22px;
            color: #999;
            text-align: center;
        }

        .card-title {
            font-size: 18px;
            color: #999;
        }

        .award-image {
            height: 100px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 8px;
            border: 1px solid #ddd;
            cursor: pointer;
            transition: 0.15s all ease;

            &:hover {
                transform: scale(1.04);
            }

            img {
                width: auto;
                height: 100px;
                object-fit: cover;
            }

            canvas {
                width: auto;
                height: 100px;
            }
        }
    }

</style>