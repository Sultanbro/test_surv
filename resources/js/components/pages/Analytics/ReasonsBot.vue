<template>
	<div class="ReasonsBot d-flex flex-wrap pt-4">
		<div
			v-for="(quizz, key) in quiz"
			:key="key"
			class="question-wrap"
		>
			<p> {{ quizz['q'] }}</p>
			<div v-if="quizz['type'] == 'answer'">
				<div
					v-for="answer in quizz['answers']"
					:key="answer.id"
					class="d-flex"
				>
					<p class="fz12">
						{{ answer.text }}
					</p>
				</div>
			</div>
			<div v-if="quizz['type'] == 'variant'">
				<div
					v-for="answer in quizz['answers']"
					:key="answer.id"
					class="row"
				>
					<div class="col-6">
						{{ answer.text + ' (' + answer.count + ')' }}
					</div>
					<div class="col-6">
						<div class="ReasonsBot-progress">
							<div class="ReasonsBot-progressPercent">
								{{ Number(answer.percent) || 0 }}%
							</div>
							<ProgressBar :progress="(Number(answer.percent) || 0) + '%'" />
						</div>
					</div>
				</div>
			</div>
			<div v-if="quizz['type'] == 'star'">
				<div
					v-for="answer in quizz['answers']"
					:key="answer.id"
					class="d-flex"
				>
					<Rating
						:grade="Number(answer.text).toFixed(0)"
						:max-stars="10"
						:has-counter="false"
					/>
					<p class="mb-0">
						{{ answer.text + ' (' + answer.count + ')' }}
					</p>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import Rating from '@/components/ui/Rating.vue'
import ProgressBar from '@ui/ProgressBar'

export default {
	name: 'ReasonsBot',
	components: {
		Rating,
		ProgressBar,
	},
	props: {
		quiz: {
			type: Array,
			default: () => []
		}
	}
}
</script>

<style lang="scss">
.ReasonsBot{
	&-progress{
		display: flex;
		align-items: center;
		justify-content: flex-start;
		gap: 10px;
		.ProgressBar{
			flex: 1;
		}
	}
	&-progressPercent{
		flex: 0 0 3em;
	}
}
</style>

