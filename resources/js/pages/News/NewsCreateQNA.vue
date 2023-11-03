<template>
	<div class="NewsCreateQNA">
		<h3 class="NewsCreateQNA-header">
			Вопросы
		</h3>
		<div
			v-for="item, index in value"
			:key="index"
			class="NewsCreateQNA-item"
		>
			<div class="NewsCreateQNA-question">
				<InputText
					v-model="item.question"
					placeholder="Введите вопрос"
				/>
				<b-button
					v-if="value.length > 1"
					class="NewsCreateQNA-trash btn btn-danger btn-icon"
					@click="$emit('remove-question', index)"
				>
					<i class="fa fa-trash" />
				</b-button>
			</div>
			<draggable
				v-model="item.variants"
				:options="{handle:'.NewsCreateQNA-move'}"
				class="NewsCreateQNA-answers"
			>
				<div
					v-for="variant, variantIndex in item.variants"
					:key="variantIndex"
					class="NewsCreateQNA-answer"
				>
					<InputText
						v-model="item.variants[variantIndex]"
						placeholder="Введите вариант ответа"
						@focus="onFocusAnswer(index, variantIndex)"
					/>
					<div class="NewsCreateQNA-move">
						<i class="fa fa-sort" />
					</div>
					<b-button
						v-if="item.variants.length > 2"
						class="NewsCreateQNA-trash btn btn-danger btn-icon"
						@click="onRemoveAnswer(index, variantIndex)"
					>
						<i class="fa fa-trash" />
					</b-button>
				</div>
			</draggable>
			<div class="NewsCreateQNA-config">
				<b-row>
					<b-col cols="4">
						<label class="NewsCreateQNA-field">
							<JobtronSwitch
								v-model="item.config.manyanswers"
							/>
							Разрешить несколько ответов
						</label>
					</b-col>
					<b-col cols="4">
						<!-- <label class="NewsCreateQNA-field">
							<JobtronSwitch
								v-model="item.config.changeanswers"
							/>
							Разрешить менять ответ
						</label> -->
					</b-col>
					<b-col cols="4">
						<!-- <label class="NewsCreateQNA-field">
							<JobtronSwitch
								v-model="item.config.public"
							/>
							Публичный опрос
							<img
								v-b-popover.hover="'Будет видно кто как проголосовал'"
								src="/images/dist/profit-info.svg"
								class="img-info"
								alt="info icon"
							>
						</label> -->
					</b-col>
				</b-row>
			</div>
		</div>
		<JobtronButton
			@click="$emit('add-question')"
		>
			Добавить вопрос
		</JobtronButton>
	</div>
</template>

<script>
import InputText from '@ui/InputText'
import JobtronButton from '@ui/Button'
import JobtronSwitch from '@ui/Switch'
export default {
	name: 'NewsCreateQNA',
	components: {
		InputText,
		JobtronButton,
		JobtronSwitch,
	},
	props: {
		value: {
			type: Array,
			required: true
		}
	}
	,
	methods: {
		onFocusAnswer(qIndex, aIndex){
			const question = this.value[qIndex]
			if(question.variants.length - 1 === aIndex) question.variants.push('')
		},
		onRemoveAnswer(qIndex, aIndex){
			const question = this.value[qIndex]
			if(!question.variants[aIndex]) return question.variants.splice(aIndex, 1)
			if(confirm('Удалить вариант ответа?')) return question.variants.splice(aIndex, 1)
		},
	},
}
</script>

<style lang="scss">
.NewsCreateQNA{
	display: flex;
	flex-flow: column nowrap;
	justify-content: flex-start;
	align-items: flex-start;
	gap: 20px;

	padding: 10px 20px;
	background-color: #fff;
	box-shadow: 0 8px 16px 4px rgba(137, 143, 150, 0.18);

	&-header{
		font-size: 20px;
		font-weight: 600;
	}

	&-item{
		width: 100%;
	}
	&-question{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		gap: 10px;

		margin-bottom: 10px;
		.InputText{
			flex: 1;
		}
	}
	&-answers{
		display: flex;
		flex-flow: column nowrap;
		gap: 10px;
	}
	&-answer{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		gap: 10px;

		margin-left: 20px;

		.InputText{
			flex: 1;
		}
	}
	&-move{
		cursor: move;
		color: #777;
		&:hover{
			color: #000;
		}
	}
	&-config{
		margin-top: 10px;
	}
	&-field{
		display: flex;
		align-items: center;
		.JobtronSwitch{
			transform-origin: center;
			transform: scale(0.75);
		}
	}

	.InputText{
		border: 1px solid #aaa;
		font-size: 14px;
		border-radius: 4px;
		&_focus{
			border: 1px solid #000;
		}
	}
}
</style>
