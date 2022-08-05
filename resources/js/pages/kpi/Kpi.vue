<template>
<div>
        <div class="d-flex mb-2 mt-2 jcfe">
            <button class="btn btn-primary rounded" @click="addKpi">Добавить</button>
        </div>
      <table class="table table-hover">
        <thead>
        <tr>
            <th scope="col">Сотрудники / Отдел</th>
           
            <th scope="col">Постановщик <i class="fa fa-info-circle" style="cursor: pointer"
                    v-b-popover.hover.right.html="'Сasdasdas'"
                    title="asdads">
                </i></th>
            <th scope="col" >Статистика</th>
            <th></th>
        </tr>
        </thead>
        <tbody>

            <tr class="p-0" v-for="(item, index) in items" :key="`employee-${index}`">
                <td>
                    {{ item.name }}
                </td>
                <td>
                    Али Акпанов
                </td>
                <td class=" position-relative" >
                    <i class="btn btn-primary rounded fa fa-edit" @click="showStat"></i>   
                </td>
                 <td class=" position-relative" >
                    <i class="btn btn-primary rounded fa fa-edit" @click="editKpi"></i>
                    <i class="btn btn-primary rounded fa fa-trash" @click="deleteKpi"></i>
                </td>
            </tr>

        </tbody>
    </table>



</div>
</template>

<script>
import { ToastPlugin } from 'bootstrap-vue';

export default {
    name: "KPI", 
    props: {

    },
    data() {
        return {
            active: 1,
            items: [
                {name: 'IT отдел'},
                {name: 'Али Акпанов'},
                {name: 'Руслан Ташметов'},
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

        addKpi() {
            this.$toast.info('Добавить KPI');
        },

        editKpi() {
            this.$toast.info('Редактировать KPI');
        }, 

        deleteKpi() {
            this.$toast.info('Удалить KPI');
        },

        showStat() {
            this.$toast.info('Показать статистику');
        }
 
    } 
}
</script>