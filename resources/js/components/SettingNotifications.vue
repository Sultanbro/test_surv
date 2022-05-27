<template>
    <div class="n-table">
        <div class="n-row">
            <div><p style="color: rgb(54, 192, 184);">Название</p></div>
            <div><p>Действие</p></div>
            <div><p>Текст уведомления</p></div>
            <div><p>Группы</p></div>
        </div>
        <div class="n-row">
            
        </div>
    </div>
</template>

<script>
/*
  name: "professions",
  props: ['positions'],



addPosition() {
  axios.post('/timetracking/settings/add', {
    position: this.new_position,
  }).then(response => {
    this.$message.info('Добавлена');
    this.getPositions()
    this.new_position = ''
  }).catch(error => {
    console.log(error.response)
  })
},


*/
export default {
  data() {
    return {
      data: this.positions,
      new_position: '',
    }
  },
  mounted() {
    // this.getPositions()
  },
  methods: {
    addPosition() {
        console.log('addPosition');
    },
  }
}
</script>

<style scoped>
.n-table {
    margin: 20px 50px 50px 40px;
    padding: 30px;
    -webkit-box-shadow: 0 3px 41px rgb(2 4 5 / 6%);
    -moz-box-shadow: 0 3px 41px rgba(2, 4, 5, 0.06);
    box-shadow: 0 3px 41px rgb(2 4 5 / 6%);
}
.n-row {
    display: flex;
    border-bottom: 1px solid #e4e4e4;
}
.n-row p {
    width: 100%;
    padding-bottom: 20px;
    color: #202226;
    font-family: "Open Sans";
    font-size: 16px;
    font-weight: 700;
    line-height: 26px;
    text-align: center;
}
.n-row div:nth-child(1) {
    width: 20%;
}
.n-row div:nth-child(2) {
    width: 20%;
}
.n-row div:nth-child(3) {
    width: 40%;
}
.n-row div:nth-child(4) {
    width: 20%;
}
</style>
