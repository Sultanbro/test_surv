<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */

import ProfileGroups from '@/components/profile/ProfileGroups' // настройки user

export default {
	name: 'UserEditGroups',
	components: {
		ProfileGroups,
	},
	props: {
		user: {
			type: Object,
			default: null
		},
		groups:{
			type: Array,
			default: () => []
		},
		in_groups:{
			type: Array,
			default: () => []
		},
		front_valid:{
			type: Object,
			default: () => null
		}
	},
	data() {
		return{
			group: null
		}
	},
	methods: {
		checkValid(event){
			if(this.front_valid && this.front_valid.formSubmitted){
				const val = event.target.value;
				!val.length ? this.$emit('valid_change', false) : this.$emit('valid_change', true);
			}
		},
	},
}
</script>
<template>
	<div
		id="iphones3"
		class="mb-3 xfade none-block"
	>
		<div
			class="form-group row"
			:class="{'form-group-error': front_valid.formSubmitted && !front_valid.group}"
		>
			<label
				class="col-sm-4 col-form-label font-weight-bold"
			>Отделы <span class="red">*</span></label>
			<div class="col-sm-8">
				<ProfileGroups
					v-if="user"
					:groups="groups"
					:user_id="user.id"
					:in_groups="in_groups"
				/>
				<select
					v-else
					id="group"
					v-model="group"
					name="group"
					class="form-control"
					@change="checkValid($event)"
				>
					<option :value="null">
						Выберите отдел
					</option>
					<option
						v-for="groupItem in groups"
						:key="groupItem.id"
						:value="groupItem.id"
					>
						{{ groupItem.name }}
					</option>
				</select>
			</div>
		</div>
	</div>
</template>
