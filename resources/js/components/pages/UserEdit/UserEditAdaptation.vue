<script>
import UserEditError from './UserEditError.vue'
const DATE_YMD = 'YYYY-MM-DD';
export default {
	name: 'UserEditAdaptation',
	components: {
		UserEditError,
	},
	props: {
		user: {
			type: Object,
			default: null
		},
		errors: {
			type: Object,
			default: () => ({})
		},
	},
	data(){
		return {
			DATE_YMD,
		}
	}
}
</script>

<template>
	<div
		v-if="user"
		id="adaptation_conversations"
		class="none-block"
	>
		<template v-for="(talk, key) in user.adaptation_talks">
			<div
				:key="key"
				class="d-flex phone-row form-group mb-2 adaptation_talk"
			>
				<div class="col-12 col-md-3">
					{{ talk.day }} й день
					<input
						:name="`adaptation_talks[${key}][day]`"
						:value="talk.day"
						type="hidden"
						class="form-control"
					>
				</div>
				<div class="col-12 col-md-3">
					<input
						:name="`adaptation_talks[${key}][inter_id]`"
						type="text"
						class="form-control"
						placeholder="Кто провел"
						:value="talk.inter_id"
					>
				</div>
				<div class="col-12 col-md-3">
					<input
						:name="`adaptation_talks[${key}][date]`"
						:value="talk.date ? $moment(talk.date).format(DATE_YMD) : ''"
						type="date"
						class="form-control"
					>
				</div>
				<div class="col-12 col-md-3">
					<textarea
						:name="`adaptation_talks[${key}][text]`"
						class="form-control"
						placeholder="Комментарии"
						style="min-height: 40px; padding: 5px 20px 0 20px!important;"
						:value="talk.text || ''"
					/>
				</div>
			</div>

			<UserEditError
				:errors="errors"
				:name="`adaptation_talks.${key}.inter_id`"
				:key="'e1' + key"
			/>
			<UserEditError
				:errors="errors"
				:name="`adaptation_talks.${key}.date`"
				:key="'e2' + key"
			/>
			<UserEditError
				:errors="errors"
				:name="`adaptation_talks.${key}.text`"
				:key="'e3' + key"
			/>
		</template>
	</div>
</template>

<style lang="scss">
// .UserEditAdaptation{}
</style>
