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
        <template v-if="awards.length">
            <BRow>
                <BCol cols="12" md="3" v-for="(award, index) in awards" :key="award.id + index">
                    <b-card @click="reward(award)">
                        <div>{{ award.name }}</div>
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
                awards: [],

            }
        },
        mounted() {
            document.addEventListener('award-user-sidebar', (e) => {
                this.open = true
                console.log('USER ID:', e.detail);
                this.userId = e.detail;
            });

            this.axios
                .get('/awards/get')
                .then(response => {
                    for (let i = 0; i < response.data.data.length; i++) {
                        this.awards.push(response.data.data[i])
                    }
                    console.log(this.awards);
                })
                .catch(error => {
                    console.log(error);
                })

            this.axios
                .get('/awards/my')
                .then(response => {
                    // for (let i = 0; i < response.data.data.length; i++) {
                    //     this.awards.push(response.data.data[i])
                    // }
                    console.log(response);
                })
                .catch(error => {
                    console.log(error);
                })
        },
        methods: {
            reward(award) {
                const formData = new FormData();
                formData.append('user_id', this.userId);
                formData.append('award_id', award.id);
                this.axios
                    .post('/awards/reward', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    })
                    .then(response => {
                        console.log(response);
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
    .card {
        cursor: pointer;
        border: 1px solid #ddd;
    }
</style>