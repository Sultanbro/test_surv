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
			taxes: []
		}
	},
	watch: {
		user(obj){
			this.axios.get('/tax', {params: {user_id: obj.id}}).then(res => {
				console.log(res);
				this.taxes = res.data.items;
			}).catch(err => {
				console.log(err);
			})

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
	computed: {
		filteredTaxes() {
			return this.taxes.length ? this.taxes.filter(item => item.name.toLowerCase().includes(this.search.toLowerCase()) && !item.isAssigned) : [];
		},
		taxFiltered(){
			return this.taxes.length ? this.taxes.filter(item => item.isAssigned) : [];
		},
		taxNotAssignedFiltered(){
			return this.taxes.length ? this.taxes.filter(item => !item.isAssigned) : [];
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
			this.taxes.push({
				name: '',
				amount: '',
				isPercent: false,
				isAssigned: true
			})
			this.search = '';
			this.searchId = null;
		},
		deleteTax(key){
			this.taxes.splice(key, 1)
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
			this.taxes.pop();
			const index = this.taxes.findIndex(m => m.id === id);
			const splicedEl = this.taxes.splice(index, 1);
			splicedEl[0].isAssigned = true;
			this.taxes.push(splicedEl[0]);
			this.search = '';
			this.searchIdx = null;
		},
		percentMod(amount, index){
			console.log(amount);
			if(amount > 100){
				this.taxes[index].amount = 100;
			} else if(amount < 0){
				this.taxes[index].amount = 0;
			} else {
				this.taxes[index].amount = amount;
			}
		},
		async saveTaxes(){
			const formData = new FormData();
			const formData2 = new FormData();

			formData.append('name', 'Налог на пидараса s aa afsa fas')
			formData.append('value', 25444)
			formData.append('is_percent', 0)
			const res1 = await this.axios.post('/tax', formData);


			console.log(res1.data.data.id);
			formData2.append('user_id', this.user.id);
			formData2.append('tax_id', res1.data.data.id);
			formData2.append('is_assigned', 1);
			const res2 = await this.axios.post('/tax/set-assignee', formData2);
			console.log(res2);
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

		<hr>
		<div
			v-if="user"
			class="cards"
		>
			<div
				class="no-text"
				v-if="!cards.length"
			>
				Нет ни одной карты
			</div>
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
		<button
			type="button"
			class="btn btn-success btn-rounded mb-2 mt-2"
			@click="addCard"
		>
			<i class="fa fa-plus mr-2" /> Добавить карту
		</button>
		<hr>

		<div
			v-if="user"
			class="taxes"
		>
			<div
				class="no-text"
				v-if="!taxes.length"
			>
				Нет ни одного налога
			</div>
			<div class="row">
				<div class="col-12 col-md-8">
					<div
						v-for="(tax, index) in taxFiltered"
						:key="tax.id"
						class="d-flex tax-row"
					>
						<b-form-group
							class="custom-switch custom-switch-sm"
							id="input-group-4"
						>
							<b-form-checkbox
								v-model="tax.isPercent"
								switch
							>
								В процентах
							</b-form-checkbox>
						</b-form-group>
						<b-form-group class="ml-2">
							<b-form-input
								v-model="tax.name"
								type="text"
								class="mr-1"
								placeholder="Название налога"
								@input="searchMethod(tax, index)"
							/>
							<div
								class="taxes-list-dropdown"
								v-if="search.length && index === taxFiltered.length - 1"
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
						<b-form-group class="ml-2">
							<b-form-input
								v-model="tax.amount"
								type="number"
								class="mr-1"
								placeholder="Процент от оклада"
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
						<button
							type="button"
							class="btn btn-danger tax-delete rounded ml-2"
							@click="deleteTax(key)"
						>
							<i class="fa fa-trash" />
						</button>
					</div>
				</div>
				<div class="col-12 col-md-4">
					<multiselect
						:options="taxNotAssignedFiltered"
						track-by="name"
						label="name"
					/>
				</div>
			</div>
		</div>

		<button
			v-if="user && user.zarplata"
			type="button"
			class="btn btn-success btn-rounded mb-2 mt-2"
			@click="addTax"
		>
			<i class="fa fa-plus mr-2" /> Добавить налог
		</button>
		<button
			type="button"
			class="btn btn-success btn-rounded mb-2 mt-2"
			@click="saveTaxes"
		>
			<i class="fa fa-plus mr-2" /> Сохранить налоги
		</button>
	</div>
</template>


<style scoped lang="scss">
	.no-text{
		height: 40px;
		color: #999;
		font-size: 16px;
		font-weight: 400;
		display: flex;
		align-items: center;
	}
	.tax-delete{
		height: 35px;
	}
</style>
