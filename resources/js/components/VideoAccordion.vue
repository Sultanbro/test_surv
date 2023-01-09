<template>
<div class="video-accordion">
    
    <div v-for="(group, g_index) in groups" class="group" :class="{'opened': group.opened || group.title == 'Без группы' }">

        <div class="g-title"  v-if="group.title != 'Без группы'" @click="toggleGroup(g_index)" >
            <input type="text" class="group-input" v-model="group.title" :disabled="mode == 'read'" @change="saveGroup(g_index)" />
            <div class="btns" > 
                <i class="fa fa-folder-plus" @click.stop="addGroup(g_index)" title="Добавить отдел" v-if="mode == 'edit'"></i>
                <i class="fa fa-upload" @click.stop="uploadVideo(g_index)"  title="Загрузить видео" v-if="mode == 'edit'"></i>
                <i class="fa fa-trash"  @click.stop="deleteGroup(g_index)"  title="Удалить отдел" v-if="mode == 'edit'"></i>
                <i class="fa fa-chevron-down chevron" v-if="group.children.length > 0 || group.videos.length > 0"></i>
            </div>
        </div> 
        
        <video-accordion 
            :token="token"
            :playlist_id="playlist_id"
            :groups="group.children"
            :mode="mode"
            :active="active"
            :is_course="is_course"
            @showVideo="showVideo"
            @deleteVideo="deleteVideo"
            @showTests="showTests"
            @order-changed="$emit('order-changed')"
            @moveTo="moveTo"
        />

        <video-list 
            :videos="group.videos"
            :mode="mode"
            :active="active"
            :g_index="g_index"
            :c_index="-1"
            @showVideo="showVideo"
            @showTests="showTests"
            @moveTo="moveTo"
            @deleteVideo="deleteVideo"
            @order-changed="$emit('order-changed')"
            :is_course="is_course"
        />

    </div>

    <b-modal
      v-model="uploader"
      hide-footer
      title="Загрузить видео"
      size="lg"
    >
        <video-uploader 
            :token="token"
            :playlist_id="playlist_id"
            :group_id="group_id"
            @close="uploader = false"
            @addVideoToPlaylist="addVideoToPlaylist"
        ></video-uploader>
    </b-modal>
</div>
</template>

<script>
export default {
	name: 'VideoAccordion',
	props: ['mode','groups', 'active', 'is_course', 'playlist_id', 'token'],
	data(){
		return {
			uploader: false,
			group_id: 0
		}
	},
	methods: {

		addVideoToPlaylist(video) {
			let i = this.groups.findIndex(el => el.id == this.group_id)
			if(i == -1) return this.$toast.error('Ошибка при добавлении в отдел в браузере');
			this.groups[i].videos.push(video);
		},

		toggleGroup(i, open = false) {
			console.log('togglegroup ' + i)
			let status = this.groups[i].opened;
			this.groups.forEach(el => {
				el.opened = false;
			});
			this.groups[i].opened = open ? true : !status;
		}, 


		showVideo(video, i) {
			this.$emit('showVideo', video, i);
		},

		moveTo(video) {
			this.$emit('moveTo', video);
		},

		showTests(video, input_focus) {
			this.$emit('showTests', video, input_focus);
		},

		deleteVideo(o) {

			if(!confirm('Вы уверены?')) return;
			axios
				.post('/playlists/delete-video', {
					id: o.video.id,
				})
				.then((response) => {
					this.$toast.success('Файл удален');
                
					// remove video from group
					if(o.c_index == -1) {
						this.groups[o.g_index].videos.splice(o.v_index, 1)
					} else {
						this.groups[o.g_index].children[o.c_index].videos.splice(o.v_index, 1)
					} 
                
				})
				.catch(error => alert(error));
           
		},

		addGroup(i) {
			console.log('add group accrodion')
			axios
				.post('/playlists/groups/create', {
					parent_id: i == -1 ? 0 : this.groups[i].id,
					playlist_id: this.playlist_id
				})
				.then((response) => {

					if(i == -1) {// from playlist_edit
						this.groups.push({
							id: response.data.id,
							title: response.data.title,
							opened: true,
							children: [],
							videos:[]
						});
					} else {
						this.groups[i].children.push({
							id: response.data.id,
							title: response.data.title,
							videos:[],
							children: [],
							opened: true,
						});
					}

					this.$toast.success('Сохранено!');
				})
				.catch((error) => {
					alert(error);
				});
            
           

			this.toggleGroup(i, true)
		},

		saveGroup(i) {    
            
			console.log(this.groups[i])
			axios
				.post('/playlists/groups/save', {
					id: this.groups[i].id,
					title: this.groups[i].title,
				})
				.then((response) => {
					this.$toast.success('Сохранено!');
				})
				.catch((error) => {
					alert(error);
				});

			this.toggleGroup(i, true)
		},

        
		uploadVideo(i) {
			console.log('upload video accordion', i)
			console.log('upload video accordion', this.groups[i].id)
			this.group_id = this.groups[i].id
            
			this.uploader = true
		},
        
		deleteGroup(i) {
			var arrStr = [
				'Вы точно хотите удалить отдел?', ' Думаю, вы случайно нажали удалить отдел. Удалить отдел?', 'Удалить отдел не смотря ни на что?'
			]
			var randElement = arrStr[Math.floor(Math.random() * arrStr.length)];
			console.log(randElement);

			if(!confirm(randElement)) {
				return;
			}

			axios
				.post('/playlists/groups/delete', {
					id: this.groups[i].id,
				})
				.then((response) => {
					this.groups.splice(i, 1);
					this.$toast.success('Удалено!');
				})
				.catch((error) => {
					alert(error);
				});
		}
	}
}
</script>