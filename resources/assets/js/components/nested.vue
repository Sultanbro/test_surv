<template>
  <draggable 
    class="dragArea" 
    tag="ul"
    :handle="handle"
    :list="tasks"
    :group="{ name: 'g1' }"
    @end="saveOrder">
    <template v-for="el in tasks">
        <li v-if="el.opened" 
          class="chapter"
          :id="el.id"
          @mouseover="hover = true"
          @mouseleave="hover = false"
          :key="el.id">
        <div class="d-flex">
          <div class="handles" >
            <i class="fa fa-bars mover" v-if="hover"></i>
            <i class="fa fa-chevron-right pointer" v-else-if="el.children.length > 0"></i>
            <i class="fa fa-caret-right pointer" v-else></i>
          </div>
          <p @click="toggleOpen(el)">{{ el.title }}</p>
        </div>
        <nested-draggable :tasks="el.children" @showPage="showPage" :parent_id="el.id" :auth_user_id="auth_user_id" />
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
        console.log(el)
      this.showPage(el.id);
      el.children.forEach(child => {
          child.opened = !child.opened;
      });
      
      
    },  
    showPage(id) {
       this.$emit('showPage', id);
    },
    
    saveOrder(event) {

        console.log(event)

        let parent_id = null;
        if(event.to.parentElement.nodeName != "ASIDE") {
          parent_id = event.to.parentElement.id;
        } else {
          parent_id = this.parent_id
        }

        axios.post('/kb/page/save-order', {
          id: event.item.id,
          order: event.newIndex, // oldIndex
          parent_id: parent_id
        })
        .then(response => {
           
        })
    },

    log(e) {
      console.log(e)
    }
  },
  name: "nested-draggable"
};
</script>
