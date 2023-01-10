<template>
  <div
    class="profile__about"
    :class="{
      'hidden': data.user === undefined || data.user === null,
      'v-loading': loading
    }"
  >

      <template v-if="data.user !== undefined && data.user !== null">
        <div class="profile__name">{{ data.user.name + ' ' + data.user.last_name }}</div>
        <div class="profile__job profile-border">{{ data.position != null ? data.position.position : 'Без должности' }}</div>
        <div class="profile__job profile-border py-2" v-html="data.groups"></div>
        <div class="profile__salary profile-border">ОКЛАД: {{ data.salary }}</div>
        <div class="profile__wrapper">
            <p class="profile-border">{{ data.workingDay }}</p>
            <p class="profile-border">{{ data.schedule }}</p>
            <p class="profile-border">{{ data.workingTime }}</p>
        </div>
      </template>

      <!-- <select class="select-css" v-model="data.currency">
          <option v-for="key in Object.keys(data.currencies)" :value="key">
            {{ key }} {{ data.currencies[key] }}
          </option>
      </select> -->
  </div>
  </template>

<script>
export default {

	name: 'ProfileInfo',
	props: {
		data: Object
	},

	data() {
		return {
			loading: false
		}
	},

	created() {
		// this.fetchData()
	},

	methods: {
		/**
       * Загрузка данных
       */
		fetchData() {
			this.loading = true

			this.axios.get('/profile/personal-info')
				.then(response => {
					this.data = response.data
					this.loading = false
				}).catch((e) => console.log(e))
		},
	}
}
</script>

  <style lang="scss" scoped>

  </style>
