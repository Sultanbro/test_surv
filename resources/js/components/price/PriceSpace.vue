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
					<div class="price-space-buttons">
						<button>Продлить</button>
						<p
							v-if="info.tariff"
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
						:src="manager && manager.photo ? manager.photo : '/images/price/AvatarManager.png'"
					>
				</div>
				<div class="price-space-info">
					<div class="price-space-info">
						<p v-if="owner">
							{{ title }}
						</p>
						<b-skeleton v-else />
					</div>



					<div class="price-space-title-block">
						<p v-if="info">
							{{ info ? info.owner.full_name : '' }}
						</p>
						<b-skeleton v-else />
					</div>
					<div class="price-space-description">
						<p v-if="owner">
							{{ text }}
						</p>
						<b-skeleton v-else />
					</div>
					<div class="price-space-contact">
						<p v-if="owner">
							{{ managerPlainPhone }}
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
	components: {PricingSpaceMessage, WhatsAppIcon },
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
			this.axios('tariffs/subscriptions').then((response) => {
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
	width: 900px;
}

.price-space-title {
	font-size: 30px;
	font-weight: 600;
	line-height: 40px;
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
	font-size: 16px;
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
	margin-top: 8px;
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
