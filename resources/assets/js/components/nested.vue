<template>
  <draggable 
    class="dragArea" 
    tag="ul"
    :handle="handle"
    :list="tasks"
    :group="{ name: 'g1' }"
    @end="saveOrder">
    <template v-for="el in tasks" >
        <li 
          v-if="opened"
          class="chapter"
          :class="{'opened':opened}"
          :id="el.id"
          :key="el.id">
        <div class="d-flex">
          <div class="handles d-flex aic" >
            <i class="fa fa-bars mover"></i>
            <div class="shower">
              <i class="fa fa-chevron-down pointer" v-if="el.children.length > 0 && el.opened"></i>
              <i class="fa fa-chevron-right pointer" v-else-if="el.children.length > 0"></i>
              <i class="fa fa-circle pointer" v-else></i>
            </div>
            
          </div>
          <p @click.stop="toggleOpen(el)" class="mb-0">{{ el.title }}</p>
           <div class="chapter-btns">
              <i class="fa fa-plus mr-1" @click.stop="addPage(el)"></i>
            </div>
        </div>
        <nested-draggable :tasks="el.children" @showPage="showPage" @addPage="addPage" :parent_id="el.id" :auth_user_id="auth_user_id" :opened="el.opened" />
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
    }
  },
  data() {
    return {
      hover: false,
      handle: '.fa-t',
    }
  },
  created() {
    if([5,18,157,84].includes(this.auth_user_id)) {
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

      

        let parent_id = null;
        console.log(event)
        if(event.to.parentElement.nodeName != "ASIDE") {
          parent_id = event.to.parentElement.id;

          if(parent_id == '') {
            parent_id = this.parent_id;
            console.log('parent_id == ""');
          } else {
            console.log('parent_id != ""');
          }
        } else {
          console.log('= Aside');
          parent_id = this.parent_id
        }

        console.log(parent_id);
        axios.post('/kb/page/save-order', {
          id: event.item.id,
          order: event.newIndex, // oldIndex
          parent_id: parent_id
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
