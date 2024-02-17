<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */
/* eslint-disable vue/no-mutating-props */

import {mask} from 'vue-the-mask'
import UserEditError from '@/components/pages/UserEdit/UserEditError'

export default {
	name: 'UserEditSalary',
	directives: {mask},
	components: {
		UserEditError,
	},
	props: {
		user: {
			type: Object,
			default: null,
		},
		old_zarplata: {
			type: String,
			default: ''
		},
		old_kaspi_cardholder: {
			type: String,
			default: ''
		},
		old_kaspi: {
			type: String,
			default: ''
		},
		old_card_kaspi: {
			type: String,
			default: ''
		},
		old_jysan_cardholder: {
			type: String,
			default: ''
		},
		old_jysan: {
			type: String,
			default: ''
		},
		old_card_jysan: {
			type: String,
			default: ''
		},
		taxes: {
			type: Array,
			default: () => ([])
		},
		allTaxes: {
			type: Array,
			default: () => ([])
		},
		errors:{
			type: Object,
			default: () => ({})
		},
		taxGroups: {
			type: Array,
			default: () => [],
		},
		taxGroup: {
			type: Number,
			default: 0
		},
	},
	data() {
		return {
			headphonesState: this.user?.headphones_sum > 0,
			cards: [],
			zarplata: 0,
			currency: this.user ? this.user.currency : null,
			newTaxes: [],
			editTaxes: [],
			assignTaxes: [],
			myTaxes: [],
			taxModal: false,
			deleteTaxObj: null,
			deleteTaxIdx: null,
			uin: this.user ? this.user.uin : '',
			isNalog: !!this.taxGroup,
			isBP: ['test', 'bp'].includes(location.hostname.split('.')[0])
		}
	},
	computed: {
		taxNotAssignedFiltered() {
			return this.taxes.length ? this.taxes.filter(item => !item.isAssigned) : [];
		},
		salaryAfterTaxes(){
			if(!this.zarplata) return 0
			let result = +this.zarplata
			this.myTaxes.forEach(tax => {
				if(tax.endSubtraction) return
				result -= tax.isPercent ? Math.round(this.zarplata * tax.value / 100) : tax.value
			})
			return result
		}
	},
	watch: {
		user(obj) {
			if (obj.cards) {
				this.cards = obj.cards
				if(!this.cards.length){
					this.cards.push({
						bank: '',
						country: '',
						cardholder: '',
						phone: '',
						number: '',
					})
				}
			}
			const zp = this.user && this.user.zarplata
				? this.user.zarplata.zarplata
					? this.user.zarplata.zarplata - this.user.headphones_sum
					: 0
				: this.old_zarplata
					? this.old_zarplata
					: 0;
			this.zarplata = Math.round(zp);
			this.currency = this.user ? this.user.currency : null
			this.uin = obj?.uin || ''
		},
		taxes() {
			this.myTaxes = this.taxes.slice().filter(item => item.isAssigned);
		},
		isNalog(){
			if(!this.isNalog) this.$emit('tax', {target: {value: 0}})
		},
		taxGroup(){
			this.isNalog = !!this.taxGroup
		},
	},
	methods: {
		addCard() {
			const card = {
				bank: '',
				country: '',
				cardholder: '',
				phone: '',
				number: '',
			};
			this.cards.push(card);
		},
		async deleteCard(key, card) {
			this.cards.splice(key, 1);
			if (card.id) {
				const response = await this.axios.post('/profile/remove/card/', {'card_id': card.id});
				if (!response.data) {
					this.$toast.error('Ошибка при удалении карты');
					return;
				}
			}
			this.$toast.success('Карта удалена');
		},
		changeHeadphonesState() {
			this.headphonesState = !this.headphonesState
			if (this.headphonesState && this.user) {
				this.user.headphones_sum = 0
			}
		},
		async unassignTax(tax, idx) {
			if (!tax.isNew && this.user) {
				const loader = this.$loading.show()
				await this.axios.post('/tax/is_assigned', {
					user_id: this.user.id,
					tax_id: tax.id || tax.tax_id,
					is_assigned: 0,
				})
				loader.hide()
			}
			this.myTaxes.splice(idx, 1);
			this.$toast.success('Налог отменен')
			this.$emit('taxes_update')
		},
		selectTaxNotAssigned(val) {
			const id = val.id || val.tax_id
			this.assignTaxes.push({
				...val,
				id,
				tax_id: id,
				value: 0,
				endSubtraction: !!val.end_subtraction,
				isPercent: !!val.is_percent,
			});
			this.myTaxes.push({
				...val,
				id,
				tax_id: id,
				value: 0,
				endSubtraction: !!val.end_subtraction,
				isPercent: !!val.is_percent,
			});
			const index = this.taxes.findIndex(t => t.id === id);
			if(~index){
				this.taxes[index].isAssigned = true;
			}

			this.$emit('taxes_fill', {
				newTaxes: this.newTaxes,
				assignTaxes: this.assignTaxes,
				editTaxes: this.editTaxes,
			})
		},
		onEditTax(tax) {
			if(tax.isNew) return
			const exists = this.editTaxes.find(t => t.tax_id === tax.tax_id)
			if(exists){
				exists.name = tax.name
				exists.value = tax.value
				exists.isPercent = tax.isPercent
				exists.endSubtraction = tax.endSubtraction
			}
			else{
				this.editTaxes.push(tax)
			}
			this.$emit('taxes_fill', {
				newTaxes: this.newTaxes,
				assignTaxes: this.assignTaxes,
				editTaxes: this.editTaxes
			})
		},
		openTaxModal(tax, idx) {
			this.taxModal = true;
			this.deleteTaxObj = tax;
			this.deleteTaxIdx = idx;
		},
		async deleteTax() {
			let loader = this.$loading.show();
			const response = await this.axios.delete(`/tax/${this.deleteTaxObj.tax_id || this.deleteTaxObj.id}`);
			if (!response.data) {
				this.$toast.error('Ошибка при удалении');
				return;
			}
			this.myTaxes.splice(this.deleteTaxIdx, 1);
			this.$toast.success('Налог удален');
			this.taxModal = false;
			this.deleteTaxObj = null;
			this.deleteTaxIdx = null;
			loader.hide();
		},
		deleteNewTax(idx){
			this.myTaxes.splice(idx, 1);
			this.newTaxes.splice(idx, 1);
		},
		checkNumber(event) {
			const value = parseInt(event.target.value);
			if (!isNaN(value) && Number.isInteger(value)) {
				this.zarplata = value;
			}
		},
	},
}
</script>
<template>
	<div
		id="profile_salary"
		class="col-md-12 mt-3 none-block"
	>
		<div class="form-group row">
			<label
				for="zarplata"
				class="col-sm-2 col-form-label font-weight-bold"
			>Оклад</label>

			<div class="col-sm-4">
				<input
					id="zarplata"
					v-model.number="zarplata"
					class="form-control"
					type="number"
					name="zarplata"
					required
					placeholder="Оклад"
					@input="checkNumber"
				>
			</div>


			<div class="col-sm-5">
				<select
					id="currency"
					v-model="currency"
					name="currency"
					class="form-control form-control-sm"
				>
					<option
						value="null"
						selected
						disabled
					>
						Валюта
					</option>
					<option
						value="kzt"
						:selected="user && user.currency === 'kzt'"
					>
						KZT Казахстанский тенге
					</option>
					<option
						value="rub"
						:selected="user && user.currency === 'rub'"
					>
						RUB Российский рубль
					</option>
					<option
						value="kgs"
						:selected="user && user.currency === 'kgs'"
					>
						KGS Киргизский сом
					</option>
					<option
						value="uzs"
						:selected="user && user.currency === 'uzs'"
					>
						UZS Узбекский сум
					</option>
					<!-- <option
						value="uah"
						:selected="user && user.currency === 'uah'"
					>
						UAH Украинская гривна
					</option>
					<option
						value="byn"
						:selected="user && user.currency === 'byn'"
					>
						BYN Белорусский рубль
					</option> -->
				</select>
			</div>

			<div class="col-sm-1">
				<img
					id="user-currency"
					src="/images/dist/profit-info.svg"
					class="img-info mt-2"
					alt="info icon"
				>
				<b-popover
					target="user-currency"
					triggers="hover"
					placement="right"
				>
					<p class="fz-15">
						Выберите валюту в которой будет отображаться вся оплата на странице профиля данного сотрудника
					</p>
				</b-popover>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-2 font-weight-bold d-flex aic">
				<label class="d-inline-flex aic gap-3">
					Налог с сотрудника
					<b-form-checkbox
						v-model="isNalog"
						switch
						@change="onEditTax(tax)"
					/>
				</label>
				<img
					v-b-popover.click.blur.html="'Включение функции удержания % или суммы налога от его оклада'"
					src="/images/dist/profit-info.svg"
					class="img-info"
					width="20"
					alt="info icon"
					tabindex="-1"
				>
			</div>
			<div class="col-sm-4">
				<select
					v-if="isNalog"
					:value="taxGroup"
					class="form-control form-control-sm"
					@change="$emit('tax', $event)"
				>
					<option
						value="0"
						selected
						disabled
					>
						Выберите группу налогов
					</option>
					<option
						v-for="tax in taxGroups"
						:key="tax.id"
						:value="tax.id"
					>
						{{ tax.name }}
					</option>
				</select>
			</div>
		</div>


		<template v-if="false && user">
			<template v-if="user.zarplata && user.zarplata.kaspi_cardholder">
				<div class="form-group row">
					<label
						for="kaspi"
						class="col-sm-3 col-form-label font-weight-bold"
					>KASPI BANK</label>
					<div class="col-sm-3">
						<input
							id="kaspi_cardholder"
							name="kaspi_cardholder"
							:value="user.zarplata ? user.zarplata.kaspi_cardholder : old_kaspi_cardholder"
							type="text"
							class="form-control"
							placeholder="Имя на карте"
						>
					</div>
					<div class="col-sm-3">
						<input
							id="kaspi"
							name="kaspi"
							:value="user.zarplata ? user.zarplata.kaspi : old_kaspi"
							type="text"
							class="form-control"
							placeholder="+7 (777) 777-77-77"
						>
					</div>
					<div
						class="col-sm-3"
						style="padding-left:0"
					>
						<input
							id="card_kaspi"
							name="card_kaspi"
							:value="user.zarplata ? user.zarplata.card_kaspi : old_card_kaspi"
							type="text"
							class="form-control card-number"
							placeholder="XXXX XXXX XXXX XXXX"
							style="font-size: 14px;"
						>
					</div>
				</div>
			</template>
			<template v-if="user.zarplata && user.zarplata.jysan_cardholder">
				<div class="form-group row">
					<label
						for="jysan"
						class="col-sm-3 col-form-label font-weight-bold"
					>JYSAN BANK</label>
					<div class="col-sm-3">
						<input
							id="jysan_cardholder"
							name="jysan_cardholder"
							:value="user.zarplata ? user.zarplata.jysan_cardholder : old_jysan_cardholder"
							type="text"
							class="form-control"
							placeholder="Имя на карте"
						>
					</div>
					<div class="col-sm-3">
						<input
							id="jysan"
							name="jysan"
							:value="user.zarplata ? user.zarplata.jysan : old_jysan"
							type="text"
							class="form-control"
							placeholder="+7 (777) 777-77-77"
						>
					</div>
					<div
						class="col-sm-3"
						style="padding-left:0"
					>
						<input
							id="card_jysan"
							name="card_jysan"
							:value="user.zarplata ? user.zarplata.card_jysan : old_card_jysan"
							type="text"
							class="form-control card-number"
							placeholder="XXXX XXXX XXXX XXXX"
							style="font-size: 14px;"
						>
					</div>
				</div>
			</template>
		</template>

		<hr>

		<div
			v-if="user"
			class="cards"
		>
			<div
				v-if="!cards.length"
				class="no-text"
			>
				Нет ни одной карты
			</div>
			<div class="bold mb-2">
				Банковская карта
				<img
					v-b-popover.click.blur.html="'Укажите данные карты на которую будет начисляться зарплата'"
					src="/images/dist/profit-info.svg"
					class="img-info"
					width="20"
					alt="info icon"
					tabindex="-1"
				>
			</div>
			<template v-for="(card, key) in cards">
				<div
					v-if="key < 1"
					:key="card.id"
					class="d-flex form-group m0 row"
				>
					<div class="col-sm-2">
						<input
							v-model="card.bank"
							:name="`cards[${key}][bank]`"
							type="text"
							class="form-control"
							placeholder="Банк/Кошелек/..."
						>
					</div>
					<div class="col-sm-2">
						<input
							v-model="card.country"
							:name="`cards[${key}][country]`"
							type="text"
							class="form-control"
							placeholder="Страна"
						>
					</div>
					<div class="col-sm-2">
						<input
							v-model="card.cardholder"
							:name="`cards[${key}][cardholder]`"
							type="text"
							class="form-control"
							placeholder="Имя на карте"
						>
					</div>
					<div class="col-sm-2">
						<input
							v-model="card.phone"
							:name="`cards[${key}][phone]`"
							type="text"
							class="form-control"
							placeholder="Телефон"
						>
					</div>
					<div class="col-sm-3">
						<input
							v-model="card.number"
							v-mask="`#### #### #### ####`"
							:name="`cards[${key}][number]`"
							type="text"
							class="form-control card-number"
							placeholder="Номер карты/счета"
						>
					</div>
					<div class="col-sm-1">
						<!-- <button
							type="button"
							class="btn btn-danger card-delete rounded"
							@click="deleteCard(key, card)"
						>
							<i class="fa fa-trash" />
						</button> -->
					</div>
				</div>
			</template>
		</div>
		<div
			v-if="false"
			class="row"
		>
			<div class="col-sm-2">
				<button
					type="button"
					class="btn btn-success btn-rounded btn-block mb-2 mt-2"
					@click="addCard"
				>
					<i class="fa fa-plus mr-2" /> Добавить карту
				</button>
			</div>
		</div>

		<hr>

		<div
			v-if="false"
			class="taxes"
		>
			<div
				v-if="!taxes.length"
				class="no-text"
			>
				Нет ни одного налога
			</div>
			<div
				v-for="(tax, idx) in myTaxes"
				:key="tax.tax_id"
				class="d-flex tax-row"
			>
				<b-form-group class="custom-switch custom-switch-sm">
					<b-form-checkbox
						v-model="tax.isPercent"
						switch
						@change="onEditTax(tax)"
					>
						В процентах
					</b-form-checkbox>
				</b-form-group>
				<b-form-group
					v-if="isBP && [20641, 27402, 5].includes($laravel.userId)"
					class="custom-switch custom-switch-sm ml-2"
				>
					<b-form-checkbox
						v-model="tax.endSubtraction"
						switch
						@change="onEditTax(tax)"
					>
						После других налогов
					</b-form-checkbox>
				</b-form-group>
				<b-form-group class="ml-2">
					<b-form-input
						v-model="tax.name"
						type="text"
						class="mr-1"
						:disabled="!!tax.tax_id"
						placeholder="Название налога"
						@input="onEditTax(tax)"
					/>
				</b-form-group>
				<b-form-group class="ml-2">
					<b-form-input
						v-if="tax.isPercent"
						v-model="tax.value"
						type="number"
						class="mr-1"
						placeholder="Процент от оклада"
						:class="{'is-invalid' : tax.value > 100}"
						@input="onEditTax(tax)"
					/>
					<b-form-input
						v-else
						v-model="tax.value"
						type="number"
						class="mr-1"
						placeholder="Сумма"
						@input="onEditTax(tax)"
					/>
				</b-form-group>

				<b-form-input
					v-if="tax.endSubtraction && tax.isPercent"
					:value="tax.value ? Math.round(salaryAfterTaxes * tax.value / 100) : 0"
					type="text"
					disabled
					class="ml-2 w-200px"
					:class="{'is-invalid' : Math.round(salaryAfterTaxes * tax.value / 100) > salaryAfterTaxes}"
				/>
				<b-form-input
					v-else-if="tax.isPercent"
					:value="tax.value ? Math.round(zarplata * tax.value / 100) : 0"
					type="text"
					disabled
					class="ml-2 w-200px"
					:class="{'is-invalid' : Math.round(zarplata * tax.value / 100) > zarplata}"
				/>

				<button
					v-if="!tax.isNew"
					:id="idx + '1'"
					type="button"
					class="btn btn-warning tax-delete rounded ml-2"
					@click="unassignTax(tax, idx)"
				>
					<span class="close-icon-style">x</span>
				</button>
				<b-popover
					:target="idx + '1'"
					triggers="hover"
					placement="top"
				>
					<p style="font-size: 15px; text-align: center;">
						Отменить налог данному сотруднику
					</p>
				</b-popover>
				<!-- <button
					v-if="!tax.isNew"
					type="button"
					class="btn btn-danger tax-delete rounded ml-2"
					@click="openTaxModal(tax, idx)"
				>
					<i class="fa fa-trash" />
				</button>
				<button
					v-else
					type="button"
					class="btn btn-outline-danger tax-delete rounded ml-2"
					@click="deleteNewTax(idx)"
				>
					<i class="fa fa-minus" />
				</button> -->
			</div>
		</div>

		<b-modal
			v-model="taxModal"
			centered
			size="md"
			title="Удалить налог"
		>
			<div class="text-center my-4">
				<p>Вы уверены, что хотите удалить налог?</p>
				<p class="mt-3">
					Данный налог будет удален из системы и автоматичски отменен всем сотрудникам, которым он был
					присвоен
				</p>
			</div>
			<template #modal-footer>
				<b-button
					variant="danger"
					@click="deleteTax"
				>
					Удалить
				</b-button>
				<b-button
					variant="light"
					@click="taxModal = false"
				>
					Отмена
				</b-button>
			</template>
		</b-modal>

		<div
			v-if="false"
			class="row my-2"
		>
			<template v-if="zarplata > 0">
				<div class="col-sm-2 d-flex aic">
					<!--  -->
				</div>
				<div class="col-sm-10">
					<multiselect
						:options="allTaxes"
						track-by="name"
						label="name"
						class="pt-2"
						placeholder="Добавить налог сотруднику"
						@select="selectTaxNotAssigned"
					/>
				</div>
			</template>
			<span
				v-else
				class="text-muted col-sm-12"
			>Поле оклад должно быть больше нуля</span>
		</div>


		<div class="UserEditSalary-other row">
			<div class="col-sm-12">
				<div class="form-group row">
					<label
						for="uin"
						class="col-sm-2 d-flex aic col-form-label font-weight-bold"
					>ИНН</label>
					<div class="col-sm-9">
						<input
							id="uin"
							v-model="uin"
							name="uin"
							type="text"
							required
							class="form-control"
							placeholder="введите ИНН"
						>
					</div>
					<UserEditError
						:errors="errors"
						name="uin"
					/>
				</div>
			</div>
		</div>
	</div>
</template>


<style scoped lang="scss">
	.no-text {
		height: 40px;
		color: #999;
		font-size: 16px;
		font-weight: 400;
		display: flex;
		align-items: center;
	}

	.tax-delete {
		height: 35px;
	}

	.close-icon-style {
		font-size: 16px;
		font-weight: 700;
		line-height: 1;
	}

</style>

<style lang="scss">
.custom-switch{
	.custom-control-label{
		height: 15px;
	}
}
</style>
