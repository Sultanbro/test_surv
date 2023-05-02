<template>
	<div class="IntegrationsPageForm IntegrationsPageFormBitrix">
		<!-- Адрес -->
		<label class="IntegrationsPageForm-row">
			<span class="IntegrationsPageForm-label">
				Адрес портала
			</span>
			<span class="IntegrationsPageForm-control">
				<b-form-input
					type="text"
					v-model="domain"
				/>
			</span>
		</label>

		<!-- ключ -->
		<label class="IntegrationsPageForm-row">
			<span class="IntegrationsPageForm-label">
				Ключ OAuth
			</span>
			<span class="IntegrationsPageForm-control">
				<b-form-input
					type="text"
					v-model="oauth"
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
	name: 'IntegrationsPageFormBitrix',
	components: {},
	data(){
		return {
			domain: '',
			oauth: '',
		}
	},
	methods: {
		onSave(){
			this.$emit('save', {
				domain: this.domain,
				oauth: this.oauth,
			})
		}
	}
}
</script>

<style>
</style>
