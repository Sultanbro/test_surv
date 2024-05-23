<template>
	<b-row
		v-if="manager"
		align-v="center"
		class="PricingManager"
	>
		<b-col
			cols="6"
			xl="4"
		>
			<div class="PricingManager-card">
				<template v-if="manager.id">
					<div class="PricingManager-photo">
						<img
							:src="manager.photo"
							alt=""
							class="PricingManager-img"
						>
					</div>
					<div class="PricingManager-info">
						<div class="PricingManager-name">
							{{ manager.lastName }} {{ manager.name }}
						</div>
						<div class="PricingManager-phone">
							<a
								:href="'https://wa.me/' + managerPlainPhone"
								title="Позвонить в ватсап"
								class="PricingManager-link"
							>{{ manager.phone }}</a>
						</div>
						<div class="PricingManager-email">
							<a
								:href="'mailto:' + manager.email"
								title="Написать на почту"
								class="PricingManager-link"
							>{{ manager.email }}</a>
						</div>
					</div>
				</template>
				<template v-else>
					<div class="PricingManager-photo">
						<b-skeleton-img no-aspect />
					</div>
					<div class="PricingManager-info">
						<b-skeleton height="1.5em" />
						<b-skeleton />
						<b-skeleton />
					</div>
				</template>
			</div>
		</b-col>
		<b-col
			cols="6"
			xl="8"
		>
			<template v-if="manager">
				<div class="PricingManager-title mb-4">
					{{ title }}
				</div>
				<div class="PricingManager-text">
					{{ text }}
				</div>
			</template>
			<template v-else>
				<div class="PricingManager-title mb-4">
					<b-skeleton />
				</div>
				<div class="PricingManager-text">
					<b-skeleton />
				</div>
			</template>
		</b-col>
	</b-row>
</template>

<script>
import { mapActions, mapState } from 'pinia'
import { usePricingStore } from '@/stores/Pricing'
export default {
	name: 'PricingManager',
	components: {},
	computed: {
		...mapState(usePricingStore, ['manager']),
		managerPlainPhone(){
			if(!this.manager) return ''
			return this.manager.phone.replace(/[^\d+]/g, '')
		},
		title(){
			if(!this.manager) return ''
			return this.manager.title || 'Приветствую!'
		},
		text(){
			if(!this.manager) return ''
			return this.manager.text || 'Обратитесь ко мне за консультацией по оплате сервиса и по выбору тарифа. Спасибо.'
		},
	},

	created(){
		this.fetchManager()
	},
	methods: {
		...mapActions(usePricingStore, ['fetchManager'])
	}
};
</script>

<style lang="scss" scoped>
.PricingManager{
	&-card{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		gap: 2rem;
	}
	&-photo{
		flex: 0 0 15rem;
		.b-skeleton-img{
			width: 15rem;
			height: 15rem;
			border-radius: 1rem;
		}
	}
	&-img{
		width: 15rem;
		height: 15rem;
		border-radius: 1rem;
	}
	&-info{
		flex: 1 1 content;
		display: flex;
		flex-flow: column nowrap;
		gap: 1rem;
	}
	&-name{
		font-size: 1.5em;
	}
	&-phone{
		.PricingManager{
			&-link{
				padding-left: 28px;
				background: url(/static/img/whatsapp64.png) left center no-repeat;
				background-size: contain;
			}
		}
	}
	&-email{
		.PricingManager{
			&-link{
				padding-left: 28px;
				background: url(/static/img/email.svg) left center no-repeat;
				background-size: contain;
			}
		}
	}
	&-link{
		color: #3361FF;
		text-decoration: none;
		&:hover{
			color: lighten(#3361FF, 10);
		}
	}
}
</style>
