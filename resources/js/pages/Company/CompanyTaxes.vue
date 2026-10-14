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

		<table class="CompanyTaxes-table">
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
						v-if="tax.edit"
						:key="'e' + tax.id"
						class="CompanyTaxes-row"
					>
						<td class="CompanyTaxes-td">
							{{ index }}
						</td>
						<td class="CompanyTaxes-td">
							<InputText
								v-model="taxForm.name"
								small
								placeholder="Введите название налога"
							/>
						</td>
						<td class="CompanyTaxes-td">
							<b-form-checkbox
								v-model="taxForm.isPercent"
								switch
							/>
						</td>
						<td class="CompanyTaxes-td">
							<b-form-checkbox
								v-model="taxForm.endSubtraction"
								switch
							/>
						</td>
						<td class="CompanyTaxes-td">
							<InputText
								v-model="taxForm.value"
								small
								placeholder="Введите сумму/процент налога"
							/>
						</td>
						<td class="CompanyTaxes-td">
							<b-button
								class="btn btn-success btn-icon"
								@click.stop="saveTax(tax)"
							>
								<i class="fa fa-save" />
							</b-button>
							<b-button
								class="btn btn-warning btn-icon"
								@click.stop="editTax()"
							>
								<i class="fa fa-times" />
							</b-button>
						</td>
					</tr>
					<tr
						v-else
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
							<template v-else-if="field.key === 'isPercent'">
								{{ tax[field.key] ? 'да' : 'нет' }}
							</template>
							<template v-else-if="field.key === 'endSubtraction'">
								{{ tax[field.key] ? 'да' : 'нет' }}
							</template>
							<template v-else-if="field.key === 'value'">
								{{ tax[field.key] }}{{ tax.isPercent ? '%' : '' }}
							</template>
							<template v-else>
								{{ tax[field.key] }}
							</template>
						</td>
					</tr>
				</template>
			</tbody>
		</table>
	</div>
</template>

<script>
import InputText from '@ui/InputText.vue'
import JobtronButton from '@ui/Button.vue'

const methodField = '_method'
const percentField = 'is_percent'
const endSubtractionField = 'end_subtraction'
let newid = 0

export default {
	name: 'CompanyTaxes',
	components: {
		InputText,
		JobtronButton,
	},
	props: {},
	data(){
		return {
			taxes: [],
			fields: [
				{
					key: 'index',
					title: '',
				},
				{
					key: 'name',
					title: 'Название',
				},
				{
					key: 'isPercent',
					title: 'В процентах',
				},
				{
					key: 'endSubtraction',
					title: 'После вычета других налогов',
				},
				{
					key: 'value',
					title: 'Сумма/Процент',
				},
				{
					key: 'actions',
					title: '',
				},
			],
			taxForm: {
				id: '',
				name: '',
				isPercent: false,
				endSubtraction: false,
				value: '',
			},
		}
	},
	computed: {},
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
				const { data } = await this.axios.get('/tax/all')
				this.taxes = data.data.map(tax => {
					return {
						...tax,
						isPercent: !!tax.is_percent,
						endSubtraction: !!tax.end_subtraction,
						edit: false,
					}
				})
			}
			catch (error) {
				window.onerror && window.onerror(error)
				console.error(error)
				this.$toast.error('Не удалось получить список налогов')
			}

			loader.hide()
		},

		async removeTax(tax){
			if(!confirm('Вы уверены что хотите удалить налог, он будет отменен для всех сотрудников')) return
			const loader = this.$loading.show()
			try {
				const { data } = await this.axios.delete(`/tax/${this.deleteTaxObj.tax_id || this.deleteTaxObj.id}`)
				if (!data) {
					loader.hide()
					return this.$toast.error('Ошибка при удалении')
				}
				const index = this.taxes.findIndex(t => t.id === tax.id)
				if(~index) this.taxes.splice(index, 1)
				this.$toast.success('Налог удален')
			}
			catch (error) {
				window.onerror && window.onerror(error)
				console.error(error)
				this.$toast.error('Ошибка при удалении')
			}
			loader.hide()
		},

		async createTax(){
			const loader = this.$loading.show()

			try {
				const { data } = await this.axios.post('/tax', {
					name: this.taxForm.name,
					value: +this.taxForm.value,
					[percentField]: this.taxForm.isPercent,
					[endSubtractionField]: this.taxForm.endSubtraction,
				})
				const id = data.data.id
				const index = this.taxes.findIndex(t => t.id === this.taxForm.id)
				if(~index){
					this.taxes[index].id = id
					this.taxes[index].name = this.taxForm.name
					this.taxes[index].value = this.taxForm.value
					this.taxes[index].isPercent = this.taxForm.isPercent
					this.taxes[index].endSubtraction = this.taxForm.endSubtraction
					this.taxes[index].edit = false
				}
				else{
					this.fetchTaxes()
				}
				this.$toast.success('Налог создан')
			}
			catch (error) {
				window.onerror && window.onerror(error)
				console.error(error)
				this.$toast.error('Ошибка при создании')
			}

			loader.hide()
		},

		async updateTax(){
			const loader = this.$loading.show()

			try {
				await this.axios.post('/tax', {
					[methodField]: 'put',
					id: this.taxForm.id,
					name: this.taxForm.name,
					value: +this.taxForm.value,
					[percentField]: this.taxForm.isPercent,
					[endSubtractionField]: this.taxForm.endSubtraction,
				})
				const index = this.taxes.findIndex(t => t.id === this.taxForm.id)
				if(~index){
					this.taxes[index].name = this.taxForm.name
					this.taxes[index].value = this.taxForm.value
					this.taxes[index].isPercent = this.taxForm.isPercent
					this.taxes[index].endSubtraction = this.taxForm.endSubtraction
					this.taxes[index].edit = false
				}
				else{
					this.fetchTaxes()
				}
				this.$toast.success('Налог сохранен')
			}
			catch (error) {
				window.onerror && window.onerror(error)
				console.error(error)
				this.$toast.error('Ошибка при сохранении')
			}

			loader.hide()
		},

		saveTax(tax){
			if(tax.id > 0) return this.updateTax()
			this.createTax()
		},

		editTax(tax){
			this.taxes.forEach(tax => {
				tax.edit = false
			})
			if(tax) {
				tax.edit = true
				this.taxForm = JSON.parse(JSON.stringify(tax))
			}
			else{
				this.clearForm()
			}
		},

		newTax(){
			this.taxes.forEach(tax => {
				tax.edit = false
			})
			const newtax = {
				id: --newid,
				name: '',
				isPercent: false,
				endSubtraction: false,
				value: '',
				edit: true,
			}
			this.taxes.unshift(newtax)
			this.taxForm = JSON.parse(JSON.stringify(newtax))
		},

		clearForm(){
			this.taxForm = {
				id: '',
				name: '',
				isPercent: false,
				endSubtraction: false,
				value: '',
			}
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
