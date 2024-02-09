<script>
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import RouterTabs from '@ui/RouterTabs.vue'

export default {
	name: 'CoursesViewV2',
	components: {
		DefaultLayout,
		RouterTabs,
	},
	data(){
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
	mounted(){}
}
</script>

<template>
	<DefaultLayout>
		<div class="old__content courses">
			<RouterTabs
				:tabs="tabs"
				:value="$route.path"
				class="kpi-tabs mt-4"
			>
				<router-view />
			</RouterTabs>
		</div>
	</DefaultLayout>
</template>
