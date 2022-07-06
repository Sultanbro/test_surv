<template>
<div class="v-list">
    <draggable 
        class="dfsdf" 
        tag="div"
        handle=".fa-bars"
        :list="videos"
        :group="{ name: 'g1' }"
        @end="saveOrder"
    >
        <div class="video-block" v-for="(video, v_index) in videos"
                :key="video.id"
                :class="{
                    'active': (active == video.id),
                    'disabled': is_course && video.item_models.length == 0
                }"
                @click="showVideo(video, v_index)"
            >
            <div class="mover" v-if="mode == 'edit' && !group_edit">
                <i class="fa fa-bars"></i> 
            </div>
            <div class="img">
                <img src="/images/author.jpg" alt="text" /> 
            </div>
            <div class="desc"> 
                <h4>
                    <i class="fa fa-lock mr-1"></i>     
                    {{ video.title }}
                </h4>
                <div class="text" v-html="video.desc"></div>
            </div>
            <div class="controls d-flex" 
                v-if="mode == 'edit' && !group_edit"
            >
                <i  class="fa far fa-trash mr-3" 
                    title="Убрать из плейлиста" 
                    @click.stop="$emit('deleteVideo', {
                        video: video, 
                        v_index: v_index,
                        g_index: g_index,
                        c_index: c_index
                    })"
                ></i>
                <i class="fa fa-arrow-alt-circle-right" title="Переместить из группы"></i>
            </div>
        </div>
    </draggable>
</div>
</template>

<script>
export default {
    name: 'VideoList',
    props: ['videos', 'mode','group_edit', 'g_index', 'c_index', 'active' , 'is_course'],
    data(){
        return {
          
        }
    },

    created() {
        let i = this.videos.findIndex(el => el.id == this.active);
   
        if(i != -1) {
            this.videos[i].item_models.push({status:0})
        }
    },

    methods: {
        saveOrder(e) {

        },

        showVideo(video, v_index) {
            if(this.is_course && video.item_models.length == 0) return;
            this.$emit('showVideo', video, v_index);
        }
    }
}
</script>