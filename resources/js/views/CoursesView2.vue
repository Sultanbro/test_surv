<script>
import DefaultLayout from '@/layouts/DefaultLayout'
// const Courses = () => import(/* webpackChunkName: "CoursesPage" */ '@/pages/Courses')
import RouterTabs from '@ui/RouterTabs.vue'

export default {
	name: 'CoursesView2',
	components: {
		DefaultLayout,
		RouterTabs

	},
	data() {
		return {
			tabs: [
				{
					title: 'Курсы',
					value: '/courses2',
				},
				{
					title: 'Назначенные курсы',
					value: '/courses2/assigned',
				},
				{
					title: 'Каталог курсов',
					value: '/courses2/catalog',
				},
			]
		}
	},
}
</script>
<template>
	<DefaultLayout>
		<div class="old__content">
			<RouterTabs
				:tabs="tabs"
				:value="$route.path"
				class="mt-4"
			>
				<router-view />
			</RouterTabs>
		</div>
	</DefaultLayout>
</template>
