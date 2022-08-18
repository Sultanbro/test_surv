<template>
  <ul class="dragArea">
    <li 
        v-for="el in tasks"
          class="chapter opened"
          :class="{
            'pass' : el.item_model !== null,
            'active': active == el.id,
            'disabled' : el.item_model === null && first_active != el.id
          }"
          :id="el.id"
          :key="el.id">
        <div class="d-flex titles">
          <div class="handles d-flex aic" >
            <div>
              <i class="fa fa-check pointer" v-if="el.item_model != null"></i>
              <i class="fa fa-arrow-right pointer" v-else-if="first_active == el.id"></i>
              <i class="fa fa-lock pointer" v-else></i>
            </div>
          </div>
          <p class="mb-0" @click="showPage(el.id)">
            {{ el.title }}
          </p>
        </div>
        <nested-course
          :tasks="el.children"
          @showPage="showPage"
          :active="active" 
        />
    </li>
  </ul>
</template>
<script>
export default {  
  name: "nested-course",
  props: {
    tasks: {
      required: true,
      type: Array
    },
    active: {
      default: 0
    },
  },
  watch: {
    active: function(v) {
      if(this.first_active == 0) this.first_active = this.active
      console.log('watch actibe')
    }
  },
  data() {
    return {
      first_active: 0
    }
  },
  methods: {
    showPage(id) {

      let item = null;
      let i = this.tasks.findIndex(el => el.id == id);
      if(i != -1) {
        item = this.tasks[i]
      }

      console.log('nested SHowpage')
      console.log(item)
      console.log(this.first_active)

      if(id != this.first_active) {
        if(item != null && item.item_model == null) return; 
      } 
 
      console.log('nested SHowpage emit')
      this.$emit('showPage', id);
    },
  },
};
</script>
