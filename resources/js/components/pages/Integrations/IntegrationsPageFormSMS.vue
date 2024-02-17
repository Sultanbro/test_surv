<template>
	<div class="IntegrationsPageForm IntegrationsPageFormSMS">
		<!-- Адрес -->
		<label class="IntegrationsPageForm-row">
			<span class="IntegrationsPageForm-label">
				API ID
			</span>
			<span class="IntegrationsPageForm-control">
				<b-form-input
					v-model="apiId"
					type="text"
				/>
			</span>
		</label>

		<!-- ключ -->
		<label class="IntegrationsPageForm-row">
			<span class="IntegrationsPageForm-label">
				API KEY
			</span>
			<span class="IntegrationsPageForm-control">
				<b-form-input
					v-model="apiKey"
					type="text"
				/>
			</span>
		</label>

		<hr class="my-5">
		<b-button
			variant="primary"
			@click="onSave"
		>
			Сохранить
		</b-button>
	</div>
</template>

<script>
export default {
	name: 'IntegrationsPageFormSMS',
	components: {},
	props: {
		data: {
			type: Object,
			required: true
		}
	},
	data(){
		return {
			apiId: this.data.apiId,
			apiKey: this.data.apiKey,
		}
	},
	methods: {
		onSave(){
			this.$emit('save', {
				apiId: this.apiId,
				apiKey: this.apiKey,
			})
		}
	}
}
</script>

<style>
</style>
