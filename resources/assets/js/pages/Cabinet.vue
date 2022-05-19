<template>
<div class="d-flex">
  <div class="lp">
    <h1 class="page-title">Настройка кабинета</h1>
    
 

   
  </div>


  <div class="rp" style="flex: 1 1 0%; padding-bottom: 50px;">
    <div class="hat">
      <div class="d-flex jsutify-content-between hat-top">
        <div class="bc">
          <a href="#">Настройка кабинета</a>
          
        </div>
        <div class="control-btns"></div> 
      </div>
    </div>
    <div class="content mt-3">

      

        <div class="form-group">
          Субдомен
          <input class="form-control" id="view_own_orders" type="text"/>
        </div>

         <div class="form-group">
          Часовой пояс
          <input class="form-control" id="view_own_orders" type="text"/>
        </div>


        <div class="form-group">
          Администраторы
          <input class="form-control" id="view_own_orders" type="text"/>
        </div>

    

  


    </div>
  </div>

 

</div>
</template>
<script>
export default {
  name: "Cabinet",
  data() {
    return {
      test: 'dsa',
      items: [],
      activeCourse: null,
    };
  },
  created() {

  },
  mounted() {},
  methods: {
    
  },
};
</script>
