<template>
	<b-row>
		<b-col
			cols="12"
			md="9"
			offset-md="3"
		>
			<SuperSelect
				v-if="done"
				:key="1"
				style="width: 80%;"
				:onlytype="2"
				:single="true"
				:placeholder="'Выберите должность или отдел'"
				:disable_type="1"
				:value_id="targetable_id"
				:available_courses="courses"
				:pre_build="true"
				:select_all_btn="false"
				@choose="superselectChoice"
			/>
		</b-col>
	</b-row>
</template>

<script>
import SuperSelect from '@/components/SuperSelect'
export default {
	name: 'ChoiceTop',
	components: {
		SuperSelect,
	},
	props: {
		/* eslint-disable camelcase, vue/prop-name-casing */
		targetable_id: {
			type: Number,
			default: null
		}
	},
	data() {
		return {
			courses: [],
			done: false
		}
	},
	mounted(){
		const loader = this.$loading.show()
		this.axios.get('/awards/get').then(({data}) => {
			const awards = data.data
			awards.forEach(item => {
				const hasTarget = item.targetable_id !== null && item.targetable_type !== null
				if(!hasTarget) return

				const type = item.targetable_type === 'App\\ProfileGroup'
					? 2
					: item.targetable_type === 'App\\Position'
						? 3
						: 0

				this.courses.push({
					id: item.targetable_id,
					type: type
				})
			})
			this.done = true
			loader.hide()
		}).catch(error => {
			console.error(error)
			loader.hide()
		})
	},
	methods: {
		superselectChoice(values) {
			const data = {}
			data.id = values.id

			switch(values.type){
			case 2:
				data.type = 'App\\ProfileGroup'
				break

			case 3:
				data.type = 'App\\Position'
				break
			}

			this.$emit('choiced-top', data)
		},
	}
}
</script>
