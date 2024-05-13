<template>
	<div>
		<h1 class="faq-item-price pb-4">
			Вопросы по тарифам
		</h1>
		<div class="referral-link-faq-content">
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
							class="answer answer--visible"
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
			faqItems: [
				{
					question: 'Если мне не понравился тариф, могу ли я вернуть деньги?',
					answer:
						'Вернуть средства за оплаченный тариф вы можете в течении 3 дней после покупки. Для этого обратитесь к вашему персональному менеджеру. Также обращаем ваше внимание,что возврат денежных средств может осуществляться до 10 рабочих дней (зависит от платежной системы, через которую осуществлялся платеж)',
				},
				{ question: 'Есть ли автопродление тарифов, чтобы не оплачивать вручную?', answer: '...' },
				{ question: 'Сколько рабочих пространств я могу подключить к бесплатному пробному периоду?', answer: '...' },
				{ question: 'Что произойдет, если пропустить оплату тарифа?', answer: '...' },
				{ question: 'Как перейти на другой тариф?', answer: '...' },
				{ question: 'Можно ли подключить дополнительных сотрудников после оплаты тарифа?', answer: '...' },
				{ question: 'Если в компании значительно больше 100 сотрудников, есть ли особые условия по их подключению?', answer: '...' },
				{ question: 'У меня есть промокод, где его применить?', answer: '...' },
				{ question: 'Нет ответа на ваш вопрос?', answer: '...' },
			],
			openedIndex: null,
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
@media (min-width: 1400px) {
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

	.answer--visible {
		max-height: 100vh !important;
		opacity: 1 !important;
		transform: translateY(0) !important;
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
	font-size: 18px;
	font-weight: 600;
}

.faq-item-price {
	font-weight: 600;
	font-size: 36px;
	line-height: 54px;

}

.referral-link-faq-content {
	font-size: 14px;
	overflow-y: auto;
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
	max-height: 0;
	opacity: 0;
	overflow: hidden;
	transform: translateY(-10px);
	transition: transform 0.2s ease-out, opacity 0.2s ease-out, max-height 0.3s ease-out;
}

.answer--visible {
	max-height: 100vh;
	opacity: 1;
	transform: translateY(0);
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
