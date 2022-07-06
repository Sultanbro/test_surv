<template>
<div class="video-accordion">
    
    <div v-for="(group, g_index) in groups" class="group" :class="{'opened': group.opened}">

            <div class="g-title" @click="toggleGroup(g_index)">
                <input type="text" class="group-input" v-model="group.title" :disabled="group.title == 'Без группы' || mode == 'read'" />
                <div class="btns">
                    <i class="fa fa-chevron-down chevron"></i>
                    <i class="fa fa-plus" @click.stop="addGroup(g_index)" title="Добавить группу" v-if="group.title != 'Без группы' && group_edit && mode == 'edit'"></i>
                    <i class="fa fa-upload" @click.stop="$emit('uploadVideo', g_index)" title="Загрузить видео"  v-if="!group_edit && mode == 'edit'"></i>
                    <i class="fa fa-trash"  @click.stop="deleteGroup(g_index)"  title="Удалить группу" v-if="group.title != 'Без группы' && group_edit && mode == 'edit'"></i>
                </div>
            </div> 

            <template v-for="(child, c_index) in group.children">
                <div class="child-group group" :class="{'opened': child.opened}">
                    <div class="g-title" @click="toggleChild(c_index, g_index)">
                    <input type="text" class="group-input" v-model="child.title" :disabled="mode == 'read'"  />
                        <div class="btns">
                            <i class="fa fa-chevron-down chevron2"></i>
                            <i class="fa fa-upload" @click.stop="$emit('uploadVideo', g_index, c_index)" title="Загрузить видео" v-if="!group_edit && mode == 'edit'"></i>
                            <i class="fa fa-trash"  @click.stop="deleteGroup(g_index, c_index)"  title="Удалить группу" v-if="group_edit && mode == 'edit'"></i>
                        </div>
                    </div>
   
                    <video-list 
                        :videos="child.videos"
                        :mode="mode"
                        :active="active"
                        :g_index="g_index"
                        :c_index="c_index"
                        :group_edit="group_edit"
                        @showVideo="showVideo"
                        @deleteVideo="deleteVideo"
                        :is_course="is_course"
                        
                    />
                </div>
            </template>

            <video-list 
                :videos="group.videos"
                :mode="mode"
                :active="active"
                :g_index="g_index"
                :c_index="-1"
                :group_edit="group_edit"
                @showVideo="showVideo"
                @deleteVideo="deleteVideo"
                :is_course="is_course"
            />
             
    </div>
        

      
</div>
</template>

<script>
export default {
    name: 'VideoAccordion',
    props: ['mode','groups','group_edit', 'active', 'is_course'],
    data(){
        return {
            edit: false,
        }
    },
    methods: {
        toggleGroup(i, open = false) {
            console.log('togglegroup ' + i)
            let status = this.groups[i].opened;
            this.groups.forEach(el => {
                el.opened = false;
            });
            this.groups[i].opened = open ? true : !status;
        }, 

        toggleChild(i, g) {

            console.log('togglegroup ' + i + ' ' + g)


            let status = this.groups[g].children[i].opened;
            this.groups[g].children.forEach(el => {
                el.opened = false;
            });
            this.groups[g].children[i].opened = !status;
        },

        showVideo(video, v_index) {
            this.$emit('showVideo', video, v_index);
        },

        deleteVideo(o) {

            axios
            .post("/playlists/delete-video", {
                id: o.video.id,
            })
            .then((response) => {
                this.$message.success("Файл удален");
                
                // remove video from group
                if(o.c_index == -1) {
                    this.groups[o.g_index].videos.splice(o.v_index, 1)
                } else {
                    this.groups[o.g_index].children[o.c_index].videos.splice(o.v_index, 1)
                } 
                
            })
            .catch(error => alert(error));
           
        },

        addGroup(g) {
            this.groups[g].children.push({
                id: 0,
                title: 'Тестовая группа',
                videos:[]
            });

            this.toggleGroup(g, true)
        },
        
        deleteGroup(g, c = -1) {
            if(c == -1) {
                this.groups.splice(g, 1);
            } else {
                this.groups[g].children.splice(c, 1);
            }
        }
    }
}
</script>