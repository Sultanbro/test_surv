<template>
	<div class="UserEditDocumentsV2">
		<div
			v-if="userId"
			class="UserEditDocumentsV2-docs"
		>
			<div
				v-for="doc, index in documents"
				:key="index"
				class="UserEditDocumentsV2-doc p-2"
			>
				<div class="UserEditDocumentsV2-docIcon">
					<img
						src="/icon/doc-pdf.png"
						alt="pdf"
						width="24"
					>
				</div>
				<a
					:href="`/signature/view?user=${userId}&doc=${doc.id}`"
					target="_blank"
					class="UserEditDocumentsV2-docName"
				>
					{{ doc.name }}
				</a>
				<div class="UserEditDocumentsV2-docControls">
					<template v-if="doc.signed">
						<i class="fas fa-check" />
						Подписан
					</template>
				</div>
			</div>
		</div>
		<div
			v-else
			class="UserEditDocumentsV2-nouser"
		>
			Список документов появится после создания пользователя
		</div>
	</div>
</template>

<script>
export default {
	name: 'UserEditDocumentsV2',
	components: {},
	props: {
		userId: {
			type: Number,
			default: 0,
		}
	},
	data(){
		return {
			documents: [],
		}
	},
	computed: {},
	watch: {
		userId(){
			this.fetchDocs()
		},
	},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {
		clearDocs(){
			this.documents = []
		},
		async fetchDocs(){
			if(!this.userId) return this.clearDocs()
			try {
				const {data} = await this.axios.get(`/signature/users/${this.userId}/files`)
				const docs = data.data || []
				this.documents = docs.map(doc => ({
					id: doc.id,
					name: doc.original_name || 'Без названия',
					file: doc.url,
					signed: doc.signed_at,
				}))
			}
			catch (error) {
				console.error(error)
			}
		},
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
