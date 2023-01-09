<script>
import {mask} from 'vue-the-mask'

export default {
	name: 'UserEditPhones',
	directives: {mask},
	props: {
		user: {
			type: Object,
			default: null,
		},
		old_phone: String,
		old_phone_1: String,
		old_phone_2: String,
		old_phone_3: String,
		old_phone_4: String,
	},
	methods:{
		addPhone(){
			this.user.profileContacts.push({
				type: 'phone',
				name: '',
				value: ''
			})
		},
		deletePhone(key){
			this.user.profileContacts.splice(key, 1)
		}
	},
}
</script>
<template>
    <div
        id="profile_contacts"
        class="phones col-md-6 none-block"
    >
        <h5 class="mb-4">Контакты</h5>
        <div class="d-flex phone-row form-group mb-2">
            <label
                for="phone"
                class="col-sm-4 col-form-label font-weight-bold"
            >Мобильный <span class="red">*</span></label>
            <div class="col-sm-12">
                <input
                    name="phone"
                    :value="user ? user.phone : old_phone"
                    type="text"
                    v-mask="'+7(###) ###-##-##'"
                    id="phone"
                    class="phone_mask form-control mr-1 col-sm-8"
                    placeholder="Телефон"
                >
            </div>
        </div>
        <div class="d-flex phone-row form-group mb-2">
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
        <div class="d-flex phone-row form-group mb-2">
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
        <div class="d-flex phone-row form-group mb-2">
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
        <div class="d-flex phone-row form-group mb-2">
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

        <template v-if="user">
            <template v-for="(key, contact) in user.profileContacts">
                <div
                    v-if="contact.type === 'phone'"
                    :key="key"
                    class="d-flex phone-row form-group m0"
                >
                    <input
                        :name="`contacts[phone][${key}][name]`"
                        :value="contact.name"
                        type="text"
                        class="form-control mr-1"
                        placeholder="Название"
                    >
                    <input
                        :name="`contacts[phone][${key}][value]`"
                        :value="contact.value"
                        type="text"
                        v-mask="'+7(###) ###-##-##'"
                        class="form-control mr-1"
                        placeholder="Телефон"
                    >
                    <button
                        type="button"
                        class="btn btn-danger btn-sm contact-delete rounded"
                        @click="deletePhone(key)"
                    >
                        <i class="fa fa-trash"/>
                    </button>
                </div>
            </template>
        </template>

        <div class="row">
            <div class="col-12 col-md-8 offset-md-4">
                <button
                    type="button"
                    class="btn btn-phone btn-success btn-rounded mb-2 mt-2"
                    @click="addPhone()"
                >
                    <i class="fa fa-plus mr-2"/> Добавить телефон
                </button>
            </div>
        </div>
    </div>
</template>