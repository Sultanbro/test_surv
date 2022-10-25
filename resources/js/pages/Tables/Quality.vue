<template>
  <div class="mt-5 index__content">

      <div class="mb-3">
        Кол-во показателей: <b>{{total_count}}</b> ,
        Среднее значение: <b>{{ total_avg }}</b>
      </div>

      <div class="">
          <table class="class">
              <tr>
                  <th class="b-table-sticky-column text-left">
                      <div>Сотрудник</div>  
                  </th>
                  <template v-for="(field, key) in fields">
                      <th :class="field.class">
                          <div>{{ field.name }}</div>
                      </th>
                  </template>
              </tr>
              <tr v-for="(item, index) in users" :key="index"> 
                  <td class="b-table-sticky-column text-left">
                      <div class="d-flex">{{ item.name }}
                          
                      </div> 
                  </td>
                  <template v-for="(field, key) in fields"> 
                      <td :class="field.class" :key="key">
                          <div v-if="item[field.key] != 0">{{ item[field.key] }}</div>
                      </td>
                  </template>
              </tr>
          </table>
      </div>
  </div>
</template>
  
<script>
export default {
    name: "TableQualityWeekly",
    props: {
        monthInfo: Object,
        items: Array,
    },
    data() {
        return {
            users: [],
            fields: [],
            user_ids: {},
            total_avg: 0,
            total_count: 0,
            loader: null,
        };
    }, 

    created() {
        this.setWeeksTableFields()
        this.users = this.items;
        this.setLeaders();
    },

    methods: {
      /**
       * set leaders
       */
      setLeaders() {
          this.users.forEach(item => {
              item.show_cup = 0;
          });


          let arr = this.users;
          arr.sort((a, b) => Number(a.total) < Number(b.total)  ?
              1 : Number(a.total) > Number(b.total) ? -1 : 0);

          if(this.users.length > 3) {
              arr[0].show_cup = 1;
              arr[1].show_cup = 2;
              arr[2].show_cup = 3;
          } 

          let total_avg = 0;
          let total_count = 0;

          arr.forEach(el => {
              if(Number(el.total) > 0) {
                  total_avg += Number(el.total);
                  total_count++;
              }
          })

          total_avg = total_count > 0 ? total_avg / total_count : 0;

          this.total_avg = total_avg
          this.total_count = total_count
      },
        
      /**
       * set fields
       */
      setWeeksTableFields() {
          
          let fieldsArray = []
          let weekNumber = 1;
          let order = 1;

          fieldsArray.push({
              key: 'total',
              name: 'Итог',
              order: order++,
              class: ' text-center t-total'
          })  
          
          for(let i = 1; i <= this.monthInfo.daysInMonth; i++) {

              let m = this.monthInfo.month.toString()
              let d = i
              if(d.toString().length == 1) d = '0' + d;
              if(m.length == 1) m = '0' + m;

              let date = moment(this.monthInfo.currentYear + '-' + m + '-' + d); 
              let dow = date.day();
              
              fieldsArray.push({
                  key: i,
                  name: i,
                  order: order++,
                  class: 'text-center',
                  type: 'day'
              })

              if(dow == 0) {
                  fieldsArray.push({
                      key: 'avg' + weekNumber,
                      name: 'Ср. ' + weekNumber ,
                      order: order++,
                      class: 'text-center averages',
                      type: 'avg'
                  })
                  weekNumber++
              }

              if(dow != 0 && i == this.monthInfo.daysInMonth) {
                  fieldsArray.push({
                      key: 'avg' + weekNumber,
                      name: 'Ср. ' + weekNumber,
                      order: order++,
                      class: 'text-center averages',
                      type: 'avg'
                  })
              }
          } 

          this.fields = fieldsArray
      },
    },
};
</script>
 