<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const Cabinet = () => import(/* webpackChunkName: "CabinetPage" */ '@/pages/Cabinet')

export default {
	name: 'CabinetView',
	components: {
		DefaultLayout,
		Cabinet,
	},
	data(){
		return {
			authRole: null,
		}
	},
	mounted(){
		useAsyncPageData('/cabinet').then(data => {
			this.authRole = data.auth_role || null
		}).catch(error => {
			console.error('useAsyncPageData', error)
		})
	}
}
</script>

<template>
	<DefaultLayout class="no-padding">
		<div class="old__content">
			<Cabinet
				v-show="authRole"
				:auth-role="authRole"
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
    }
}
</style>
