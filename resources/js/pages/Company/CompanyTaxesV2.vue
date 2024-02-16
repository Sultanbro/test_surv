<template>
	<div class="CompanyTaxes">
		<b-row class="mb-4">
			<b-col>
				<JobtronButton
					small
					@click="newTax"
				>
					Добавить налог
				</JobtronButton>
			</b-col>
		</b-row>

		<table
			v-if="taxes.length"
			class="CompanyTaxes-table"
		>
			<thead class="CompanyTaxes-thead">
				<tr class="CompanyTaxes-headRow">
					<th
						v-for="field in fields"
						:key="field.key"
						class="CompanyTaxes-th"
					>
						{{ field.title }}
					</th>
				</tr>
			</thead>
			<tbody class="CompanyTaxes-tbody">
				<template v-for="tax, index in taxes">
					<tr
						:key="tax.id"
						class="CompanyTaxes-row"
					>
						<td
							v-for="field in fields"
							:key="field.key"
							class="CompanyTaxes-td"
						>
							<template v-if="field.key === 'actions'">
								<b-button
									class="btn btn-primary btn-icon"
									@click.stop="editTax(tax)"
								>
									<i class="fa fa-edit" />
								</b-button>
								<b-button
									class="btn btn-danger btn-icon"
									@click.stop="removeTax(tax)"
								>
									<i class="fa fa-trash" />
								</b-button>
							</template>
							<template v-else-if="field.key === 'index'">
								<span :title="tax.id">
									{{ index }}
								</span>
							</template>
							<template v-else-if="field.key === 'created_at'">
								{{ $moment.utc(tax.created_at).local().format('DD.MM.YYYY HH:mm') }}
							</template>
							<template v-else-if="field.key.includes('value')">
								<template v-if="tax.items[field.item]">
									{{ tax.items[field.item].value }}{{ tax.items[field.item].isPercent ? '%' : '' }}
									<img
										v-if="tax.items[field.item].end_subtraction"
										v-b-popover.click.blur.html="'Данный процент или сумма будет вычитаться от остаточной суммы которая к начислению после вычетов указанных выше'"
										src="/images/dist/profit-info.svg"
										class="img-info"
										alt="info icon"
										width="20"
										tabindex="-1"
									>
								</template>
							</template>
							<template v-else-if="field.key.includes('tax')">
								<template v-if="tax.items[field.item]">
									{{ tax.items[field.item].name }}
								</template>
							</template>
							<template v-else>
								{{ tax[field.key] }}
							</template>
						</td>
					</tr>
				</template>
			</tbody>
		</table>

		<div
			v-else
			class=""
		>
			Нет налогов
		</div>

		<SideBar
			v-if="editedTax"
			title="Настройки"
			width="600px"
			:open="!!editedTax"
			@close="editedTax = null"
		>
			<TexesEditForm
				:tax="editedTax"
				@submit="saveTax"
			/>
		</SideBar>
	</div>
</template>

<script>

import JobtronButton from '@ui/Button.vue'
import SideBar from '@ui/Sidebar.vue'
import TexesEditForm from './TexesEditForm.vue'

export default {
	name: 'CompanyTaxes',
	components: {
		JobtronButton,
		SideBar,
		TexesEditForm,
	},
	props: {},
	data(){
		return {
			taxes: [],
			editedTax: null,
		}
	},
	computed: {
		maxItems(){
			return this.taxes.reduce((max, tax) => Math.max(max, tax.items.length), 0)
		},
		fields(){
			const fields = [
				{
					key: 'index',
					title: '',
				},
				{
					key: 'name',
					title: 'Название',
				},
			]
			Array.from({ length: this.maxItems }, (_, i) => {
				fields.push({
					key: 'tax' + i,
					title: 'Налог',
					item: i,
				})
				fields.push({
					key: 'value' + i,
					title: 'Сумма/Процент',
					item: i,
				})
			})
			fields.push({
				key: 'created_at',
				title: 'Дата создания',
			})
			fields.push({
				key: 'actions',
				title: '',
			})
			return fields
		}
	},
	watch: {},
	created(){},
	mounted(){
		this.fetchTaxes()
	},
	beforeDestroy(){},
	methods: {
		async fetchTaxes(){
			const loader = this.$loading.show()

			try {
				const { data } = await this.axios.get('/taxes')
				this.taxes = data.data
			}
			catch (error) {
				window.onerror && window.onerror(error)
				console.error(error)
				this.$toast.error('Не удалось получить список налогов')
			}

			loader.hide()
		},

		async removeTax(tax){
			if(!confirm('Вы уверены что хотите удалить налог?')) return

			const loader = this.$loading.show()
			try {
				await this.axios.delete(`/taxes/${tax.id}`)
				this.fetchTaxes()
				this.$toast.success('Группа налогов удалена')
			}
			catch (error) {
				window.onerror && window.onerror(error)
				console.error(error)
				this.$toast.error('Ошибка при удалении')
			}
			loader.hide()
		},

		async createTax(tax){
			const loader = this.$loading.show()
			try {
				await this.axios.post('/taxes', tax)
				this.fetchTaxes()
				this.clearForm()
				this.$toast.success('Группа налогов создана')
			}
			catch (error) {
				window.onerror && window.onerror(error)
				console.error(error)
				this.$toast.error('Ошибка при создании')
			}
			loader.hide()
		},

		async updateTax(tax){
			const loader = this.$loading.show()
			try {
				await this.axios.put(`/taxes/${tax.id}`, tax)
				this.fetchTaxes()
				this.clearForm()
				this.$toast.success('Группа налогов сохранена')
			}
			catch (error) {
				window.onerror && window.onerror(error)
				console.error(error)
				this.$toast.error('Ошибка при сохранении')
			}
			loader.hide()
		},

		saveTax(tax){
			if(tax.id > 0) return this.updateTax(tax)
			this.createTax(tax)
		},

		editTax(tax){
			this.editedTax = tax
		},

		newTax(){
			this.editedTax = {
				name: '',
				items: []
			}
		},

		clearForm(){
			this.editedTax = null
		},
	},
}
</script>

<style lang="scss">
.CompanyTaxes{
	&-table{
		width: 100%;
	}
	&-row{
		&:hover{
			background-color: #f5f5f5;
		}
	}
	&-td{
		vertical-align: middle;
	}
}
</style>
