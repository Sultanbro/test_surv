<template>
<div class="profile__about" v-if="data.user !== undefined && data.user !== null">

    <div class="profile__name">{{ data.user.last_name + ' ' + data.user.name }}</div>
    <div class="profile__job profile-border">{{ data.position.position }}</div>
    <div class="profile__salary profile-border">ОКЛАД {{ data.salary }}</div>
    <div class="profile__wrapper">
        <p class="profile-border">{{ data.workingDay }}</p>
        <p class="profile-border">{{ data.schedule }}</p>
        <p class="profile-border">{{ data.workingTime }}</p>
    </div>

    <select class="select-css" v-model="data.currency">
        <option v-for="key in Object.keys(data.currencies)" :value="key">
          {{ key }} {{ data.currencies[key] }}
        </option>
    </select>
</div>
</template>

<script>
export default {

  name: 'ProfileInfo',
  props: {

  },

  data() {
    return {
      data: {
        currencies: {},
        currency: 'KZT'
      },
    }
  },

  created() {
    this.fetchData()
  },

  methods: {
      /**
       * Загрузка данных 
       */
      fetchData() {
        let loader = this.$loading.show();

        axios.post('/profile/personal-info', {
            month: new Date().getMonth() + 1
        }).then(response => {
            this.data = response.data
            loader.hide()
        }).catch((e) => console.log(e))
    },
  }
}
</script>

<style lang="scss" scoped>

</style>
