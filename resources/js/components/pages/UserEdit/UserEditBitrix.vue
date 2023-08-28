<script>
export default {
	name: 'UserEditBitrix',
	props: {
		user: {
			type: Object,
			default: null
		},
	},
	data(){
		return {
			isEditor: false
		}
	},
	methods: {
		openEditor(){
			this.isEditor = true
		}
	}
}
</script>
<template>
	<div
		v-if="user"
		id="other_data_bitrix"
		class="none-block"
	>
		<div class="col-md-12">
			<p v-if="user.bitrix_id">
				<b>Битрикс: <span class="text-green">Есть</span></b>
				<a
					:href="`https://infinitys.bitrix24.kz/company/personal/user/${ user.bitrix_id }/`"
					target="_blank"
				>
					<i class="fa fa-link pointer" />
				</a>
				<i
					id="bitrix_editor"
					class="fa fa-cogs pointer"
					title="Редактировать"
					@click="openEditor"
				/>
			</p>
			<p v-else>
				<b>
					Битрикс:
					<span class="text-red">Нет</span>
				</b>
				<i
					id="bitrix_editor"
					class="fa fa-cogs pointer"
					title="Редактировать"
					@click="openEditor"
				/>
			</p>
			<input
				v-show="isEditor"
				id="bitrix_id_input"
				name="bitrix_id"
				:value="user.bitrix_id"
				type="text"
				class="form-control form-control-sm"
				placeholder="ID профиля в битриксе"
			>
		</div>
	</div>
</template>