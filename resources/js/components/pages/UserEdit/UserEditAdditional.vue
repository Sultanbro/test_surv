<script>
export default {
	name: 'UserEditAdditional',
	props: {
		user: {
			type: Object,
			default: null
		},
		userCreated: {
			type: String,
			default: ''
		},
		userApplied: {
			type: String,
			default: ''
		},
		userAppliedDays: {
			type: Number,
			default: 0
		},
		isTrainee: {
			type: Boolean
		},
		userDeleted: {
			type: String,
			default: ''
		},
		userDeletedAt: {
			type: String,
			default: ''
		},
		history: {
			type: Array,
			default: null
		}
	},
	data(){
		return {
			isBP: ['bp', 'test'].includes(location.hostname.split('.')[0])
		}
	}
}
</script>

<template>
	<div
		id="add_info"
		class="UserEditAdditional none-block"
	>
		<div class="UserEditAdditional-scroll">
			<h5 class="UserEditAdditional-title add-info-title">
				Дополнительная информация
			</h5>
			<table
				v-if="history"
				class="UserEditAdditional-history"
			>
				<colgroup>
					<col class="UserEditAdditional-labels">
					<col class="UserEditAdditional-values">
				</colgroup>
				<tbody>
					<tr v-if="isBP && user && user.lead && (user.lead.lead_id || user.lead.deal_id)">
						<td
							v-if="user.lead.lead_id"
							:colspan="user.lead.deal_id ? 1 : 2"
						>
							<a
								:href="`https://infinitys.bitrix24.kz/crm/lead/details/${user.lead.lead_id}/?any=details%2F${user.lead.lead_id}%2F`"
								target="_blank"
							>
								Ссылка на лид
							</a>
						</td>
						<td
							v-if="user.lead.deal_id"
							:colspan="user.lead.lead_id ? 1 : 2"
						>
							<a
								:href="`https://infinitys.bitrix24.kz/crm/deal/details/${user.lead.deal_id}/?any=details%2F${user.lead.deal_id}%2F`"
								target="_blank"
							>
								Ссылка на сделку
							</a>
						</td>
					</tr>
					<tr>
						<td>
							<span>Дата регистрации</span>
						</td>
						<td>
							<span>{{ userCreated }}</span>
						</td>
					</tr>
					<tr>
						<td>
							<span>Дата принятия на работу</span>
						</td>
						<td>
							<span>{{ !userApplied && !isTrainee ? userCreated : userApplied }}</span>
						</td>
					</tr>
					<tr v-if="user && userAppliedDays">
						<td>
							<span>Успел стать частью команды ~</span>
						</td>
						<td>
							<span>{{ userAppliedDays }} дней</span>
						</td>
					</tr>
					<tr
						v-for="hist, index in history"
						:key="index"
					>
						<td>
							{{ hist.label }}
						</td>
						<td>
							{{ hist.date }}
							<i>{{ hist.cause || '' }}</i>
						</td>
					</tr>
				</tbody>
			</table>


			<table
				v-else
				class="table"
			>
				<colgroup>
					<col class="UserEditAdditional-labels">
					<col class="UserEditAdditional-values">
				</colgroup>
				<tbody>
					<tr v-if="isBP && user && user.lead && (user.lead.lead_id || user.lead.deal_id)">
						<td
							v-if="user.lead.lead_id"
							:colspan="user.lead.deal_id ? 1 : 2"
						>
							<a
								:href="`https://infinitys.bitrix24.kz/crm/lead/details/${user.lead.lead_id}/?any=details%2F${user.lead.lead_id}%2F`"
								target="_blank"
							>
								Ссылка на лид
							</a>
						</td>
						<td
							v-if="user.lead.deal_id"
							:colspan="user.lead.lead_id ? 1 : 2"
						>
							<a
								:href="`https://infinitys.bitrix24.kz/crm/deal/details/${user.lead.deal_id}/?any=details%2F${user.lead.deal_id}%2F`"
								target="_blank"
							>
								Ссылка на сделку
							</a>
						</td>
					</tr>
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

<style lang="scss">
.UserEditAdditional{
	position: relative;

	&-title{
		margin: 0 !important;
		padding: 5px 20px !important;
		border-bottom: 1px solid #dee2e6;

		position: sticky;
		top: 0;

		background-color: #fff;
		border-radius: 10px 10px 0 0;
	}
	&-scroll{
		max-height: 199px;
		overflow-y: auto;
	}
	&-history{
		width: 100%;
		height: 100%;
		margin: 0;
		border: none;

		vertical-align: top;
		font-size: 1.4rem;

		td{
			padding: 5px 20px;
			border: 1px solid #dee2e6;
			&:first-child{
				border-left: none !important;
			}
			&:last-child{
				border-right: none !important;
			}
		}

		tr{
			&:first-child{
				td{
					border-top: none;
				}
			}
		}
	}
}
</style>
