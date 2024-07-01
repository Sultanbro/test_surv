<template>
	<div
		v-if="data"
		class="TraineeEstimationGroup"
	>
		<div class="TraineeEstimationGroup-name">
			{{ data.group }}
		</div>

		<!-- table with answers -->
		<table class="TraineeEstimationGroup-quiz">
			<thead>
				<tr>
					<th>Суть работы</th>
					<th>График работы</th>
					<th>Заработная плата</th>
					<th>Оценка тренера</th>
					<th>Рекомендации</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="top">
						<div
							v-for="answer in data.quiz[1]"
							:key="answer.id"
							class="TraineeEstimationGroup-cell"
						>
							<TraineeEstimationProgress
								:text="answer.text"
								:count="Number(answer.count)"
								:percent="Number(answer.percent)"
								class="TraineeEstimationGroup-answer"
							/>
						</div>
					</td>
					<td class="top">
						<div
							v-for="answer in data.quiz[2]"
							:key="answer.id"
							class="TraineeEstimationGroup-cell"
						>
							<TraineeEstimationProgress
								:text="answer.text"
								:count="Number(answer.count)"
								:percent="Number(answer.percent)"
								class="TraineeEstimationGroup-answer"
							/>
						</div>
					</td>
					<td class="top">
						<div
							v-for="answer in data.quiz[3]"
							:key="answer.id"
							class="TraineeEstimationGroup-cell"
						>
							<TraineeEstimationProgress
								:text="answer.text"
								:count="Number(answer.count)"
								:percent="Number(answer.percent)"
								class="TraineeEstimationGroup-answer"
							/>
						</div>
					</td>
					<td class="top">
						<div
							class="TraineeEstimationGroup-cell"
							data-rows="4"
						>
							<div class="TraineeEstimationGroup-rating">
								<div class="TraineeEstimationGroup-ratingValue">
									{{ data.quiz[4].count }} оценок ({{ data.quiz[4].avg }})
								</div>
								<ProfileStars
									:value="parseInt(data.quiz[4].avg)"
								/>
							</div>
						</div>
					</td>
					<td class="top">
						<template v-for="(answer, ind) in data.quiz[5]">
							<div
								v-if="answer"
								:key="ind"
								class="TraineeEstimationGroup-cell"
							>
								<div class="TraineeEstimationGroup-review">
									{{ answer }}
								</div>
							</div>
						</template>
					</td>
				</tr>
			</tbody>
		</table>

		<!-- invited table -->
		<table class="TraineeEstimationGroup-invite mb-5">
			<thead>
				<tr>
					<th class="first-td">
						Приглашенные
					</th>
					<th>1 день</th>
					<th>2 день</th>
					<th>3 день</th>
					<th>4 день</th>
					<th>5 день</th>
					<th>6 день</th>
					<th>7 день</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="first-td">
						{{ data.presence[0] }}
					</td>
					<td>{{ data.presence[1] }}</td>
					<td>{{ data.presence[2] }}</td>
					<td>{{ data.presence[3] }}</td>
					<td>{{ data.presence[4] }}</td>
					<td>{{ data.presence[5] }}</td>
					<td>{{ data.presence[6] }}</td>
					<td>{{ data.presence[7] }}</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
import TraineeEstimationProgress from './TraineeEstimationProgress'
import ProfileStars from '@ui/ProfileStars'
export default {
	name: 'TraineeEstimationGroup',
	components: {
		TraineeEstimationProgress,
		ProfileStars,
	},
	props: {
		data: {
			type: Object,
			default: null
		}
	}
}
</script>

<style lang="scss">
.TraineeEstimationGroup{
	margin-top: 3rem;
	&-name{
		font-family: "Open Sans",sans-serif;
		margin-bottom: 2rem;
		font-size:1.8rem;
		color:#5B6166;
		font-weight: 600;
	}
	&-quiz,
	&-invite{
		width: 100%;
		// padding-right: 3.5rem;

		border-radius: 1.2rem 1.2rem 0 0;
		border-collapse: separate;
		border-spacing: 0px;
		th, td{
			height: 4rem;
		}
		th{
			border: 1px solid #E7EAEA;
			text-align: center;
			font-size: 1.3rem;
			font-weight: 600;
			color: #62788B;
			&:first-of-type{
				border-radius: 1.2rem 0 0 0;
			}
			&:last-of-type{
				border-radius: 0 1.2rem 0 0;
			}
		}
		td{
			font-size: 1.2rem;
		}
	}
	&-invite{
		margin-top: 2rem;
		th, td{
			&:first-of-type{
				min-width: 45.7rem;
				font-weight: 600;
				background: #F5F7FC;
			}
		}
		th{
			min-width: 12rem;
			font-size: 1.2rem;
			font-weight: 600;
			background: #fff;
		}
		td{
			width: 12rem;
			border: 1px solid #E7EAEA;
			text-align: center;
			color: #62788B;
			background: #fff;
		}
	}
	&-quiz{
		td{
			border: 1px solid transparent;
		}
	}
	&-answer{
		display: flex;
		align-items: center;

		min-width: 24rem;
		height: 4rem;
		overflow: hidden;
		padding: 1.2rem 1.5rem;
	}
	&-cell{
		height: 4rem;
		margin-bottom: 1px;
		background: #fff;
		outline: 1px solid #E7EAEA;
		&[data-rows="4"]{
			height: calc(16rem + 3px);
		}
	}
	&-rating{
		padding: 1.2rem 1.5rem;
	}
	&-ratingValue{
		margin-bottom: 1rem;
		font-weight: 700;
	}

	&-review{
		display: flex;
		align-items: center;

    width: 33.4rem;
		height: 4rem;
		padding: 1.2rem 1.5rem;

		font-size:1.2rem;
		color:#62788B;
	}


	.top{
		vertical-align: top;
	}


  table.invite{
    margin-top: 2rem;
    td{
      width: 12rem;
      text-align: center;
      background: #fff;
      color:#62788B;
    }
    th {
      background: #fff;
      min-width: 12rem;
      font-size:1.2rem;
      font-weight: 600;
    }

    .first-td{
      background: #F5F7FC;
      min-width: 45.7rem;
      font-weight: 600;

    }
  }
}
</style>
