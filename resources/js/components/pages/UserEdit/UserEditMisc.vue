<script>
export default {
	name: 'UserEditMisc',
	props: {
		user: {
			type: Object,
			default: null
		},
	}
}
</script>
<template>
	<div
		id="other_data"
		class="none-block"
	>
		<div class="col-md-12 mt-3">
			<h5 class="mb-4">
				Прочие данные
			</h5>
		</div>

		<div class="col-md-8 ">
			<div class="form-group row">
				<label
					for="description"
					class="col-sm-4 col-form-label font-weight-bold"
				>Примечание для внешнего рекрутера</label>
				<div class="col-sm-8">
					<textarea
						id="recruiter_comment"
						name="recruiter_comment"
						class="form-control"
						placeholder="Коментарии"
						:value="user ? user.recruiter_comment : ''"
					/>
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<div
				v-if="user && user.lead"
				class="table-responsive"
			>
				<table class="my-table table user-list">
					<thead>
						<tr>
							<th colspan="2">
								<span>Битрикс</span>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<span>Сегмент</span>
							</td>
							<td>
								<span>{{ user.segment }}</span>
							</td>
						</tr>

						<tr v-if="user.lead.lead_id">
							<td>
								<span>Лид</span>
							</td>
							<td>
								<span>
									<a
										:href="`https://infinitys.bitrix24.kz/crm/lead/details/${user.lead.lead_id}/`"
										target="_blank"
									>{{ user.lead.lead_id }}</a>
								</span>
							</td>
						</tr>

						<tr v-if="user.lead.deal_id">
							<td>
								<span>Сделка</span>
							</td>
							<td>
								<span>
									<a
										:href="`https://infinitys.bitrix24.kz/crm/lead/details/${user.lead.deal_id}/`"
										target="_blank"
									>{{ user.lead.deal_id }}</a>
								</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</template>