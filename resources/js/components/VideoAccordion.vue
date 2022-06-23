<template>
<div class="video-accordion">
    
    <div v-for="(group, g_index) in groups" class="group" :class="{'opened': group.opened}">

            <div class="g-title" @click="toggleGroup(g_index)">
                <input type="text" class="group-input" v-model="group.title" :disabled="group.title == 'Без группы'" />
                <div class="btns" v-if="mode == 'edit'">
                    <i class="fa fa-plus" @click.stop="addGroup(g_index)" title="Добавить группу" v-if="group.title != 'Без группы' && group_edit"></i>
                    <i class="fa fa-upload" @click.stop="$emit('uploadVideo', g_index)" title="Загрузить видео"  v-if="!group_edit"></i>
                    <i class="fa fa-trash"  @click.stop="deleteGroup(g_index)"  title="Удалить группу" v-if="group.title != 'Без группы' && group_edit"></i>
                </div>
            </div>

            <template v-for="(child, c_index) in group.children">
                <div class="child-group group" :class="{'opened': child.opened}">
                    <div class="g-title" @click="toggleChild(c_index, g_index)">
                    <input type="text" class="group-input" v-model="child.title" />
                        <div class="btns" v-if="mode == 'edit'">
                            <i class="fa fa-upload" @click.stop="$emit('uploadVideo', g_index, c_index)" title="Загрузить видео" v-if="!group_edit"></i>
                            <i class="fa fa-trash"  @click.stop="deleteGroup(g_index, c_index)"  title="Удалить группу" v-if="group_edit"></i>
                        </div>
                    </div>
   
                    <video-list 
                        :videos="child.videos"
                        :mode="mode"
                        :group_edit="group_edit"
                        @showVideoSettings="showVideoSettings"
                    />
                </div>
            </template>

            <video-list 
                :videos="group.videos"
                :mode="mode"
                :group_edit="group_edit"
                @showVideoSettings="showVideoSettings"
            />
             
    </div>
        

      
</div>
</template>

<script>
export default {
    name: 'VideoAccordion',
    props: ['mode','groups','group_edit'],
    data(){
        return {
            edit: false,
        }
    },
    methods: {
        toggleGroup(i) {
            let status = this.groups[i].opened;
            this.groups.forEach(el => {
                el.opened = false;
            });
            this.groups[i].opened = !status;
        }, 

        toggleChild(i, g) {
            let status = this.groups[g].children[i].opened;
            this.groups[g].children.forEach(el => {
                el.opened = false;
            });
            this.groups[g].children[i].opened = !status;
        },

        showVideoSettings(video, v_index) {
            this.$emit('showVideoSettings', video, v_index);
        },

        addGroup(g) {
            this.groups[g].children.push([{
                id: 0,
                title: 'Тестовая группа',
                videos:[]
            }]);
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