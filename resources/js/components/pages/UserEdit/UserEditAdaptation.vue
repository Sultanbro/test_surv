<script>
export default {
	name: 'UserEditAdaptation',
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
        v-if="user"
        id="adaptation_conversations"
        class="none-block"
    >
        <h5 class="adaptation-title mb-5">Адаптационные беседы</h5>
        <div
            v-for="(talk, key) in user.adaptation_talks"
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
                >{{ talk.text }}</textarea>
            </div>
        </div>
    </div>
</template>