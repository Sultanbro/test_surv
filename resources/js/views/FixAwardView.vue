<script>
import {fetchProfileAwards,} from '@/stores/api.js'

import DefaultLayout from '@/layouts/DefaultLayout'

export default {
	name: 'FixAwardView',
	components: {
		DefaultLayout,
	},
	data(){
		return {
			categories: [],
			personalData: {}
		}
	},
	mounted(){
		this.getAwards()
	},
	methods: {
		async getAwards() {
			const loader = this.$loading.show()
			this.categories = []
			try {
				const {data} = await this.axios.get('/award-categories/get')
				for(const cat of data.data){
					const {data} = await this.axios.get('/award-categories/get/awards/' + cat.id)
					cat.items = data.data
				}
				this.categories = data.data

				const personalData = {
					nominations: await fetchProfileAwards('nomination'),
					// certificates: await fetchProfileAwards('certificate'),
					accrual: await fetchProfileAwards('accrual'),
				}
				this.personalData = personalData
			}
			catch (error) {
				console.error(error)
			}
			loader.hide()
		},
		fix(/* awardId */){},
		fixUser(/* awardId */){},
	},
}
</script>

<template>
	<DefaultLayout>
		<div class="old__content">
			<div class="FixAwardView">
				<h2>Награды</h2>
				<div
					v-for="cat in categories"
					:key="cat.id"
					class="FixAwardView-category mb-4"
				>
					<h3
						:title="cat.id"
						class="FixAwardView-title"
					>
						{{ cat.type }} {{ cat.name }}
					</h3>
					<p class="FixAwardView-subtitle">
						{{ cat.description }}
					</p>
					<template v-if="cat.items">
						<div
							v-for="item in cat.items"
							:key="item.id"
							class="FixAwardView-item d-flex gap-4 mb-4 py-2"
							:class="item.preview_path ? '' : 'FixAwardView-item_error'"
						>
							<!--  -->
							<div class="FixAwardView-path flex-1">
								{{ item.path }}
							</div>
							<div class="FixAwardView-preview flex-1">
								{{ item.preview_path }}
							</div>
							<div class="FixAwardView-actions">
								<button
									@click="fix(item.id)"
								>
									Создать превью
								</button>
							</div>
						</div>
					</template>
				</div>
				<h2>Выданные</h2>
				<div
					v-for="cats in personalData"
					:key="cats.id"
				>
					<template v-for="cat in cats.data">
						<h3
							:key="cat.id"
							:title="cat.id"
							class="FixAwardView-title"
						>
							{{ cat.name }}
						</h3>
						<p
							:key="'p' + cat.id"
							class="FixAwardView-subtitle"
						>
							{{ cat.description }}
						</p>
						<template v-if="cat.other">
							<div
								v-for="item in cat.other"
								:key="item.award_id + '-' + item.user_id"
								class="FixAwardView-item d-flex gap-4 mb-4 py-2"
								:class="item.preview_path ? '' : 'FixAwardView-item_error'"
							>
								<!--  -->
								<div class="FixAwardView-path flex-1">
									{{ item.tempPath }}
								</div>
								<div class="FixAwardView-preview flex-1">
									{{ item.preview_path }}
								</div>
								<div class="FixAwardView-actions">
									<button
										@click="fixUser(item)"
									>
										Создать превью
									</button>
								</div>
							</div>
						</template>
					</template>
				</div>
			</div>
		</div>
	</DefaultLayout>
</template>

<style lang="scss">
.FixAwardView{
	&-title{
		font-size: 24px;
	}
	&-subtitle{
		font-size: 16px;
	}
	&-item{
		background-color: #6a6;
		&_error{
			background-color: #a66;
		}
	}
	h2{
		font-size: 28px;
		font-weight: 700;
	}
}
</style>
