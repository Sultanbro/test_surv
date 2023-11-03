<template>
	<div class="NewsQNA">
		<NewsQNAItem
			v-for="item, index in qna"
			:key="index"
			:item="item"
			:reanswer="reanswer"
			:is-responded="isResponded"
			class="NewsQNA-item"
			@change="onChange(item, $event)"
		/>
		<div class="d-flex gap-4 mb-4">
			<JobtronButton
				v-if="!isResponded || reanswer"
				@click="onSend"
			>
				Проголосовать
			</JobtronButton>
			<JobtronButton
				v-if="isResponded && isChangeAnswers && !reanswer"
				@click="reanswer = true"
			>
				Переголосовать
			</JobtronButton>
		</div>
	</div>
</template>

<script>
import { mapGetters } from 'vuex'

import NewsQNAItem from './NewsQNAItem.vue'
import JobtronButton from '@ui/Button.vue'

export default {
	name: 'NewsQNA',
	components: {
		NewsQNAItem,
		JobtronButton,
	},
	props: {
		qna: {
			type: Array,
			default: () => [],
		}
	},
	data(){
		return {
			reanswer: false,
			votes: {}
		}
	},
	computed: {
		...mapGetters(['user']),
		participantIds(){
			const ids = []
			this.qna.forEach(item => {
				item.answers.forEach(answer => {
					ids.push(...answer.votes.map(vote => vote.id))
				})
			})
			return ids
		},
		isResponded(){
			return this.participantIds.includes(this.user.id)
		},
		isChangeAnswers(){
			if(!this.qna.length) return false
			return this.qna[0].changeAnswers
		}
	},
	methods: {
		onChange(item, values){
			this.votes[item.id] = values
		},
		onSend(){},
	}
}
</script>

<style lang="scss">
.NewsQNA{
	&-item{
		margin-bottom: 20px;
	}
}
</style>
