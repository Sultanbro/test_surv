<template>
	<sidebar
        id="award-user-sidebar"
        title="Наградить пользователя"
		v-if="open"
        :open="open"
        @close="open = false"
        width="45%"
    >
		<b-button @click="uploadModalOpen = true" variant="primary" class="mx-auto d-block my-3">
			Загрузить файл награды
		</b-button>

		<awards-card class="mt-4" header="Награды пользователя" :values="awards"/>

		<upload-modal :open.sync="uploadModalOpen" />
	</sidebar>
</template>

<script>
import AwardsCard from '../profile/UserEarnings/AwardsCard.vue'
import UploadModal from '../modals/Upload'

export default {
	name: 'AwardUserSidebar',
	components: { AwardsCard, UploadModal },
	data () {
		return {
			open: false,
			uploadModalOpen: false,
			awards: [
				{ imgSrc: 'myAward1.png' },
				{ imgSrc: 'myAward1.png' },
				{ imgSrc: 'myAward1.png' },
				{ imgSrc: 'myAward1.png' },
				{ imgSrc: 'myAward1.png' }
			]
		}
	},
	mounted () {
		document.addEventListener('award-user-sidebar', (e) => {
			this.open = true
			console.log('USER ID:', e.detail)
		});

		this.axios
		.get('/awards/get')
        .then(response => {
			console.log(response);
		})
		.catch(error => {
			console.log(error);
		})
	}
}
</script>