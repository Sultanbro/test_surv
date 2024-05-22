<template>
	<div class="price-space">
		<h2 class="price-space-title">
			Ваше пространство
		</h2>
		<div class="price-space-info-blocks">
			<div class="price-space-content">
				<div class="price-space-image">
					<img
						class="price-space-image"
						src="/images/price/DefaultAvatar.png"
					>
				</div>
				<div class="price-space-info">
					<div
						v-if="current"
						class="price-space-title-block"
					>
						{{ current.tenant_id }}
					</div>
					<div class="price-space-text-content">
						<p class="price-space-text-name">
							Тариф:
						</p>
						<p class="price-space-name-data">
							<template v-if="info.tariff">
								{{ names[info.tariff.kind] }}
							</template>
							<template v-else-if="info">
								Бесплатный
							</template>
							<b-skeleton v-else />
						</p>
					</div>
					<div class="price-space-text-content">
						<p class="price-space-text-name">
							Пользователей:
						</p>
						<p
							v-if="info"
							class="price-space-name-data"
						>
							{{ totalUsers }} из {{ info.tariff? info.tariff.total_user_limit : '5' }}
						</p>
						<b-skeleton v-else />
					</div>
					<div
						v-if="info.tariff && info.tariff.payment_id !=='trial'"
						class="price-space-buttons"
					>
						{{ info.tariff }}
						<button>Продлить</button>
						<p

							@click="editRate"
						>
							Управление тарифом
						</p>
					</div>
				</div>
			</div>
			<div class="price-space-content">
				<div class="price-space-image">
					<img
						class="price-space-image"
						:src="manager && manager.photo ? manager.photo : '/images/price/DefaultAvatar.png'"
					>
				</div>
				<div class="price-space-info">
					<div class="price-space-info-text">
						<p v-if="owner">
							{{ title }}
						</p>
						<b-skeleton v-else />
					</div>



					<div class="price-space-title-block">
						<p
							v-if="info"
							class="price-space-title-block"
						>
							{{ info ? info.owner.name : '' }}
							{{ info ? info.owner.last_name : '' }}
						</p>
						<b-skeleton v-else />
					</div>
					<div class="price-space-description">
						<p
							v-if="owner"
							class="price-space-info-text"
						>
							{{ text }}
						</p>
						<b-skeleton v-else />
					</div>
					<div class="price-space-contact">
						<p
							v-if="owner"
							class="price-space-contact-text"
						>
							{{ managerPhone }}
						</p>
						<b-skeleton v-else />
						<a
							class="price-space-button"
							:href="'https://wa.me/' + managerPlainPhone"
							target="_blank"
							rel="noopener noreferrer"
						>
							<WhatsAppIcon />
						</a>
						<PricingSpaceMessage />
					</div>
				</div>
			</div>
		</div>
	</div>
</template>


<script>
import WhatsAppIcon from '../pages/Pricing/assets/WhatsAppIcon.vue';
import { mapActions, mapState } from 'pinia';
import { usePricingStore } from '../../stores/Pricing';
import { useModalStore } from '../../stores/Modal';
import { usePricingPeriodStore } from '../../stores/PricingPeriod';
import PricingSpaceMessage from '../pages/Pricing/PricingSpaceMessage.vue';

