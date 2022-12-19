<template>
  <div>
      <b-row class="align-items-center">
          <b-col cols="12" lg="4">
             <b-form-group label="Должность">
                 <b-form-select v-model="activebtn" :options="positions" size="md" @change="selectPosition" class="group-select col-lg-6 d-flex">
                     <template #first>
                         <b-form-select-option :value="null" disabled>Выберите должность из списка</b-form-select-option>
                     </template>
                 </b-form-select>
             </b-form-group>
          </b-col>
          <b-col cols="12" lg="4">
              <b-form-group label="Добавить должность" class="add-grade">
                  <b-form-input type="text" v-model="new_position"></b-form-input>
                  <button @click='addPosition' class="btn btn-success ml-4" title="Добавить должность"><i class="fa fa-plus"></i></button>
              </b-form-group>
          </b-col>
          <b-col cols="12" lg="4">
              <b-form-group label="Название должности">
                  <b-form-input type="text" class="form-control group-select" v-model="new_name"></b-form-input>
              </b-form-group>
          </b-col>
      </b-row>

      <template v-if="activebtn != null">
          <b-row class="align-items-center my-4">
              <b-col cols="12" md="4">
                  <b-form-group label="Сумма индексации">
                      <b-form-input type="text" class="form-control group-select" v-model="sum"
                                    v-if="indexation"></b-form-input>
                      <b-form-input type="text" class="form-control group-select" v-model="sum" v-else
                                    disabled></b-form-input>
                  </b-form-group>
              </b-col>
              <b-col cols="12" md="4">
                  <b-form-group class="mt-5">
                      <b-form-checkbox
                              v-model="indexation"
                              :value="1"
                              :unchecked-value="0"
                              switch
                      >
                          Индексация зарплаты
                      </b-form-checkbox>
                  </b-form-group>
              </b-col>
          </b-row>
          <b-row>
              <b-col cols="12" class="my-4">
                  <b-form-checkbox
                          v-model="desc.show"
                          :value="1"
                          switch
                          :unchecked-value="0"
                  >
                      Показывать таблицу в профиле
                  </b-form-checkbox>
              </b-col>

              <b-col cols="12" class="my-4">
                  <div class="table-container">
                      <b-table-simple class="table b-table table-sm pos-desc pos-desc-1">
                         <b-thead>
                             <b-tr>
                                 <b-th>Следующая ступень карьерного роста</b-th>
                                 <b-th>Требования к кандидату</b-th>
                                 <b-th>Что нужно делать</b-th>
                                 <b-th>График работы</b-th>
                                 <b-th>Заработная плата</b-th>
                                 <b-th>Нужные знания для перехода на следующую должность</b-th>
                             </b-tr>
                         </b-thead>
                       <b-tbody>
                           <b-tr>
                               <b-td><b-textarea v-model="desc.next_step"></b-textarea></b-td>
                               <b-td><b-textarea v-model="desc.require"></b-textarea></b-td>
                               <b-td><b-textarea v-model="desc.actions"></b-textarea></b-td>
                               <b-td><b-textarea v-model="desc.time"></b-textarea></b-td>
                               <b-td><b-textarea v-model="desc.salary"></b-textarea></b-td>
                               <b-td><b-textarea v-model="desc.knowledge"></b-textarea></b-td>
                           </b-tr>
                       </b-tbody>
                      </b-table-simple>
                  </div>
              </b-col>
          </b-row>
          <div class="mt-3">
              <button @click='savePosition' class="btn btn-success mr-2">Сохранить</button>
              <button @click.stop="deletePosition(position_id,activebtn)" class="btn btn-danger mr-2"><i
                      class="fa fa-trash mr-2"></i> Удалить
              </button>
          </div>
      </template>
  </div>


</template>

