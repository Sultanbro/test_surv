<template>
<div class="video-accordion">
    <div v-for="(group, g_index) in groups" class="group" :class="{'opened': group.opened}">

            <div class="g-title" @click="toggleGroup(g_index)">
                <span>{{ group.title }}</span>
                <div class="btns">
                    <i class="fa fa-plus"></i>
                </div>
            </div>

            <template v-for="(child, c_index) in group.children">
                <div class="child-group group" :class="{'opened': child.opened}">
                    <div class="g-title" @click="toggleChild(c_index, g_index)">{{ child.title }}</div>
                    <template v-for="(video, v_index) in child.videos">
                        <div
                            class="video-block"
                            :key="video.id"
                            @click.stop="$emit('showVideoSettings',video, v_index)"
                            >
                            <div class="mover">
                                <i class="fa fa-bars"></i> 
                            </div>
                            <div class="img">
                                <img src="/video_learning/noimage.png" alt="text" />
                            </div>
                            <div class="desc"> 
                                <h4>{{ video.title }}</h4>
                                <div class="text" v-html="video.desc"></div>
                            </div>
                            <div class="controls">
                                <i class="fas fa-ellipsis-h"></i>
                                <div class="controls-menu">
                                <div class="item" >
                                    <i class="fa far fa-trash"></i>
                                    <div class="text">Убрать из плейлиста</div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <template v-for="(video, v_index) in group.videos">
              <div
                  class="video-block"
                  :key="video.id"
                  @click.stop="$emit('showVideoSettings',video, v_index)"
                >
                  <div class="mover">
                    <i class="fa fa-bars"></i> 
                  </div>
                  <div class="img">
                    <img src="/video_learning/noimage.png" alt="text" />
                  </div>
                  <div class="desc"> 
                    <h4>{{ video.title }}</h4>
                    <div class="text" v-html="video.desc"></div>
                  </div>
                  <div class="controls">
                    <i class="fas fa-ellipsis-h"></i>
                    <div class="controls-menu">
                      <div class="item" @click.stop="$emit('removeVideo', v_index)">
                        <i class="fa far fa-trash"></i>
                        <div class="text">Убрать из плейлиста</div>
                      </div>
                    </div>
                  </div>
              </div>
            </template>

    
    </div>
        

        <!-- <draggable
          class="videos"
          tag="div"
          handle=".fa-bars"
          :list="playlist.videos"
          :group="{ name: 'g1' }"
          @end="saveOrder"
        >
         
        </draggable> -->
</div>
</template>

<script>
export default {
    name: 'VideoAccordion',
    props: ['mode','groups'],
    data(){
        return {
            playerOptions: {
            
            },
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
        }
    }
}
</script>