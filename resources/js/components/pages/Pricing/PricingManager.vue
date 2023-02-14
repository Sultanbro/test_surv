<template>
	<b-row
		align-v="center"
		class="PricingManager"
	>
		<b-col
			cols="6"
			xl="4"
		>
			<div class="PricingManager-card">
				<template v-if="manager">
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
								:href="'tel:' + managerPlainPhone"
								class="PricingManager-link"
							>{{ manager.phone }}</a>
						</div>
						<div class="PricingManager-email">
							<a
								:href="'mailto:' + manager.email"
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
					{{ manager.title }}
				</div>
				<div class="PricingManager-text">
					{{ manager.text }}
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
		}
	},
	created(){
		// this.fetchManager()
		setTimeout(() => { this.fetchManager() }, 1500) // timeout for imitate net lag
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
	// &-phone{}
	// &-email{}
	&-link{
		color: green;
		text-decoration: none;
		&:hover{
			color: lightgreen;
		}
	}
}
</style>
