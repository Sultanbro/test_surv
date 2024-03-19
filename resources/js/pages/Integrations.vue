<template>
	<div class="IntegrationsPage">
		<IntegrationsPageItem
			v-for="item, key in integrations"
			:key="key"
			:name="key"
			:title="item.title"
			:icon="item.icon"
			:status="item.status"
			@click="selectedIntegration = key"
		/>

		<!-- <SideBar
			width="50%"
			:title="selectedIntegration ? integrations[selectedIntegration].title : ''"
			:open="selectedIntegration"
			@close="selectedIntegration = null"
		>
			<IntegrationsPageFormBitrix
				v-if="selectedIntegration === 'bitrix'"
				@save="onSaveBitrix"
			/>
		</SideBar> -->
		<IntegrationsPageFormSMS
			:open="selectedIntegration === 'sms'"
			:data="data.sms"
			@save="onSaveSMS"
			@close="selectedIntegration = ''"
		/>
	</div>
</template>

<script>
// import IntegrationsPageFormBitrix from '@/components/pages/Integrations/IntegrationsPageFormBitrix'
import IntegrationsPageFormSMS from '@/components/pages/Integrations/IntegrationsPageFormSMS'
import IntegrationsPageItem from '@/components/pages/Integrations/IntegrationsPageItem'
// import SideBar from '@ui/Sidebar'

export default {
	name: 'IntegrationsPage',
	components: {
		IntegrationsPageItem,
		// IntegrationsPageFormBitrix,
		IntegrationsPageFormSMS,
		// SideBar,
	},
	data(){
		return {
			selectedIntegration: null,
			integrations: {
				bitrix: {
					title: 'Bitrix24',
					icon: '',
					status: false,
				},
				amocrm: {
					title: 'AmoCRM',
					icon: '',
					status: false,
				},
				sms: {
					title: 'СМС интеграция',
					icon: 'https://u-marketing.org/images/logou2.png',
					status: false,
				},
				// callibro: {
				// 	title: 'Callibro',
				// 	icon: '',
				// 	status: false,
				// },
			},
			status: {
				bitrix: false,
				sms: false,
			},
			data: {
				sms: {
					apiId: '',
					apiKey: '',
				}
			}
		}
	},
	mounted(){
		this.fetchIntegrations()
	},
	methods: {
		async fetchIntegrations(){
			const {data} = await this.axios.get('/signature/integrations')
			this.integrations.sms.status = !!data.data
			if(data.data){
				const json = JSON.parse(data.data.data)
				this.data.sms = {
					apiId: json.app_id,
					apiKey: json.api_key,
				}
			}
		},
		onSaveBitrix(){
			// do staff
			this.integrations.bitrix.status = true
			this.selectedIntegration = null
		},
		async onSaveSMS({apiId, apiKey}){
			/* eslint-disable camelcase */
			try {
				await this.axios.post('/signature/integrations', {
					app_id: apiId,
					api_key: apiKey,
				})
				this.data.sms = {
					apiId,
					apiKey,
				}
				this.integrations.sms.status = true
				this.selectedIntegration = null
				this.$toast.success('Интегрция сохранена')
			}
			catch (error) {
				this.$toast.error('Не удалось сохранить интеграцию')
			}
			/* eslint-enable camelcase */
		},
	}
}
</script>

<style lang="scss">
.IntegrationsPage{
	display: flex;
	flex-flow: row wrap;
	align-items: flex-start;
	justify-content: flex-start;
	gap: 10px;

	.ui-sidebar.is-open .ui-sidebar__body{
		right: 60px;
	}
}

.IntegrationsPageForm{
	&-row{
		display: flex;
		align-items: flex-start;
		justify-content: flex-start;
	}
	&-label{
		flex: 0 0 30%;

		font-size: 1.4rem;
    line-height: 2rem;
	}
	&-control{
		flex: 1;
	}
}
</style>
