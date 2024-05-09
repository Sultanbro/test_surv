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
						:src="manager.photo || '/images/price/AvatarManager.png'"
					>
				</div>
				<div class="price-space-info">
					<div class="price-space-info">
						Ваш персональный менеджер
					</div>
					<div class="price-space-title-block">
						{{ manager.name }} {{ manager.lastName }}
					</div>
					<div class="price-space-description">
						Я здесь и готова помочь по любому вопросу 😊
					</div>
					<div class="price-space-contact">
						<p>+7 778 548-67-59</p>
						<button
							class="price-space-button"
							@click="fff"
						>
							<WhatsAppIcon />
						</button>
						<button class="price-space-button">
							<MessageIcon />
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import WhatsAppIcon from '../pages/Pricing/assets/WhatsAppIcon.vue';
import MessageIcon from '../pages/Pricing/assets/MessageIcon.vue';
import { mapActions, mapState } from 'pinia';
import { usePricingStore } from '../../stores/Pricing';
import { useModalStore } from '../../stores/Modal';
import { usePricingPeriodStore } from '../../stores/PricingPeriod';

export default {
	name: 'PriceSpace',
	components: { MessageIcon, WhatsAppIcon },
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
.price-space {
	width: 1195px;
}

.price-space-title {
	font-size: 44px;
	font-weight: 600;
	line-height: 54px;
}

.price-space-content {
	display: flex;
	gap: 24px;
}

.price-space-image {
	max-width: 164px;
	width: 100%;
	border-radius: 16px;
}

.price-space-title-block {
	font-size: 20px;
	font-weight: 600;
	line-height: 30px;
	margin-bottom: 8px;
}

.price-space-text-content {
	margin-top: 8px;
	display: flex;
	gap: 4px;
}

.price-space-text-name {
	color: #737b8a;
	font-size: 16px;
	font-weight: 400;
}

.price-space-name-data {
	font-size: 16px;
}
.price-space-info-blocks {
	display: flex;
	justify-content: space-between;
	margin-top: 24px;
}

.price-space-info {
	display: flex;
	flex-direction: column;
	max-width: 358px;
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

.price-space-buttons{
  margin-top: 30px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.price-space-buttons button {
  width: 156px;
  height: 32px;
  padding: 5px 24px;
  outline: none;
  color: white;
  background-color: #0C50FF;
  border-radius: 8px;
}

.price-space-buttons p {
  color: #0C50FF;
  font-weight: 700;
  cursor: pointer;
  width: 100%;
}
</style>
