<template>
  <draggable 
    class="dragArea" 
    tag="ul"
    :handle="handle"
    :list="tasks"
    :group="{ name: 'g1' }"
    :id="parent_id"
    @end="saveOrder"
    >
    <template v-for="el in tasks" >
        <li 
          class="chapter"
          :class="{'opened':opened}"
          :id="el.id"
          :key="el.id">
        <div class="d-flex">
          <div class="handles d-flex aic" >
            <i class="fa fa-bars mover" v-if="mode == 'edit'"></i>
            <i class="fa fa-circle mover" v-else></i>
            <div class="shower">
              <i class="fa fa-chevron-down pointer" v-if="el.children.length > 0 && el.opened"></i>
              <i class="fa fa-chevron-right pointer" v-else-if="el.children.length > 0"></i>
              <i class="fa fa-circle pointer" v-else></i>
            </div>
            
          </div>
          <p @click.stop="toggleOpen(el)" class="mb-0">{{ el.title }}
            <span class="long">{{ el.title }}</span>
          </p>
           <div class="chapter-btns" v-if="mode == 'edit'">
              <i class="fa fa-plus mr-1" @click.stop="addPage(el)"></i>
            </div>
        </div>
        <nested-draggable :tasks="el.children" @showPage="showPage" @addPage="addPage" :parent_id="el.id" :auth_user_id="auth_user_id" :opened="el.opened"  :mode="mode" />
      </li>
    </template>
  </draggable>
</template>
<script>
export default { 
  props: {
    tasks: {
      required: true,
      type: Array
    },
    parent_id: {
      default: null
    },
    opened: {
      default: false
    },
    auth_user_id: {
      type: Number
    },
    mode: {
      type: String
    }
  },
  data() {
    return {
      hover: false,
      handle: '.fa-t',
    }
  },
  created() {
    if(this.mode == 'edit') {
      this.handle = '.fa-bars';
    }
  },
  methods: {
    toggleOpen(el) {
      this.showPage(el.id, false, true);
      el.opened = !el.opened
    },  
    showPage(id) {
       this.$emit('showPage', id);
    },
    
    addPage(el) {
      this.$emit('addPage', el);
    },
    saveOrder(event) {
        axios.post('/kb/page/save-order', {
          id: event.item.id,
          order: event.newIndex, // oldIndex
          parent_id: event.to.id
        })
        .then(response => {
           this.$message.success('Очередь сохранена');
        })
    },

    log(e) {
      console.log(e)
    }
  },
  name: "nested-draggable"
};
</script>
