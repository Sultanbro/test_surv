<template>
<div>
    <a class="btn btn-sm btn-primary text-white rounded" @click="toggle()">Настроить KPI</a>

    <sidebar
      title="Условия расчета KPI"
      :open="showKpiSidebar"
      @close="toggle()"
      width="70%"
    >
      <t-kpi 
        :activeuserid="activeuserid"
        group="Настроить kpi по групповым итогам"
        :group_id="user_id"
        :is_admin="true"
        :oklad="0"
        type="individual"
      />
      
    </sidebar>

</div>
</template>
    
<script>
export default {
    name: "ProfileKpiButton",
    props: {
        user_id: Number,
        activeuserid: Number,
    },
    data() {
        return {
            showKpiSidebar: false,
        };
    },
    created() {

    },
    methods: {
        toggle() {
            this.showKpiSidebar = !this.showKpiSidebar
            document.getElementById('list-example').classList.toggle("sticky");
        },
    },
};
</script>

<style lang="scss">

</style>
    