<script>
export default {
  name: "professions",
  props: ['positions'],
  data() {
    return {
      data: this.positions,
      new_name: '',
      new_position: '',
      position_id: 0,
      indexation: 0,
      sum: 0,
      activebtn: null,
      desc: {
        require: '',
        actions: '',
        time: '',
        salary:'',
        knowledge:'',
        next_step:'',
        show: 0
      }
    }
  },
  mounted() {
    // this.getPositions()
  },
  methods: {

    selectPosition(value) {

        this.activebtn = value
        console.log(this.activebtn)

        axios.post('/timetracking/settings/positions/get', {
          name: this.activebtn,
        }).then(response => {
          //this.$toast.info('Добавлена');
          console.log(response.data)
          this.new_name = response.data.position;
          this.position_id = response.data.id;
          this.indexation = response.data.indexation;
          this.sum = response.data.sum;
          this.desc = response.data.desc;
          
        }).catch(error => {
          console.log(error.response)
        })
        
        
  // if (response.data) {
  //                   this.gname = this.activebtn
  //                   this.value = response.data.users
  //                   this.bgs = response.data.book_groups
  //                   this.timeon = response.data.timeon
  //                   this.timeoff = response.data.timeoff
  //                   this.group_id = response.data.group_id
  //                   this.zoom_link = response.data.zoom_link
  //                   this.bp_link = response.data.bp_link
  //               } else {
  //                   this.value = []
  //               }
    },  

    addPosition() {
      axios.post('/timetracking/settings/positions/add', {
        position: this.new_position,
      }).then(response => {
        if(response.data.code == 201) {
          this.$toast.error('Должность с таким названием уже существует!');
        } else {
           this.$toast.success('Добавлена');
           this.positions.push(response.data.pos)
          this.new_position = ''
        }
       
      }).catch(error => {
        console.log(error.response)
      })
    },
    savePosition() {



        axios.post('/timetracking/settings/positions/save', {
          id: this.position_id,
          new_name: this.new_name,
          indexation: this.indexation,
          sum: this.sum,
          desc: this.desc,
        }).then(response => {
          this.positions = Object.values(response.data.positions)
          this.$toast.success('Сохранено');
          this.activebtn = response.data.pos.position;
        }).catch(error => {
          console.log(error.response)
        })
    },
    deletePosition(index, status) {
         if (confirm('Вы уверены что хотите удалить должность?')) {
            axios.post('/timetracking/settings/positions/delete', {
                    position: status,
                })
                .then(response => {
                    this.$toast.info('Удалена');
                })

            let ind = this.positions.indexOf(status);
            this.positions.splice(ind, 1)
            this.activebtn = null
        }
    },

  }
}
</script>

<style scoped lang="scss" >
.listprof {
  display: flex;
  margin-top: 20px;
}

.profitem {
  margin-right: 10px;
}

.add-grade {
  display: flex;
  max-width: 500px;
}.ant-tabs {
    overflow: visible;
}
.listprof {
    display: flex;
    flex-wrap: wrap;
    margin-top: 20px;
}

.profitem {
    margin-right: 10px;
    margin-bottom: 5px;
}

.add-grade {
    display: flex;
    max-width: 500px;
}

.dialerlist {
    display: flex;
    align-items: center;
    margin: 0;
    &.bg {
        background: #f1f1f1;
        padding-left: 15px;
    }
    .fl {
        flex: 1;
        display: flex;
        align-items: center;
    }
}

.group-select {
    border-radius: 0;
    max-width: 100%;
}

p.choose {
    line-height: 31px;
    margin-right: 15px;
}
span.before {
    padding: 0 10px;
}
.multiselect__tags {
    border-radius: 0 !important;
}
.multiselect__tag {
    display: block !important;
    max-width: max-content !important;
}
.blu .multiselect__tag {
    background: #017cff !important;
}
.pos-desc td {
  padding: 0; 
}
.pos-desc td textarea {
      font-size: 12px;
    resize: none;
    overflow: auto;
    min-height: 350px;
}

@media(min-width: 1000px) {
    .multiselect__tags-wrap {
        flex-wrap: wrap;
        display: flex !important;
    }
    .multiselect__tag {
        flex: 0 0 49%;
        /* margin-left: 1% !important; */
        margin-right: 1% !important;
        max-width: 49% !important;
    }
}

@media(min-width: 1300px) {
    .multiselect__tag {
        flex: 0 0 32%;
        /* margin-left: 1% !important; */
        margin-right: 1% !important;
        max-width: 32% !important;
    }
}
@media(min-width: 1700px) {
    .multiselect__tag {
        flex: 0 0 24%;
        /* margin-left: 1% !important; */
        margin-right: 1% !important;
        max-width: 24% !important;
    }    
}
.scscsc {
    margin-left: 15px;
}
.sssz button {
    margin-top: 1px;
}
.add-grade input {
    border-radius: 0;
}
.p {
  font-size: 14px;
  width: 200px;
    color: #5a5a5a;
}
</style>
