<template>
  <div>

      <div class="row align-items-center">
          
          <div class="col-lg-3 col-md-6">
              <b-form-select v-model="activebtn" :options="positions" size="md" @change="selectPosition" class="group-select col-lg-6 d-flex">
                  <template #first>
                      <b-form-select-option :value="null" disabled>Выберите должность из списка</b-form-select-option>
                  </template>
              </b-form-select>
          </div>
          <div class="col-lg-3 col-md-6">
              
              <div class="add-grade">
                  <input type="text" class="form-control" v-model="new_position">
                  <button @click='addPosition' class="btn btn-success">Добавить должность</button>
              </div>
          </div>
          <div class="col-lg-3 col-md-6 " style="text-align:right">
              Название должности
          </div>
          <div class="col-lg-3 col-md-6">
              <input type="text" class="form-control group-select" v-model="new_name">
          </div>
      </div>

      <div v-if="activebtn != null" class="row align-items-center">
          
          
          <div class="col-lg-2 mb-3 mt-3">
            <b-form-checkbox
                    v-model="indexation"
                    :value="1"
                    :unchecked-value="0"
                    >
                    Индексация зарплаты 
                </b-form-checkbox>
          </div>

          <div class="col-lg-4 col-md-6 mb-3 mt-3 d-flex align-items-center" >
            <p class="p mr-3 mb-0">Сумма индексации</p>
            <input type="text" class="form-control group-select" v-model="sum" v-if="indexation">
            <input type="text" class="form-control group-select" v-model="sum" v-else disabled>
          </div>

          <div class="col-lg-12 mb-3 mt-3">
            <b-form-checkbox
                    v-model="desc.show"
                    :value="1"
                    :unchecked-value="0"
                    >
                    Показывать таблицу в профиле
                </b-form-checkbox>
          </div>

          <div class="col-lg-12 mb-3 mt-3">
            <table class="table b-table table-striped table-bordered table-sm pos-desc pos-desc-1">
              <tr>
                <th>Следующая ступень карьерного роста</th>
                <th>Требования к кандидату</th>
                <th>Что нужно делать</th>
                <th>График работы</th>
                <th>Заработная плата</th>
                <th>Нужные знания для перехода на следующую должность</th>
              </tr>
              <tr>
                <td><textarea v-model="desc.next_step" class="form-control"></textarea></td>
                <td><textarea v-model="desc.require" class="form-control"></textarea></td>
                <td><textarea v-model="desc.actions" class="form-control"></textarea></td>
                <td><textarea v-model="desc.time" class="form-control"></textarea></td>
                <td><textarea v-model="desc.salary" class="form-control"></textarea></td>
                <td><textarea v-model="desc.knowledge" class="form-control"></textarea></td>
              </tr>
            </table>
          </div>



          <div class="col-lg-12 mb-3 mt-3">
              <button @click='savePosition' class="btn btn-success mr-2">Сохранить</button>
              <button @click.stop="deletePosition(position_id,activebtn)" class="btn btn-danger mr-2"><i class="fa fa-trash"></i> Удалить</button>
          </div>

      </div>

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
          //this.$message.info('Добавлена');
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
          this.$message.error('Должность с таким названием уже существует!');
        } else {
           this.$message.success('Добавлена');
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
          this.$message.success('Сохранено');
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
                    this.$message.info('Удалена');
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
    background: #29a746 !important;
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
    overflow: hidden;
    min-height: 350px;
}
.pos-desc-1 th,
.pos-desc-1 td {
  &:nth-child(1) {
    width: 16.666%;
  }
  &:nth-child(2) {
   width: 16.666%;
  }
  &:nth-child(3) {
    width: 12.222%
  }
  &:nth-child(4) {
    width: 10.222%;
  }
  &:nth-child(5) {
    width: 16.666%;
  }
  &:nth-child(6) {
    width: 12.222%;
  }
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
