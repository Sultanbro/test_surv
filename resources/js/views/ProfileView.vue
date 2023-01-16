<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { mapActions } from 'pinia'
import { usePersonalInfoStore } from '@/stores/PersonalInfo'
import { useProfileCoursesStore } from '@/stores/ProfileCourses'
import { useProfileSalaryStore } from '@/stores/ProfileSalary'
import { useProfileStatusStore } from '@/stores/ProfileStatus'
import { usePaymentTermsStore } from '@/stores/PaymentTerms'
import { useSettingsStore } from '@/stores/Settings'
const ProfilePage = () => import(/* webpackChunkName: "ProfilePage" */ '@/pages/Profile/ProfilePage')

// const personalInfoStore = usePersonalInfoStore()
// const profileCoursesStore = useProfileCoursesStore()
// const profileSalaryStore = useProfileSalaryStore()
// const profileStatusStore = useProfileStatusStore()
// const settingsStore = useSettingsStore()
// const paymentTermsStore = usePaymentTermsStore()

export default {
	name: 'ProfileView',
	components: {
		DefaultLayout,
		ProfilePage,
	},
	mounted(){
		// подготовка данных для страницы профиля
		this.fetchSettings('company')
		this.fetchStatus()
		this.fetchPersonalInfo()
		this.fetchCourses(false)
		this.fetchSalary()
		this.fetchPaymentTerms()
	},
	methods:{
		...mapActions(usePersonalInfoStore, ['fetchPersonalInfo']),
		...mapActions(useProfileCoursesStore, ['fetchCourses']),
		...mapActions(useProfileSalaryStore, ['fetchSalary']),
		...mapActions(useProfileStatusStore, ['fetchStatus']),
		...mapActions(useSettingsStore, ['fetchSettings']),
		...mapActions(usePaymentTermsStore, ['fetchPaymentTerms']),
	}
}
</script>

<template>
	<DefaultLayout class="profile-page">
		<ProfilePage/>
	</DefaultLayout>
</template>