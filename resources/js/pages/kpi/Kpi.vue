<template>
<div>
    
      <table class="table table-hover">
        <thead>
        <tr>
            <th scope="col">Сотрудники / Отдел</th>
            <th scope="col">KPI
                <i class="fa fa-info-circle" style="cursor: pointer"
                    v-b-popover.hover.right.html="'Сколько раз будет уведомление Чек лист автоматически будет всплывать в кабинете сотрудника'"
                    title="Работают">
                </i>
            </th>
            <th scope="col">Постановщик</th>
            <th scope="col" >Статистика</th>
            <th></th>
        </tr>
        </thead>
        <tbody>

            <tr class="p-0" v-for="(item, index) in items" :key="`employee-${index}`">
                <td>
                asdasd
                </td>
                <td  >
                asdasd
                </td>
                <td>
                    asdadsd
                </td>
                <td class=" position-relative" >

                    asdasd
                </td>
                 <td class=" position-relative" >

                    asdasd
                </td>
            </tr>

        </tbody>
    </table>



</div>
</template>

<script>
export default {
    name: "KPI", 
    props: {

    },
    data() {
        return {
            active: 1,
            items: [
                {},
                {},
                {},
                {},
                {},
            ]
        }
    },

    created() {
       // this.fetchData()
    },
    methods: {

        fetchData() {
            let loader = this.$loading.show();

            axios.post('/kpi/' + this.page, {
                month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
            }).then(response => {
              
                loader.hide()
            }).catch(error => {
                loader.hide()
                alert(error)
            });
        },
 
    } 
}
</script>