<template>
	<div class="news-birthday-card">
		<img
			class="news-birthday-card__image"
			:src="user.avatar"
			alt="img"
		>
		<div class="news-birthday-card__body">
			<!-- eslint-disable -->
			<span
				class="news-birthday-card__name"
				v-html="user.name"
			/>
			<span
				:class="'news-birthday-card__birthday ' + getCardColor(user)"
				v-html="user.date_human"
			/>
			<!-- eslint-enable -->
		</div>
		<div
			:class="'news-birthday-card__gift ' +(success ? 'news-birthday-card__gift--success' : (hover ? 'news-birthday-card__gift--hover' : ''))"
			@click="togleShowModal(true)"
			@mouseleave="hover=false"
			@mouseenter="hover=true"
		>
			<img
				v-show="!success"
				:src="hover ? '/icon/news/birthday/money-hover.svg' : '/icon/news/birthday/money.svg'"
				alt=""
			>
			<img
				v-show="success"
				src="/icon/news/birthday/arrow.svg"
				alt=""
			>
		</div>

		<div
			v-show="hover"
			class="news-money-title"
		>
			<img
				src="/icon/news/birthday/money-title.svg"
				alt=""
				class="news-money-title__img"
			>
			<span class="news-money-title__text">Подарить деньги</span>
		</div>

		<div
			v-show="showModal && !showSecondModal"
			v-scroll-lock="showModal"
			class="news-gift-popup"
		>
			<div class="news-gift-popup__container">
				<img
					src="/icon/news/birthday/close.svg"
					alt=""
					class="news-gift-popup__close"
					@click="togleShowModal(false)"
				>

				<div class="news-gift-popup__header">
					<img
						src="/icon/news/birthday/money-title.svg"
						alt=""
						class="news-gift-popup__img"
					>
					<span class="news-gift-popup__text">Подарить деньги</span>
				</div>

				<div class="news-gift-popup__body">
					<input
						v-model="summ"
						class="news-gift-popup__input"
						type="number"
						placeholder="Укажите сумму"
					>
					<a class="news-gift-popup__submit">
						<span @click="toggleSecondModal(summ)">Отправить</span>
					</a>
				</div>

				<div class="news-gift-popup__footer">
					<span
						class="news-gift-popup__button"
						@click="toggleSecondModal(250)"
					>250 KZT</span>
					<span
						class="news-gift-popup__button"
						@click="toggleSecondModal(500)"
					>500 KZT</span>
					<span
						class="news-gift-popup__button"
						@click="toggleSecondModal(1000)"
					>1000 KZT</span>
				</div>
			</div>
		</div>

		<div
			v-show="showSecondModal"
			class="news-gift-second"
		>
			<div class="news-gift-second__container">
				<img
					src="/icon/news/birthday/close.svg"
					alt=""
					class="news-gift-second__close"
					@click="closeSecondModal()"
				>

				<div class="news-gift-second__header">
					<img src="/icon/news/birthday/hand.svg">
					<div class="news-gift-second__text">
						Вы дарите <span>{{ summ + ' KZT' }}</span> пользователю:
					</div>
				</div>

				<div class="news-gift-second__body">
					<img
						:src="user.avatar"
						alt="img"
						class="news-birthday-card__image"
					>
					<div class="news-birthday-card__body">
						<!-- eslint-disable -->
						<span
							class="news-birthday-card__name"
							v-html="user.name"
						/>
						<span
							:class="'news-birthday-card__birthday ' + getCardColor(user)"
							v-html="user.date_human"
						/>
						<!-- eslint-enable -->
					</div>
					<a class="news-gift-second__send">
						<span @click="sendMoney">Подарить!</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import moment from 'moment/moment';

export default {
	name: 'BirthdayUser',
	props: {
		user: {
			type: Object,
			required: true
		}
	},
	data() {
		return {
			hover: false,
			showModal: false,
			showSecondModal: false,
			summ: '',
			success: false,
		}
	},
	mounted() {

	},
	methods: {
		togleShowModal(newValue) {
			this.showSecondModal = false;
			this.showModal = newValue;
			this.$root.$emit('toggle-white-bg', newValue);
		},

		toggleSecondModal(value) {
			this.summ = value;
			this.showSecondModal = true;
		},

		closeSecondModal() {
			this.showSecondModal = false;
		},

		getCardColor(user) {
			return moment(user.date).format('DD-MM') == moment().format('DD-MM') ? 'news-birthday-card__birthday--today' :
				(moment(user.date).format('DD-MM') == moment().add(1, 'd').format('DD-MM') ? 'news-birthday-card__birthday--tomorrow' : '');
		},

		async sendMoney() {
			let formData = new FormData;
			this.togleShowModal(false);
			this.showSecondModal = false;

			formData.append('amount', this.summ);
			this.summ = '';
			await this.axios.post('birthdays/' + this.user.id + '/send-gift ', formData)
				.then(res => {
					this.createAvans(res.data.data)
				})
				.catch(res => {
					console.error(res);
				})
		},
		async createAvans(data) {
			await this.axios.post('/timetracking/salaries/update', data.avansData)
				.then(() => {
					this.sendBonuses(data.bonusData)
				})
				.catch(res => {
					console.error(res)
				})
		},
		async sendBonuses(data) {
			await this.axios.post('/timetracking/salaries/update', data)
				.then(() => {
					this.success = true;
					setTimeout(() => {  this.success = false;}, 5000);
				})
				.catch(res => {
					console.error(res)
				})
		},

	}
}
</script>
