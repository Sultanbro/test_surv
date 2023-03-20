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
		front_valid:{
			type: Object,
			default: () => ({})
		}
	},
	data(){
		return {
			headphonesState: this.user?.headphones_sum > 0,
			cards: [],
			zarplata: 0,
			currency: null,
			search: '',
			searchIdx: null,
			mockDataAll: [],
			mockData: []
		}
	},
	watch: {
		user(obj){
			if(obj.cards){
				this.cards = obj.cards
			}
			this.zarplata = this.user && this.user.zarplata
				? this.user.zarplata.zarplata
					? this.user.zarplata.zarplata - this.user.headphones_sum
					: 0
				: this.old_zarplata
					? this.old_zarplata
					: 0
		},
		zarplata(){
			this.changeZp();
		},
		currency(){
			this.changeZp();
		}
	},
	mounted() {
		// this.axios.get('/')
		// if(this.taxes) this.mockData = this.taxes;

		for(let i = 0; i < 15; i++){
			this.mockDataAll.push({
				id: i,
				name: 'name lorem ' + i,
				amount: Math.floor(Math.random() * 501),
				isPercent: i > 9,
				isAssigned: i > 4 && i < 8
			})
		}
	},
	computed: {
		filteredTaxes() {
			return this.mockDataAll.filter(item => item.name.toLowerCase().includes(this.search.toLowerCase()) && !item.isAssigned)
		},
		mockDataFiltered(){
			return this.mockDataAll.filter(item => item.isAssigned)
		}
	},
	methods: {
		changeZp(){
			if(this.front_valid && this.front_valid.formSubmitted){
				this.currency && Number(this.zarplata) > 1000 ? this.$emit('valid_change', {name: 'zarplata', bool: true}) : this.$emit('valid_change', {name: 'zarplata', bool: false});
			}
		},
		addCard(){
			const card = {
				bank: '',
				country: '',
				cardholder: '',
				phone: '',
				number: '',
			};
			this.cards.push(card);
		},
		async deleteCard(key, card){
			this.cards.splice(key, 1);
			if(card.hasOwnProperty('id')){
				const response = await this.axios.post('/profile/remove/card/', {'card_id': card.id});
				if(!response.data){
					this.$toast.error('Ошибка при удалении карты');
					return;
				}
				this.$toast.success('Карта удалена');
			}
		},
		addTax(){
			this.mockDataAll.push({
				name: '',
				amount: '',
				isPercent: false,
				isAssigned: true
			})
			this.search = '';
			this.searchId = null;
		},
		deleteTax(key){
			this.mockDataAll.splice(key, 1)
		},
		changeHeadphonesState(){
			this.headphonesState = !this.headphonesState
			if(this.headphonesState && this.user){
				this.user.headphones_sum = 0
			}
		},
		searchMethod(tax, index){
			this.search = tax.name;
			this.searchIdx = index;
		},
		selectTax(id){
			this.mockDataAll.pop();
			const index = this.mockDataAll.findIndex(m => m.id === id);
			const splicedEl = this.mockDataAll.splice(index, 1);
			splicedEl[0].isAssigned = true;
			this.mockDataAll.push(splicedEl[0]);
			this.search = '';
			this.searchIdx = null;
		},
		percentMod(amount, index){
			console.log(amount);
			if(amount > 100){
				this.mockData[index].amount = 100;
			} else if(amount < 0){
				this.mockData[index].amount = 0;
			} else {
				this.mockData[index].amount = amount;
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
		<div
			class="form-group row"
			:class="{'form-group-error': front_valid.formSubmitted && front_valid.zarplata === false}"
		>
			<label
				for="zarplata"
				class="col-sm-3 col-form-label font-weight-bold"
				:class="{'mr-3': !user}"
			>Оклад <span class="red">*</span></label>

			<div class="col-sm-3">
				<input
					class="form-control"
					type="text"
					name="zarplata"
					id="zarplata"
					required
					placeholder="Оклад"
					v-model="zarplata"
				>
			</div>


			<div class="col-sm-3">
				<select
					name="currency"
					id="currency"
					class="form-control form-control-sm"
					v-model="currency"
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
				v-for="(card, key) in cards"
				:key="card.id"
				class="d-flex form-group m0 card-row"
			>
				<input
					:name="`cards[${key}][bank]`"
					v-model="card.bank"
					type="text"
					class="form-control mr-1 col-sm-2"
					placeholder="Банк/Кошелек/..."
				>
				<input
					:name="`cards[${key}][country]`"
					v-model="card.country"
					type="text"
					class="form-control mr-1 col-sm-2"
					placeholder="Страна"
				>
				<input
					:name="`cards[${key}][cardholder]`"
					v-model="card.cardholder"
					type="text"
					class="form-control mr-1 col-sm-2"
					placeholder="Имя на карте"
				>
				<input
					:name="`cards[${key}][phone]`"
					v-model="card.phone"
					type="text"
					class="form-control mr-1 col-sm-2"
					placeholder="Телефон"
				>
				<input
					:name="`cards[${key}][number]`"
					v-model="card.number"
					type="text"
					v-mask="`#### #### #### ####`"
					class="form-control mr-1 col-sm-3 card-number"
					placeholder="Номер карты/счета"
				>
				<button
					type="button"
					class="btn btn-danger card-delete rounded ml-1"
					@click="deleteCard(key, card)"
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
				v-for="(tax, index) in mockDataFiltered"
				:key="tax.id"
				class="d-flex tax-row"
			>
				<b-form-group>
					<b-form-input
						v-model="tax.name"
						type="text"
						class="mr-1"
						placeholder="Название"
						@input="searchMethod(tax, index)"
					/>
					<div
						class="taxes-list-dropdown"
						v-if="search.length && index === mockDataFiltered.length - 1"
					>
						<ul class="taxes-list">
							<li
								class="taxes-item"
								v-for="item in filteredTaxes"
								:key="item.id"
								@click="selectTax(item.id)"
							>
								{{ item.name }}
							</li>
						</ul>
					</div>
				</b-form-group>
				<b-form-group>
					<b-form-input
						v-model="tax.amount"
						type="number"
						class="mr-1"
						placeholder="Сумма"
						@change="percentMod(Number(tax.amount), index)"
						v-if="tax.isPercent"
					/>
					<b-form-input
						v-model="tax.amount"
						type="number"
						class="mr-1"
						placeholder="Сумма"
						v-else
					/>
				</b-form-group>
				<b-form-group
					class="custom-switch custom-switch-sm"
					id="input-group-4"
				>
					<b-form-checkbox
						v-model="tax.isPercent"
						switch
					>
						Значение в процентах
					</b-form-checkbox>
				</b-form-group>
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
			@click="addTax"
		>
			<i class="fa fa-plus mr-2" /> Добавить налог
		</button>
		<!-- END OF OKLAD -->
	</div>
</template>
