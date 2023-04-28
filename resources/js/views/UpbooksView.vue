<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const Upbooks = () => import(/* webpackChunkName: "UpbooksPage" */ '@/pages/Upbooks')

export default {
	name: 'UpbooksView',
	components: {
		DefaultLayout,
		Upbooks,
	},
	data(){
		return {
			token: '',
			can_edit: false
		}
	},
	mounted(){
		useAsyncPageData('/admin/upbooks').then(data => {
			this.token = data.token
			this.can_edit = data.can_edit
		}).catch(error => {
			console.error('useAsyncPageData', error)
		})
	}
}
</script>

<template>
	<DefaultLayout class="no-padding">
		<div class="old__content my-course-content">
			<Upbooks
				v-show="token"
				:token="token"
				:can_edit="can_edit"
			/>
		</div>
	</DefaultLayout>
</template>

<style scoped>
.header__profile {
    display:none !important;
}
@media (min-width: 1360px) {
    .container.container-left-padding {
        padding-left: 7rem !important;
        padding-right: 6rem !important;
    }
}
</style>
