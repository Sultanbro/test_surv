<template>
<div>
  <a-modal title="Настройка доступа" v-model="openPremissionModal" @ok="savePremission">
    Выберите сотрудников, которым будет разрешено редактировать время

    <multiselect v-model="group_editors" :options="users" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Выберите" label="email" track-by="email">
      <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} выбрано</span>
      </template>
    </multiselect>
  </a-modal>

  <div class="text-right mb-3">

    <a href="#" class="btn btn-primary rounded" @click.prevent="openPremissionModal = true">
      <i class="fa fa-cogs"></i> Доступ</a>

  </div>
</div>
</template>

<script>

import {bus} from '../../bus';

export default {
  name: "GroupPremission",
  props: {
    currentGroup: Number,
    page: String
  },
  watch: {
    currentGroup(val) {
      this.loadEditors()
    }
  },
  data() {
    return {
      openPremissionModal: false,
      users: [],
      group_editors: []
    }
  },
  mounted() {
    bus.$on('checkPremissions', this.checkPremissions)
  },
  created() {
    this.loadEditors()
    axios.post('/timetracking/users', {})
      .then(response => {
        this.users = response.data.users
      })

  },
  methods: {
    loadEditors() {
      axios.post('/timetracking/reports/get-editors', {
        group_id: this.currentGroup,
        page: this.page
      }).then(response => {
        this.group_editors = response.data
      })
    },
    savePremission() {
      axios.post('/timetracking/reports/add-editors', {
        users: this.group_editors,
        group_id: this.currentGroup,
        page: this.page
      }).then(response => {}).catch(error => {
        console.log(error)
      });
      this.openPremissionModal = false

    },
    checkPremissions(activeuserid) {
      
      let premission = false
      this.group_editors.forEach(editor => {
        if (editor.ID == parseInt(activeuserid)) premission = true
      })
      console.log(premission)
      return premission;
    }
  }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style><style lang="scss">
