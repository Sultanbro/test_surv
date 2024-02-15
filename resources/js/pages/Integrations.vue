<template>
	<div class="IntegrationsPage">
		<IntegrationsPageItem
			title="Bitrix24"
			name="bitrix"
			:status="status.bitrix"
		/>
		<IntegrationsPageItem
			title="AmoCRM"
			name="amo"
			:status="false"
		/>
		<!-- <IntegrationsPageItem
			title="Callibro"
			name="callibro"
			:status="false"
		/> -->

		<IntegrationsPageItem
			title="u-call"
			name="sms"
			:status="status.sms"
			@click="selectedIntegration = 'sms'"
		/>

		<SideBar
			width="50%"
			title=""
			:open="selectedIntegration"
			@close="selectedIntegration = null"
		>
			<IntegrationsPageFormBitrix
				v-if="selectedIntegration === 'bitrix'"
				@save="onSaveBitrix"
			/>
			<IntegrationsPageFormSMS
				v-if="selectedIntegration === 'sms'"
				:data="data.sms"
				@save="onSaveSMS"
			/>
		</SideBar>
	</div>
</template>

<script>
import IntegrationsPageFormBitrix from '@/components/pages/Integrations/IntegrationsPageFormBitrix'
import IntegrationsPageFormSMS from '@/components/pages/Integrations/IntegrationsPageFormSMS'
import IntegrationsPageItem from '@/components/pages/Integrations/IntegrationsPageItem'
import SideBar from '@ui/Sidebar'

export default {
	name: 'IntegrationsPage',
	components: {
		IntegrationsPageItem,
		IntegrationsPageFormBitrix,
		IntegrationsPageFormSMS,
		SideBar,
	},
	data(){
		return {
			selectedIntegration: null,
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
	methods: {
		async fetchIntegrations(){
			const {data} = await this.axios.get('/signature/integrations')
			this.status.sms = data.data
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
			this.status.bitrix = true
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
				this.status.sms = true
				this.selectedIntegration = null
				this.$toast.error('Интегрция сохранена')
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
