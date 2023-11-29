<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */
/* eslint-disable vue/no-mutating-props */

import {mask} from 'vue-the-mask'
// import { VueTelInput } from 'vue-tel-input'
// import 'vue-tel-input/vue-tel-input.css'

export default {
	name: 'UserEditPhones',
	directives: {mask},
	components: {
		// VueTelInput,
	},
	props: {
		user: {
			type: Object,
			default: null,
		},
		profileContacts: {
			type: Array,
			default: () => []
		},
		old_phone: {
			type: String,
			default: ''
		},
		old_phone_1: {
			type: String,
			default: ''
		},
		old_phone_2: {
			type: String,
			default: ''
		},
		old_phone_3: {
			type: String,
			default: ''
		},
		old_phone_4: {
			type: String,
			default: ''
		}
	},
	data() {
		return{
			mainPhone: this.user?.phone || ''
		}
	},
	watch: {
		user(obj){
			this.mainPhone = obj ? obj.phone : '';
		}
	},
	mounted(){
		this.applyMask()
	},
	created(){
		this.initMask()
	},
	methods: {
		initMask(){
			if(window.intlTelInput) return
			const el = document.createElement('script')
			el.setAttribute('src', 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js')
			document.head.appendChild(el)

			const link = document.createElement('link')
			link.rel = 'stylesheet'
			link.href = 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css'
			document.head.appendChild(link)
		},
		applyMask(){
			if(!window.intlTelInput) return setTimeout(this.applyMask, 100)
			const phones = document.querySelectorAll('.UserEditPhones-phoneInput')
			phones.forEach(input => {
				window.intlTelInput(input, {
					utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js',
					autoInsertDialCode: true,
					preferredCountries: ['ru', 'kz', 'kg', 'uz'],
					nationalMode: false,
					autoPlaceholder: 'aggressive',
					numberType: 'MOBILE',
					// separateDialCode: true,
					// hiddenInput: true,
				})
			})
		},
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
		class="UserEditPhones phones"
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
							v-b-popover.hover="'Только на этот номер могут отправляться сообщения васап'"
							src="/images/dist/profit-info.svg"
							class="img-info"
							alt="info icon"
						>
					</label>
					<div class="col-sm-12">
						<input
							id="phone"
							v-model="mainPhone"
							name="phone"
							type="text"
							class="UserEditPhones-phoneInput form-control mr-1 col-sm-8"
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
							id="phone_1"
							name="phone_1"
							:value="user ? user.phone_1 : old_phone_1"
							type="text"
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
							id="phone_2"
							name="phone_2"
							:value="user ? user.phone_2 : old_phone_2"
							type="text"
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
							id="phone_3"
							name="phone_3"
							:value="user ? user.phone_3 : old_phone_3"
							type="text"
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
							id="phone_4"
							name="phone_4"
							:value="user ? user.phone_4 : old_phone_4"
							type="text"
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
								class="btn btn-danger contact-delete rounded"
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

<style lang="scss">
.UserEditPhones{
	&-phoneInput{
		&.form-control{
			padding-left: 56px !important;
		}
	}

	.iti{
		width: 100%;
	}
}
</style>
