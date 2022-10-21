<template>
<div class="profile__about" v-if="data.user !== undefined && data.user !== null">

    <div class="profile__name">{{ data.user.last_name + ' ' + data.user.name }}</div>
    <div class="profile__job profile-border">{{ data.position }}</div>
    <div class="profile__salary profile-border">ОКЛАД {{ data.salary }}</div>
    <div class="profile__wrapper">
        <p class="profile-border">{{ data.workingDay }}</p>
        <p class="profile-border">{{ data.schedule }}</p>
        <p class="profile-border">{{ data.workingTime }}</p>
    </div>

    <select class="select-css">
        <option>KZT Казахстанский тенге</option>
        <option>RUB Казахстанский тенге</option>
        <option>KGS Казахстанский тенге</option>
        <option>KZT Казахстанский тенге</option>
        <option>KZT Казахстанский тенге</option>
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
      data: {},
    }
  },

  methods: {
      /**
       * Загрузка данных 
       */
      fetchData() {
        let loader = this.$loading.show();

        axios.post('/personal-info', {
            month: new Date().getMonth() + 1
        }).then(response => {
            this.data = response.data.data
            loader.hide()
        }).catch((e) => console.log(e))
    },
  }
}
</script>

<style lang="scss" scoped>

</style>
