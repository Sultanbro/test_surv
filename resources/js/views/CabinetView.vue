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
			auth_role: null,
		}
	},
	mounted(){
		useAsyncPageData('/cabinet').then(data => {
			this.auth_role = data.auth_role || null
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
				v-show="auth_role"
				:auth-role="auth_role"
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
