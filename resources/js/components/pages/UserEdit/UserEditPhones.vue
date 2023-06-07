<script>
/* eslint-disable vue/no-mutating-props */
import {mask} from 'vue-the-mask'

export default {
	name: 'UserEditPhones',
	directives: {mask},
	props: {
		user: {
			type: Object,
			default: null,
		},
		profileContacts: {
			type: Array,
			default: () => []
		},
		old_phone: String,
		old_phone_1: String,
		old_phone_2: String,
		old_phone_3: String,
		old_phone_4: String
	},
	data() {
		return{
			mainPhone: ''
		}
	},
	watch: {
		user(obj){
			this.mainPhone = obj ? obj.phone : '';
		}
	},
	methods:{
		addPhone(){
			this.$emit('add_contacts', {
				type: 'phone',
				name: '',
				value: ''
			});
		},
		editContact(event, input, key){
			this.$emit('change_contact', {
				input: input,
				key: key,
				value: event.target.value
			});
		},
		async deletePhone(key){
			this.profileContacts.splice(key, 1);
		}
	}
}
</script>
<template>
	<div
		id="profile_contacts"
		class="phones"
	>
		<div class="row">
			<div class="col-12 col-md-6">
				<div class="d-flex phone-row form-group">
					<label
						for="phone"
						class="col-sm-4 col-form-label font-weight-bold"
					>
						Мобильный
						<img
							src="/images/dist/profit-info.svg"
							class="img-info"
							alt="info icon"
							v-b-popover.hover="'Только на этот номер могут отправляться сообщения васап'"
						>
					</label>
					<div class="col-sm-12">
						<input
							name="phone"
							type="text"
							v-model="mainPhone"
							v-mask="'+7(###) ###-##-##'"
							id="phone"
							class="phone_mask form-control mr-1 col-sm-8"
							placeholder="Телефон"
						>
					</div>
				</div>
				<div class="d-flex phone-row form-group">
					<label
						for="phone_1"
						class="col-sm-4 col-form-label font-weight-bold"
					>Домашний</label>
					<div class="col-sm-12">
						<input
							name="phone_1"
							:value="user ? user.phone_1 : old_phone_1"
							type="text"
							v-mask="'+7(###) ###-##-##'"
							id="phone_1"
							class="phone_mask form-control mr-1 col-sm-8"
							placeholder="Телефон"
						>
					</div>
				</div>
				<div class="d-flex phone-row form-group">
					<label
						for="phone_2"
						class="col-sm-4 col-form-label font-weight-bold"
					>Супруга/Муж</label>
					<div class="col-sm-12">
						<input
							name="phone_2"
							:value="user ? user.phone_2 : old_phone_2"
							type="text"
							v-mask="'+7(###) ###-##-##'"
							id="phone_2"
							class="phone_mask form-control mr-1 col-sm-8"
							placeholder="Телефон"
						>
					</div>
				</div>
				<div class="d-flex phone-row form-group">
					<label
						for="phone_3"
						class="col-sm-4 col-form-label font-weight-bold"
					>Друг/Брат/Сестра</label>
					<div class="col-sm-12">
						<input
							name="phone_3"
							:value="user ? user.phone_3 : old_phone_3"
							type="text"
							v-mask="'+7(###) ###-##-##'"
							id="phone_3"
							class="phone_mask form-control mr-1 col-sm-8"
							placeholder="Телефон"
						>
					</div>
				</div>
				<div class="d-flex phone-row form-group">
					<label
						for="phone_4"
						class="col-sm-4 col-form-label font-weight-bold"
					>Сын/Дочь</label>
					<div class="col-sm-12">
						<input
							name="phone_4"
							:value="user ? user.phone_4 : old_phone_4"
							type="text"
							v-mask="'+7(###) ###-##-##'"
							id="phone_4"
							class="phone_mask form-control mr-1 col-sm-8"
							placeholder="Телефон"
						>
					</div>
				</div>
			</div>
			<div class="col-12 col-md-6">
				<template v-if="profileContacts">
					<template v-for="(contact, key) in profileContacts">
						<div
							v-if="contact.type === 'phone'"
							:key="key"
							class="d-flex phone-row form-group mb-3"
						>
							<input
								:name="`contacts[phone][${key}][name]`"
								:value="contact.name"
								type="text"
								class="form-control mr-1"
								placeholder="Название"
								@change="editContact($event, 'name', key)"
							>
							<input
								:name="`contacts[phone][${key}][value]`"
								:value="contact.value"
								type="text"
								class="form-control mr-1"
								placeholder="Телефон"
								@change="editContact($event, 'value', key)"
							>
							<button
								type="button"
								class="btn btn-danger btn-sm contact-delete rounded"
								@click="deletePhone(key)"
							>
								<i class="fa fa-trash" />
							</button>
						</div>
					</template>
				</template>
				<button
					type="button"
					class="btn btn-phone btn-success btn-rounded"
					@click="addPhone"
				>
					<i class="fa fa-plus mr-2" /> Добавить телефон
				</button>
			</div>
		</div>
	</div>
</template>
