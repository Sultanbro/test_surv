<template>
	<div class="IntegrationsPage">
		<IntegrationsPageItem
			title="Bitrix24"
			name="bitrix"
			:status="status.bitrix"
			@click="selectedIntegration = 'bitrix'"
		/>
		<IntegrationsPageItem
			title="AmoCRM"
			name="amo"
			:status="false"
		/>
		<IntegrationsPageItem
			title="Callibro"
			name="callibro"
			:status="false"
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
		</SideBar>
	</div>
</template>

<script>
import IntegrationsPageFormBitrix from '@/components/pages/Integrations/IntegrationsPageFormBitrix'
import IntegrationsPageItem from '@/components/pages/Integrations/IntegrationsPageItem'
import SideBar from '@ui/Sidebar'

export default {
	name: 'IntegrationsPage',
	components: {
		IntegrationsPageItem,
		IntegrationsPageFormBitrix,
		SideBar,
	},
	data(){
		return {
			selectedIntegration: null,
			status: {
				bitrix: false
			}
		}
	},
	methods: {
		onSaveBitrix(){
			// do staff
			this.status.bitrix = true
			this.selectedIntegration = null
		}
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
