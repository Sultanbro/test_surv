<template>
  <ul class="dragArea">
    <li 
        v-for="el in tasks"
          class="chapter opened"
          :class="{
            'pass' : el.item_model !== null,
            'active': active == el.id
          }"
          :id="el.id"
          :key="el.id">
        <div class="d-flex titles">
          <div class="handles d-flex aic" >
            <div>
              <i class="fa fa-arrow-right pointer" v-if="active == el.id"></i>
              <i class="fa fa-check pointer" v-else-if="el.item_model !== null"></i>
              <i class="fa fa-lock pointer" v-else></i>
            </div>
          </div>
          <p class="mb-0" @click="showPage(el.id)">
            {{ el.title }}
            <span class="long">{{ el.title }}</span>
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
  data() {
    return {

    }
  },
  created() {
    
  },
  methods: {
    showPage(id) {
      this.$emit('showPage', id);
    },
  },
};
</script>
