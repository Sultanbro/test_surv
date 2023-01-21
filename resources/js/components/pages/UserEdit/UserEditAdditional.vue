<script>
export default {
	name: 'UserEditAdditional',
	props: {
		user: {
			type: Object,
			default: null
		},
		userCreated: String,
		userApplied: String,
		userAppliedDays: Number,
		isTrainee: Boolean,
		userDeleted: String,
		userDeletedAt: String,
	}
}
</script>
<template>
	<div
		id="add_info"
		class="none-block"
	>
		<h5 class="mb-4">
			Дополнительная информация
		</h5>
		<div class="table-responsive">
			<table class="my-table table user-list">
				<tbody>
					<tr>
						<td>
							<span>Дата регистрации</span>
						</td>
						<td>
							<span>{{ userCreated }}</span>
						</td>
					</tr>
					<template v-if="user">
						<tr>
							<td>
								<span>Дата принятия на работу</span>
							</td>
							<td>
								<span>{{ !userApplied && !isTrainee ? userCreated : userApplied }}</span>
							</td>
						</tr>
						<tr>
							<td>
								<span>Успел стать частью команды ~</span>
							</td>
							<td>
								<span>{{ userAppliedDays }} дней</span>
							</td>
						</tr>
						<tr v-if="userDeleted">
							<td>
								<span>Дата отработки</span>
							</td>
							<td>
								<span>{{ userDeleted }}</span>
							</td>
						</tr>
						<template v-if="userDeletedAt">
							<tr>
								<td>
									<span>Дата увольнения</span>
								</td>
								<td>
									<span>{{ userDeletedAt }}</span>
								</td>
							</tr>
							<tr v-if="user.downloads && user.downloads.resignation">
								<td>
									<span>Заявление об увольнении</span>
								</td>
								<td>
									<a
										:href="`/static/profiles/${user.id}/resignation/${user.downloads.resignation}`"
										download
										class="d-block"
									>Скачать</a>
								</td>
							</tr>
							<tr v-if="user.fire_cause">
								<td>
									<span>Причина увольнения</span>
								</td>
								<td>
									<span>{{ user.fire_cause }}</span>
								</td>
							</tr>
						</template>
					</template>
				</tbody>
			</table>
		</div>
	</div>
</template>