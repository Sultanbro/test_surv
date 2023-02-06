<script>
import {mask} from 'vue-the-mask'

export default {
	name: 'UserEditSalary',
	directives: {mask},
	props: {
		user: {
			type: Object,
			default: null,
		},
		taxes: {
			type: Array,
			default: () => {},
		},
		old_zarplata: String,
		old_kaspi_cardholder: String,
		old_kaspi: String,
		old_card_kaspi: String,
		old_jysan_cardholder: String,
		old_jysan: String,
		old_card_jysan: String,
	},
	data(){
		return {
			headphonesState: this.user?.headphones_sum > 0
		}
	},
	methods: {
		addCard(){
			this.user.cards.push({
				bank: '',
				country: '',
				cardholder: '',
				phone: '',
				number: '',
			})
		},
		deleteCard(key){
			this.user.cards.splice(key, 1)
		},
		addTax(userId){
			this.taxes.push({
				name: '',
				amount: '',
				percent: '',
				user_id: userId,
			})
		},
		deleteTax(key){
			this.taxes.splice(key, 1)
		},
		changeHeadphonesState(){
			this.headphonesState = !this.headphonesState
			if(this.headphonesState && this.user){
				this.user.headphones_sum = 0
			}
		}
	},
}
</script>
<template>
	<div
		id="profile_salary"
		class="col-md-12 mt-3 none-block"
	>
		<h5 class="mb-4">
			Оплата
		</h5>

		<div class="form-group row">
			<label
				for="zarplata"
				class="col-sm-3 col-form-label font-weight-bold"
				:class="{'mr-3': !user}"
			>Оклад <span class="red">*</span></label>

			<div
				:class="{
					'col-sm-3': user,
					'col-sm-4': !user,
					'p-0': !user,
				}"
			>
				<input
					class="form-control"
					type="text"
					name="zarplata"
					id="zarplata"
					required
					placeholder="Оклад"
					:value="user && user.zarplata
						? user.zarplata.zarplata
							? user.zarplata.zarplata - user.headphones_sum
							: 0
						: old_zarplata
							? old_zarplata
							: 0"
				>
			</div>


			<div
				:class="{
					'col-sm-3': user,
					'col-sm-5': !user,
					'pl-1': !user,
					'pr-0': !user,
				}"
			>
				<select
					name="currency"
					id="currency"
					class="form-control form-control-sm"
				>
					<option
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

			<div class="col-sm-3 pl-0" />
		</div>
		<template v-if="user">
			<template v-if="user.zarplata && user.zarplata.kaspi_cardholder">
				<div class="form-group row">
					<label
						for="kaspi"
						class="col-sm-3 col-form-label font-weight-bold"
					>KASPI BANK</label>
					<div class="col-sm-3">
						<input
							name="kaspi_cardholder"
							:value="user.zarplata ? user.zarplata.kaspi_cardholder : old_kaspi_cardholder"
							type="text"
							id="kaspi_cardholder"
							class="form-control"
							placeholder="Имя на карте"
						>
					</div>
					<div class="col-sm-3">
						<input
							name="kaspi"
							:value="user.zarplata ? user.zarplata.kaspi : old_kaspi"
							type="text"
							id="kaspi"
							class="form-control"
							placeholder="+7 (777) 777-77-77"
						>
					</div>
					<div
						class="col-sm-3"
						style="padding-left:0"
					>
						<input
							name="card_kaspi"
							:value="user.zarplata ? user.zarplata.card_kaspi : old_card_kaspi"
							type="text"
							id="card_kaspi"
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
							name="jysan_cardholder"
							:value="user.zarplata ? user.zarplata.jysan_cardholder : old_jysan_cardholder"
							type="text"
							id="jysan_cardholder"
							class="form-control"
							placeholder="Имя на карте"
						>
					</div>
					<div class="col-sm-3">
						<input
							name="jysan"
							:value="user.zarplata ? user.zarplata.jysan : old_jysan"
							type="text"
							id="jysan"
							class="form-control"
							placeholder="+7 (777) 777-77-77"
						>
					</div>
					<div
						class="col-sm-3"
						style="padding-left:0"
					>
						<input
							name="card_jysan"
							:value="user.zarplata ? user.zarplata.card_jysan : old_card_jysan"
							type="text"
							id="card_jysan"
							class="form-control card-number"
							placeholder="XXXX XXXX XXXX XXXX"
							style="font-size: 14px;"
						>
					</div>
				</div>
			</template>
		</template>
		<div
			v-if="user"
			class="form-group row"
		>
			<div class="col-sm-3" />
			<div class="col-sm-3">
				<div class="custom-control custom-checkbox">
					<input
						type="checkbox"
						:checked="headphonesState"
						id="headphones_amount_checkbox"
						class="custom-control-input"
						@change="changeHeadphonesState"
					>
					<label
						for="headphones_amount_checkbox"
						class="custom-control-label"
					>
						Выдано оборудование в счет зарплаты <br>
						{{ user.headphones_date }}
					</label>
				</div>
			</div>
			<div class="col-sm-3">
				<label
					for="headphones_amount"
					class="font-weight-bold"
				>На сумму</label>
			</div>
			<div class="col-sm-3 pl-0">
				<input
					name="headphones_amount"
					class="form-control"
					type="number"
					id="headphones_amount"
					:value="user.headphones_sum"
					:disabled="!headphonesState"
				>
			</div>
		</div>

		<div
			v-if="user"
			class="cards"
		>
			<div
				v-for="(card, key) in user.cards"
				:key="card.id"
				class="d-flex form-group m0 card-row"
			>
				<input
					:name="`cards[${key}][bank]`"
					:value="card.bank"
					type="text"
					class="form-control mr-1 col-sm-2"
					placeholder="Банк/Кошелек/..."
				>
				<input
					:name="`cards[${key}][country]`"
					:value="card.country"
					type="text"
					class="form-control mr-1 col-sm-2"
					placeholder="Страна"
				>
				<input
					:name="`cards[${key}][cardholder]`"
					:value="card.cardholder"
					type="text"
					class="form-control mr-1 col-sm-2"
					placeholder="Имя на карте"
				>
				<input
					:name="`cards[${key}][phone]`"
					:value="card.phone"
					type="text"
					class="form-control mr-1 col-sm-2"
					placeholder="Телефон"
				>
				<input
					:name="`cards[${key}][number]`"
					:value="card.number"
					type="text"
					v-mask="`#### #### #### ####`"
					class="form-control mr-1 col-sm-3 card-number"
					placeholder="Номер карты/счета"
				>
				<button
					type="button"
					class="btn btn-danger card-delete rounded ml-1"
					@click="deleteCard(key)"
				>
					<i class="fa fa-trash" />
				</button>
			</div>
		</div>

		<div
			v-if="user"
			class="taxes"
		>
			<div
				v-for="(tax, key) in taxes"
				:key="tax.id"
				class="d-flex form-group m0 tax-row"
			>
				<input
					:name="`taxes[${key}][name]`"
					:value="tax.name"
					type="text"
					class="form-control mr-1 col-sm-2"
					placeholder="Название"
				>
				<input
					:name="`taxes[${key}][amount]`"
					:value="tax.amount"
					type="text"
					class="form-control mr-1 col-sm-2"
					placeholder="Сумма"
				>
				<input
					:name="`taxes[${key}][percent]`"
					:value="tax.percent"
					type="text"
					class="form-control mr-1 col-sm-2"
					placeholder="Процент"
				>
				<input
					:name="`tax[${key}][user_id]`"
					:value="user.id"
					type="hidden"
					class="form-control mr-1 col-sm-2"
				>
				<button
					type="button"
					class="btn btn-danger tax-delete rounded ml-1"
					@click="deleteTax(key)"
				>
					<i class="fa fa-trash" />
				</button>
			</div>
		</div>

		<button
			type="button"
			class="btn btn-success btn-rounded mb-2 mt-2"
			@click="addCard"
		>
			<i class="fa fa-plus mr-2" /> Добавить карту
		</button>

		<button
			v-if="user && user.zarplata"
			type="button"
			class="btn btn-success btn-rounded mb-2 mt-2"
			@click="addTax(user.id)"
		>
			<i class="fa fa-plus mr-2" /> Добавить налог
		</button>
		<!-- END OF OKLAD -->
	</div>
</template>