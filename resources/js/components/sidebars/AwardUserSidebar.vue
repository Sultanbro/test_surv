<template>
    <sidebar
            id="award-user-sidebar"
            title="Наградить пользователя"
            v-if="open"
            :open="open"
            @close="open = false"
            width="45%"
    >
        <!--		<b-button @click="uploadModalOpen = true" variant="primary" class="mx-auto d-block my-3">-->
        <!--			Загрузить файл награды-->
        <!--		</b-button>-->

        <!--		<awards-card class="mt-4" header="Награды пользователя" :values="awards"/>-->
        <template v-if="awardsAvailable.length || awardsMy.length">
            <h4 class="title-awards px-4 pb-4">Доступные номинации</h4>
            <BRow class="m-0">
                <BCol cols="12" md="4" v-for="(award, index) in awardsAvailable" :key="award.id + index">
                    <b-card class="award-card" @click.once="reward(award, index)" ref="award">
                        <template #header>
                            {{ award.name }}
                        </template>
                        <img :src="award.path" :alt="award.name" :title="award.name" style="width: 100%; height: auto">
                    </b-card>
                </BCol>
            </BRow>
            <hr class="my-4">
            <h4 class="title-awards px-4 pb-4">Выданные номинации</h4>
            <BRow class="m-0">
                <BCol cols="12" md="4" v-for="(award, index) in awardsMy" :key="award.id + index">
                    <b-card class="award-card my">
                        <img :src="award.path" :alt="award.name" :title="award.name" style="width: 100%; height: auto">
                    </b-card>
                </BCol>
            </BRow>
        </template>
        <template v-else>
            <div class="text-center w-100">Ничего нет</div>
        </template>
        <!--		<upload-modal :open.sync="uploadModalOpen" />-->
    </sidebar>
</template>

<script>
    // import AwardsCard from '../profile/UserEarnings/AwardsCard.vue'
    import UploadModal from '../modals/Upload'

    export default {
        name: 'AwardUserSidebar',
        components: {UploadModal},
        data() {
            return {
                open: false,
                uploadModalOpen: false,
                userId: null,
                awardsMy: [],
                awardsAvailable: [],

            }
        },
        mounted() {
            document.addEventListener('award-user-sidebar', (e) => {
                this.open = true;
                console.log('USER ID:', e.detail);
                this.userId = e.detail;

                this.axios
                    .get('/awards/type?key=nomination')
                    .then(response => {
                        console.log(response);
                    })
                    .catch(error => {
                        console.log(error);
                    })
            });
        },
        methods: {
            reward(award, index) {
                this.$refs.award[index].classList.add('active');
                const formData = new FormData();
                formData.append('user_id', this.userId);
                formData.append('award_id', award.id);
                //Нужно реализовать
                // formData.append('file', 'Какой-то файл');
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
                    })
                    .catch(function (error) {
                        console.log("error");
                        console.log(error);
                    });
            }
        }
    }
</script>

<style lang="scss" scoped>
    .title-awards{
        font-size: 20px;
        font-weight: 600;
    }
    .award-card {
        cursor: pointer;
        border: 1px solid #ddd;
        &.active{
            border: 3px solid #038e34;
        }
        &.my{
            border: 3px solid #038e34;
            cursor: default;
            img{
                filter: grayscale(1);
            }
        }

    .card-header{
        padding: 10px;
        font-size: 14px;
        font-weight: 600;
    }
        .card-body{
            overflow: hidden;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            img{
                width: auto !important;
                height: 150px !important;
                object-fit: cover;
            }
        }
    }
</style>