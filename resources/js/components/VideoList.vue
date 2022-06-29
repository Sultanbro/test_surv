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
                @click="$emit('showVideo', video, v_index)">
            <div class="mover" v-if="mode == 'edit' && !group_edit">
                <i class="fa fa-bars"></i> 
            </div>
            <div class="img">
                <img src="/video_learning/noimage.png" alt="text" />
            </div>
            <div class="desc"> 
                <h4>{{ video.title }}</h4>
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
    props: ['videos', 'mode','group_edit', 'g_index', 'c_index'],
    data(){
        return {
          
        }
    },
    methods: {
        saveOrder(event) {

            // axios.post('/kb/page/save-order', {
            //     id: event.item.id,
            //     order: event.newIndex, // oldIndex
            //     parent_id: event.to.id
            // })
            // .then(response => {
            //     // this.$message.success('Очередь сохранена');
            // });
        }
    }
}
</script>