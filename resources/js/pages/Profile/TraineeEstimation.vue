<template>
	<div
		id="trainee"
		class="TraineeEstimation block _anim _anim-no-hide"
		:class="{
			'hidden': !Object.keys(data).length,
			'v-loading': loading
		}"
	>
		<div class="TraineeEstimation-title title mt-5">
			Оценка стажеров
		</div>
		<div class="TraineeEstimation-subtitle subtitle">
			Подробная информация об оценке стажерами вашего обучения
		</div>
		<div class="TraineeEstimation-content">
			<ProfileTabs
				:tabs="Object.keys(data)"
			>
				<template
					v-for="key, index in Object.keys(data)"
					#[`tab(${index})`]
				>
					<TraineeEstimationGroup
						v-for="(item, item_index) in data[key]"
						:key="item_index"
						:data="item"
					/>
				</template>
			</ProfileTabs>
		</div>
	</div>
</template>

<script>
import ProfileTabs from '@ui/ProfileTabs'
import TraineeEstimationGroup from './TraineeEstimationGroup'

export default {
	name: 'TraineeEstimation',
	components: {
		ProfileTabs,
		TraineeEstimationGroup,
	},
	props: {},
	data () {
		return {
			data: {},
			loading: false
		};
	},
	created(){
		this.fetchData()
	},
	methods: {
		async fetchData() {
			this.loading = true

			try {
				const {data} = await this.axios.post('/profile/trainee-report', {})
				this.showBtn(data)
				this.data = data
			}
			catch (error) {
				console.error(error)
			}
			this.loading = false
		},

		/**
		 * private: show btn in introTop
		 */
		showBtn(data) {
			if(Object.keys(data).length > 0) {
				this.$emit('init')
			}
		},
	}
};
</script>

<style lang="scss">
.TraineeEstimation{
	.tabs{
		padding-bottom: 4rem;
	}

	&-content{
		background: #F8F9FD;
		padding: 3rem 3.8rem 2rem;
		border-radius: 1.5rem 1.5rem 0 0;
	}
}

@media(max-width:440px){
	.TraineeEstimation{
		&-content{
			padding: 3rem 1.8rem 2rem;
		}
	}
}

@media(min-width:1920px){
  // .trainee{}
}

@media(min-width:2100px){
  .trainee__content .trainee__review{
    width: 51.2rem;
  }
}
</style>
