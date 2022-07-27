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
                :id="video.id"
                :class="{
                    'active': (active == video.id),
                    'disabled': active != video.id && mode == 'read' && video.item_model == null
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
                <div class="more">
                    <i class="fas fa-ellipsis-h mr-2"></i>
                    <div class="show" @click.stop="$emit('showTests', video, true)">
                         <div class="el" >
                            <i class="fa fa-edit  mr-2" title="Текст" ></i>
                            <span>Переименовать</span>
                        </div>
                        <div class="el"  @click.stop="moveTo(video, v_index)">
                            <i class="fas fa-angle-double-right  mr-2" title="Текст"></i>
                            <span>Переместить</span>
                        </div>
                        <div class="el"  @click.stop="$emit('showTests', video, false)">
                            <i class="far fa-question-circle mr-2" title="Вопросы к видео"></i>
                            <span>Вопросы к видео</span>
                        </div>
                    </div>
                </div>
               
                <i  class="far fa-trash-alt" 
                    title="Убрать из плейлиста" 
                    @click.stop="$emit('deleteVideo', {
                        video: video, 
                        v_index: v_index,
                        g_index: g_index,
                        c_index: c_index
                    })"
                ></i>
            </div>
        </div>
    </draggable>


    <!-- Переместить -->
    <sidebar
      v-model="modal"
      title="Переместить видео"
        :open="modal"
        @close="modal = false"
        width="50%"
    >   
    
        <div class="d-flex mb-2 p-3 aic">
            <p class="mb-0 mr-2">Плейлист</p>
            <v-select :options="playlists" label="title" v-model="playlist" class="group-select w-full"></v-select>
        </div>

        <div class="d-flex mb-2 p-3 aic">
            <p class="mb-0 mr-2">Группа</p>
            <v-select :options="groups" label="title" v-model="group" class="group-select w-full"></v-select>
        </div>


        <div class="mb-3">
            <button class="btn btn-primary rounded m-auto " @click="move">
                <span>Сохранить</span>
            </button>
        </div>
      
    </sidebar>

</div>
</template>

<script>
export default {
    name: 'VideoList',
    props: ['videos', 'mode','group_edit', 'g_index', 'c_index', 'active' , 'is_course'],
    data(){
        return {
          modal: false,
          index: -1,
          playlist: null,
          group: {
                id: 0,
                title: 'Без группы'
            },
          playlists: [],
          groups: []
        }
    },

    watch: {
        playlist() {
            if(this.playlist != null) {
                let i = this.playlists.findIndex(el => el.id == this.playlist.id)
                if(i != -1) {
                   
                    this.groups = this.playlists[i].groupses;
                    this.groups.unshift({
                        id: 0,
                        title: 'Без группы'
                    });
                     this.group = {
                        id: 0,
                        title: 'Без группы'
                    }
                }
            }  
        }
    },

    created() {
       
    },

    methods: {

        moveTo(video, i) {
            this.modal = true;
            this.fetch();
            this.index = i
        },

        fetch() {
            axios.post('/videos/get-playlists-to-move')
            .then(response => {
                this.playlists = response.data

                 if(this.videos.length > 0) {
                    let i = this.playlists.findIndex(el => el.id == this.videos[0].playlist_id)
                    if(i != -1) {
                        this.playlist = this.playlists[i]
                        this.group = {
                            id: 0,
                            title: 'Без группы'
                        };
                    }
                }
            })
        },  

        move() {
            axios.post('/videos/move-to-playlist', {
                video_id: this.videos[this.index].id,
                playlist_id: this.playlist.id, 
                group_id: this.group.id
            })
            .then(response => {
                this.$toast.success('Видео перемещено');
                this.videos.splice(this.index,1);
            })
        },   

        saveOrder(e) {
            let loader = this.$loading.show(); 
            axios.post('/videos/save-order', {
                id: e.item.id,
                order: e.newIndex, // oldIndex
            })
            .then(response => {
                loader.hide()
                this.$emit('order-changed')
                this.$toast.success('Очередь сохранена');
            })
            .catch(e => {
                loader.hide()
                console.log(e)
            })
        },

        showVideo(video, i) {
            if(video.item_model == null && this.mode == 'read') return;
            this.$emit('showVideo', video, i);
        },
    }
}
</script>

