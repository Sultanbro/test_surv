<template>
	<div>
		<h1 class="faq-item-price">
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
							<div
								class="faq-item-question-title"
							>
								{{ item.question }}
							</div>
							<div class="faq-item-image-block">
								<img
									class="faq-item-image"
									:class="{'active':item.showAnswer}"
									src="/icon/news/referral-partner/plus.svg"
								>
							</div>
						</div>

						<div
							v-if="item.showAnswer"
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
					answer: 'Вернуть средства за оплаченный тариф вы можете в течении 3 дней после покупки. Для этого обратитесь к вашему персональному менеджеру. Также обращаем ваше внимание,что возврат денежных средств может осуществляться до 10 рабочих дней (зависит от платежной системы, через которую осуществлялся платеж)'
				},
				{question: 'Есть ли автопродление тарифов, чтобы не оплачивать вручную?', answer: 'Вернуть средства за оплаченный тариф вы можете в течении 3 дней после покупки. Для этого обратитесь к вашему персональному менеджеру. Также обращаем ваше внимание,что возврат денежных средств может осуществляться до 10 рабочих дней (зависит от платежной системы, через которую осуществлялся платеж)'},
				{question: 'Сколько рабочих пространств я могу подключить к бесплатному пробному периоду?', answer: 'Вернуть средства за оплаченный тариф вы можете в течении 3 дней после покупки. Для этого обратитесь к вашему персональному менеджеру. Также обращаем ваше внимание,что возврат денежных средств может осуществляться до 10 рабочих дней (зависит от платежной системы, через которую осуществлялся платеж)'},
				{question: 'Что произойдет, если пропустить оплату тарифа?', answer: 'Вернуть средства за оплаченный тариф вы можете в течении 3 дней после покупки. Для этого обратитесь к вашему персональному менеджеру. Также обращаем ваше внимание,что возврат денежных средств может осуществляться до 10 рабочих дней (зависит от платежной системы, через которую осуществлялся платеж)'},
				{question: 'Как перейти на другой тариф?', answer: 'Вернуть средства за оплаченный тариф вы можете в течении 3 дней после покупки. Для этого обратитесь к вашему персональному менеджеру. Также обращаем ваше внимание,что возврат денежных средств может осуществляться до 10 рабочих дней (зависит от платежной системы, через которую осуществлялся платеж)'},
				{question: 'Можно ли подключить дополнительных сотрудников после оплаты тарифа?', answer: 'Вернуть средства за оплаченный тариф вы можете в течении 3 дней после покупки. Для этого обратитесь к вашему персональному менеджеру. Также обращаем ваше внимание,что возврат денежных средств может осуществляться до 10 рабочих дней (зависит от платежной системы, через которую осуществлялся платеж)'},
				{
					question: 'Если в компании значительно больше 100 сотрудников, есть ли особые условия по их подключению?',
					answer: 'Вернуть средства за оплаченный тариф вы можете в течении 3 дней после покупки. Для этого обратитесь к вашему персональному менеджеру. Также обращаем ваше внимание,что возврат денежных средств может осуществляться до 10 рабочих дней (зависит от платежной системы, через которую осуществлялся платеж)'
				},
				{question: 'У меня есть промокод, где его применить?', answer: 'Вернуть средства за оплаченный тариф вы можете в течении 3 дней после покупки. Для этого обратитесь к вашему персональному менеджеру. Также обращаем ваше внимание,что возврат денежных средств может осуществляться до 10 рабочих дней (зависит от платежной системы, через которую осуществлялся платеж)'},
				{question: 'Нет ответа на ваш вопрос?', answer: 'Вернуть средства за оплаченный тариф вы можете в течении 3 дней после покупки. Для этого обратитесь к вашему персональному менеджеру. Также обращаем ваше внимание,что возврат денежных средств может осуществляться до 10 рабочих дней (зависит от платежной системы, через которую осуществлялся платеж)'},
			]
		}
	},
	computed: {
		canShowMore() {
			return this.showMore < this.faqItems.length;
		},
	},
	methods: {
		toggleAnswer(index) {
			const faqItems = [...this.faqItems];
			faqItems[index].showAnswer = !faqItems[index].showAnswer;
			this.faqItems = faqItems;
		},

	},
}
</script>

<style scoped>

.faq-item-question-title {
	font-size: 20px;
	font-weight: 600;
}

.faq-item-price {
	font-weight: 600;
	font-size: 44px;
	line-height: 54px;
}

.referral-link-faq-content {
	font-size: 16px;
	overflow-y: auto;
}

.referral-faq-question {
	display: flex;
	flex-direction: column;

	padding: 20px;
}

.faq-item {
	max-width: 900px;
	width: 100%;
	background-color: white;
	cursor: pointer;
	padding: 20px;
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

}

.faq-item-image.active {
	transform: rotate(45deg);
}


</style>
