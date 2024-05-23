<template>
	<div>
		<button
			class="faq-item-price pb-4"
			@click="question = !question"
		>
			Вопросы по тарифам
		</button>
		<div
			v-if="question"
			class="referral-link-faq-content"
		>
			<div class="referral-faq-question">
				<div
					v-for="(item, index) in faqItems"
					:key="index"
					class="faq-item"
					@click="toggleAnswer(index)"
				>
					<div class="faq-item-content">
						<div class="faq-item-question">
							<div class="faq-item-question-title">
								{{ item.question }}
							</div>
							<div class="faq-item-image-block">
								<img
									class="faq-item-image"
									:class="{ active: openedIndex === index }"
									src="/icon/news/referral-partner/plus.svg"
								>
							</div>
						</div>

						<div
							v-if="openedIndex === index"
							class="answer"
						>
							{{ item.answer }}
						</div>
					</div>
				</div>
				<button
					v-if="canShowMore"
					@click="showMore += 5"
				>
					Показать еще
				</button>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'PriceFAQ',
	data() {
		return {
			showMore: 10,
			question: false,
			faqItems: [
				{
					question: 'Есть ли автопродление тарифов, чтобы не оплачивать вручную?',
					answer: 'Что бы не оплачивать ежемесячно. Вы можете оплатить за 3 месяца или за год',
				},
				{
					question: 'Сколько рабочих пространств я могу подключить к бесплатному пробному периоду?',
					answer: 'На бесплатном тарифе Вы можете создать до 3 проектов.',
				},
				{
					question: 'Что произойдет, если пропустить оплату тарифа?',
					answer: 'Ваш тариф перейдет на бесплатный. В портал смогут войти только до 5 сотрудников.',
				},
				{
					question: 'Можно ли подключить дополнительных сотрудников после оплаты тарифа?',
					answer: 'Да, Вы можете нажать на кнопку Управление тарифом и добавить дополнительно сотрудников.',
				},
				{
					question: 'Если в компании значительно больше 100 сотрудников, есть ли особые условия по их подключению?',
					answer: 'Особых условий по подключению больше 100 сотрудников нет. Вам просто нужно перейти в Управление тарифом и добавить нужное дополнительное количество сотрудников',
				},
				{
					question: 'У меня есть промокод, где его применить?',
					answer: 'При оплате, нажав на тариф, выйдет окошко для вашего промокода.',
				},
				{
					question: 'Нет ответа на ваш вопрос?',
					answer: 'Если вы не нашли ответа на вопрос, то напишите Вашему менеджеру заботы. Его контакты выше на этой старанице.',
				},
			],
			openedIndex: null,
			close: false,
		};
	},
	computed: {
		canShowMore() {
			return this.showMore < this.faqItems.length;
		},
	},
	methods: {
		toggleAnswer(index) {
			if (this.openedIndex === index) {
				this.openedIndex = null;
			} else {
				this.openedIndex = index;
			}
		},
	},
};
</script>

<style scoped>
@media (min-width: 1600px) {
	.faq-item-question-title {
		font-size: 20px !important;
		font-weight: 600 !important;
	}

	.faq-item-price {
		font-weight: 600 !important;
		font-size: 44px !important;
		line-height: 54px !important;
	}

	.referral-link-faq-content {
		font-size: 16px !important;
		overflow-y: auto !important;
	}

	.referral-faq-question {
		padding: 20px 0 20px 0 !important;
	}

	.faq-item {
		padding: 20px 0 20px 0 !important;
	}

	.faq-item-content {
		transition: max-height 0.3s ease-out !important;
	}

	.answer {
		margin-top: 10px !important;
	}


	.faq-item-question {
		display: flex !important;
		justify-content: space-between !important;
	}

	.faq-item-image-block {
		padding: 8px !important;
	}

	.faq-item-image {
		transition: transform 0.3s ease !important;
	}

	.faq-item-image.active {
		transform: rotate(45deg) !important;
	}
}

.faq-item-question-title {
	font-size: 16px;
	font-weight: 600;
}

.faq-item-price {
	font-weight: 600;
	font-size: 30px;
	line-height: 44px;
	color: #333333;
	background-color: white;
	cursor: pointer;
}

.referral-link-faq-content {
	font-size: 14px;
	overflow-y: auto;
	animation: fadeInUp 0.3s ease;
}

.referral-faq-question {
	display: flex;
	flex-direction: column;
	padding: 20px 0 20px 0;
}

.faq-item {
	max-width: 800px;
	width: 100%;
	background-color: white;
	cursor: pointer;
	padding: 20px 0 20px 0 !important;
	border-bottom: 1px solid #CCCCCC;
}


.faq-item-content {
	transition: max-height 0.3s ease-out;
}

.answer {
	margin-top: 10px;
	color: gray;
	max-height: 100vh;
	animation: fadeInUp 0.3s ease;
	overflow: hidden;
}

@keyframes fadeInUp {
	0% {
		transform: translateY(-40px);
	}
	100% {
		transform: translateY(0);
	}
}

.faq-item-question {
	display: flex;
	justify-content: space-between;
}

.faq-item-image-block {
	padding: 8px;
	background-color: #F2F2F2;
}

.faq-item-image {
	transition: transform 0.3s ease;
	background-color: #F2F2F2;
}

.faq-item-image.active {
	transform: rotate(45deg);
}
</style>