export default {
	name: 'PriceSpace',
	components: { PricingSpaceMessage, WhatsAppIcon },
	data() {
		return {
			names: {
				null: 'Бесплатный',
				base: 'База',
				standard: 'Стандарт',
				pro: 'PRO',
			},
			info: [],
			owner:[],
			tariff:[],
			current: []
		};
	},
	computed: {
		...mapState(usePricingStore, ['current', 'items', 'manager']),
		...mapState(usePricingStore, ['manager']),
		//  эт для ватсапа сделано

		managerPlainPhone(){
			if(!this.info) return ''
			else if (this.owner && this.info.owner.phone.startsWith('8')) {
				return  this.info.owner.phone.replace(/^8/, '+7');
			}
			else if (this.info && !this.info.owner.phone.includes('+')) {
				return	this.updatePhoneIfNeeded()
			}
			return this.info.owner.phone
		},
		// 	чтоб красиво было тип такого +7 778 548-67-59
		managerPhone() {
			if (!this.info || !this.owner || !this.info.owner.phone) return '';

			const phone = this.info.owner.phone;
			return this.formatPhoneNumber(phone);
		},


		ownerInfo() {
			return newValue => [...this.owner, newValue.owner];
		},
		tariffInfo() {
			return newValue => [...this.tariff, newValue.tariff];
		},
		title(){
			if(!this.ownerInfo) return ''
			return 'Ваш персональный менеджер'
		},
		text(){
			if(!this.ownerInfo) return ''
			return 'Я здесь и помогу по любому вопросу 😊'
		},
		totalUsers() {
			if (!this.info) return 0;
			return this.info.users_count
		},
	},
	watch:{
		info(newValue){
			this.tariffUpdate(newValue.tariff)
			this.ownerUpdate(newValue.owner)
		}
	},
	created() {
		this.fetchManager();
		this.infoFetch();
		this.currentFetch()
	},
	mounted(){

	},
	methods: {
		...mapActions(usePricingPeriodStore, ['connectedTariff']),
		...mapActions(useModalStore, ['setCurrentModal', 'removeModalActive']),
		...mapActions(usePricingStore, ['fetchManager']),
		ownerUpdate(newValue) {
			this.owner = [...this.owner, newValue.owner];
		},
		formatPhoneNumber(phone) {
			// Удаляем все символы, кроме цифр
			const cleaned = ('' + phone).replace(/\D/g, '');

			// Разбиваем номер на части
			const match = cleaned.match(/^(\d{1})(\d{3})(\d{3})(\d{2})(\d{2})$/);

			if (match) {
				// Форматируем номер телефона
				return `${match[1]} ${match[2]} ${match[3]}-${match[4]}-${match[5]}`;
			}

			return phone;
		},
		tariffUpdate(newValue) {
			this.tariff = [...this.tariff, newValue.tariff];
		},
		updatePhoneIfNeeded() {
			if (!this.info.owner.phone.includes('+')) {
				this.info.owner.phone = '+' + this.info.owner.phone;
			}
		},
		fff() {},
		infoFetch(){
			this.axios('tariff/subscriptions').then((response) => {
				this.info = response.data.data
				this.managerPlainPhone()
			})
		},
		currentFetch(){
			this.axios.get('/portal/current').then(res => {
				this.current = res.data.data
			})
		},


		editRate() {
			if (this.info.tariff) this.connectedTariff(this.info.tariff.kind)

			this.setCurrentModal('editRate')
		}
	},
};
</script>

<style scoped>
@media (min-width: 1600px) {
	.price-space {
		width: 1195px !important;
	}

	.price-space-title {
		font-size: 36px !important;
		line-height: 54px !important;
	}

	.price-space-image {
		max-width: 164px !important;
		border-radius: 16px !important;
	}

	.price-space-title-block {
		font-size: 20px !important;
		line-height: 30px !important;
		margin-bottom: 8px !important;
	}

	.price-space-text-name {
		font-size: 16px !important;
	}

	.price-space-name-data {
		font-size: 16px !important;
	}

	.price-space-info {
		max-width: 358px !important;
	}

	.price-space-buttons{
			flex-direction: row !important;
			align-items: center !important;
	}

	.price-space-buttons button {
		padding: 5px 24px !important;
		font-size: 16px !important;
		gap: 12px !important;
	}

	.price-space-buttons p {
		font-size: 16px !important;

	}
}

.price-space {
	width: 900px;
}

.price-space-title {
	font-size: 30px;
	font-weight: 600;
	line-height: 40px;
		color: #333333;
}

.price-space-content {
	display: flex;
	gap: 14px;
}

.price-space-image {
	max-width: 134px;
	width: 100%;
	border-radius: 12px;
}

.price-space-title-block {
	font-size: 18px;
	font-weight: 600;
	line-height: 28px;
	display: flex;
		gap: 5px;
}

.price-space-text-content {
	margin-top: 8px;
	display: flex;
	gap: 4px;
}

.price-space-text-name {
	color: #737b8a;
	font-size: 14px;
	font-weight: 400;
}

.price-space-name-data {
	font-size: 14px;
}

.price-space-info-blocks {
	display: flex;
	justify-content: space-between;
	margin-top: 24px;
}

.price-space-info {
	display: flex;
	flex-direction: column;
	max-width: 318px;
	width: 100%;
}
.price-space-info-text {
	display: flex;
	flex-direction: column;
	max-width: 318px;
	width: 100%;
	color: #737B8A;
	font-size: 14px;
	gap: 5px;
}
.price-space-contact {
	margin-top: auto;
	display: flex;
	gap: 12px;
	align-items: center;
		font-weight: 500;
}
.price-space-contact-text {
	display: flex;
	gap: 12px;
	align-items: center;
		font-weight: 500;
		font-size: 14px;
}

.price-space-button {
	background-color: #ededed;
	width: 40px;
	height: 40px;
	border-radius: 8px;
	padding: 8px;
}
.price-space-button:hover {
	background-color: #bbbbbb;
}

.price-space-buttons {
	margin-top: auto;
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	gap: 5px;
}

.price-space-buttons button {
	font-size: 14px;
	padding: 5px 16px;
	outline: none;
	color: white;
	background-color: #0c50ff;
	border-radius: 8px;
}

.price-space-buttons button:hover {

	color: white;
	background-color: #0839b6;

}

.price-space-buttons p {
		font-size: 13px;
	color: #0c50ff;
	font-weight: 700;
	cursor: pointer;
	width: 100%;
}
.price-space-buttons p:hover {
	color: #0d3cb2;
}
</style>
