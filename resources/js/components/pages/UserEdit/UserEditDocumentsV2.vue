<template>
	<div class="UserEditDocumentsV2">
		<div
			v-if="documents.length"
			class="UserEditDocumentsV2-docs"
		>
			<div
				v-for="doc, index in documents"
				:key="index"
				class="UserEditDocumentsV2-doc p-2"
			>
				<div class="UserEditDocumentsV2-docIcon">
					<i class="fa fa-file-pdf" />
				</div>
				<div class="UserEditDocumentsV2-docName">
					{{ doc.name }}
				</div>
				<div class="UserEditDocumentsV2-docControls">
					<template v-if="doc.signed">
						<i class="fas fa-check" />
						Подписан
					</template>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'UserEditDocumentsV2',
	components: {},
	props: {},
	data(){
		return {
			documents: [
				{
					id: 0,
					name: 'NDA',
					file: ''
				},
				{
					id: 0,
					name: 'TD',
					file: '',
					signed: true
				},
			],
		}
	},
	computed: {},
	watch: {},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {
		async fetchDocs(){},
	},
}
</script>

<style lang="scss">
.UserEditDocumentsV2{
	&-doc{
		display: flex;
		align-items: center;
		gap: 10px;
		&:hover{
			background-color: rgba(#777, 0.25);
		}
	}
	&-docIcon{
		flex: 0 0 32px;
		font-size: 24px;
	}
	&-docName{
		flex: 1;
	}
	&-docControl{
		display: flex;
		align-items: center;
		gap: 10px;
	}
}
</style>
