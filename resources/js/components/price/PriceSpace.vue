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
					<div class="price-space-title-block">
						1suol9rbcn
					</div>
					<div class="price-space-text-content">
						<p class="price-space-text-name">
							Тариф:
						</p>
						<p class="price-space-name-data">
							<template v-if="current">
								{{ names[current.tariff.kind] }}
							</template>
							<b-skeleton v-else />
						</p>
					</div>
					<div class="price-space-text-content">
						<p class="price-space-text-name">
							Пользователей:
						</p>
						<p
							v-if="current"
							class="price-space-name-data"
						>
							4 из {{ totalUsers }}
						</p>
						<b-skeleton v-else />
					</div>
					<div class="price-space-buttons">
						<button>Продлить</button>
						<p @click="editRate">
							Управление тарифом
						</p>
					</div>
				</div>
			</div>
			<div class="price-space-content">
				<div class="price-space-image">
					<img
						class="price-space-image"
						:src="manager && manager.photo ? manager.photo : '/images/price/AvatarManager.png'"
					>
				</div>
				<div class="price-space-info">
					<div class="price-space-info">
						{{ title }}
					</div>
					<div class="price-space-title-block">
						{{ manager ? manager.name : '' }} {{ manager ? manager.lastName : '' }}
					</div>
					<div class="price-space-description">
						{{ text }}
					</div>
					<div class="price-space-contact">
						<p>+7 778 548-67-59</p>
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
	components: {PricingSpaceMessage, WhatsAppIcon },
	data() {
		return {
			names: {
				free: 'Бесплатный',
				base: 'База',
				standard: 'Стандарт',
				pro: 'PRO',
			},
		};
	},
	computed: {
		...mapState(usePricingStore, ['current', 'items', 'manager']),
		...mapState(usePricingStore, ['manager']),

		managerPlainPhone(){
			if(!this.manager) return ''
			return this.manager.phone.replace(/[^\d+]/g, '')
		},
		title(){
			if(!this.manager) return ''
			return this.manager.title || 'Ваш персональный менеджер'
		},
		text(){
			if(!this.manager) return ''
			return this.manager.text || 'Я здесь и помогу по любому вопросу 😊'
		},
		totalUsers() {
			if (!this.current) return 0;
			return (
				(this.current.extra_user_limit || 0) +
				(this.current.tariff.users_limit || 0)
			);
		},
	},
	created() {
		this.fetchManager();
	},

	methods: {
		...mapActions(usePricingPeriodStore, ['connectedTariff']),
		...mapActions(useModalStore, ['setCurrentModal', 'removeModalActive']),
		...mapActions(usePricingStore, ['fetchManager']),

		fff() {},

		editRate() {
			this.connectedTariff('PRO')
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

	.price-space-buttons button {
		padding: 5px 24px !important;
		font-size: 16px !important;

	}

	.price-space-buttons p {
		font-size: 16px !important;

	}
}

.price-space {
	width: 1000px;
}

.price-space-title {
	font-size: 32px;
	font-weight: 600;
	line-height: 46px;
}

.price-space-content {
	display: flex;
	gap: 14px;
}

.price-space-image {
	max-width: 144px;
	width: 100%;
	border-radius: 12px;
}

.price-space-title-block {
	font-size: 18px;
	font-weight: 600;
	line-height: 28px;
	margin-bottom: 8px;
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

.price-space-contact {
	margin-top: auto;
	display: flex;
	gap: 12px;
	align-items: center;
}

.price-space-button {
	background-color: #ededed;
	width: 40px;
	height: 40px;
	border-radius: 8px;
	padding: 8px;
}

.price-space-buttons {
	margin-top: 16px;
	display: flex;
	align-items: center;
	gap: 12px;
}

.price-space-buttons button {
	font-size: 14px;
	padding: 5px 24px;
	outline: none;
	color: white;
	background-color: #0c50ff;
	border-radius: 8px;
}

.price-space-buttons p {
		font-size: 14px;
	color: #0c50ff;
	font-weight: 700;
	cursor: pointer;
	width: 100%;
}
</style>
