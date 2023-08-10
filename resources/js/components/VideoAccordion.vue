<template>
	<div class="video-accordion">
		<div
			v-for="(group, g_index) in groups"
			:key="g_index"
			class="group"
			:class="{'opened': group.opened || group.title == 'Без группы' }"
		>
			<div
				v-if="group.title != 'Без группы'"
				class="g-title"
				@click="toggleGroup(g_index)"
			>
				<input
					v-if="mode !== 'read'"
					v-model="group.title"
					type="text"
					class="group-input"
					:disabled="mode == 'read'"
					@change="saveGroup(g_index)"
				>
				<span
					v-if="mode == 'read'"
					class="g-text"
				>{{ group.title }}</span>
				<div class="btns">
					<i
						v-if="mode == 'edit'"
						class="fa fa-folder-plus"
						title="Добавить отдел"
						@click.stop="addGroup(g_index)"
					/>
					<i
						v-if="mode == 'edit'"
						class="fa fa-upload"
						title="Загрузить видео"
						@click.stop="uploadVideo(g_index)"
					/>
					<i
						v-if="mode == 'edit'"
						class="fa fa-trash"
						title="Удалить отдел"
						@click.stop="deleteGroup(g_index)"
					/>
					<i
						v-if="group.children.length > 0 || group.videos.length > 0"
						class="fa fa-chevron-down chevron"
					/>
				</div>
			</div>

			<VideoAccordion
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

			<VideoList
				:videos="group.videos"
				:mode="mode"
				:active="active"
				:g_index="g_index"
				:c_index="-1"
				:is_course="is_course"
				@showVideo="showVideo"
				@showTests="showTests"
				@moveTo="moveTo"
				@deleteVideo="deleteVideo"
				@order-changed="$emit('order-changed')"
			/>
		</div>

		<b-modal
			v-model="uploader"
			hide-footer
			title="Загрузить видео"
			size="lg"
		>
			<VideoUploader
				:token="token"
				:playlist_id="playlist_id"
				:group_id="group_id"
				@close="uploader = false"
				@addVideoToPlaylist="addVideoToPlaylist"
			/>
		</b-modal>
	</div>
</template>

<script>
import VideoList from '@/components/VideoList'
import VideoUploader from '@/components/VideoUploader'

const VideoAccordion = {
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
			this.axios
				.post('/playlists/delete-video', {
					id: o.video.id,
				})
				.then(() => {
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
			this.axios
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
			this.axios
				.post('/playlists/groups/save', {
					id: this.groups[i].id,
					title: this.groups[i].title,
				})
				.then(() => {
					this.$toast.success('Сохранено!');
				})
				.catch((error) => {
					alert(error);
				});

			this.toggleGroup(i, true)
		},


		uploadVideo(i) {
			this.group_id = this.groups[i].id

			this.uploader = true
		},

		deleteGroup(i) {
			var arrStr = [
				'Вы точно хотите удалить отдел?', ' Думаю, вы случайно нажали удалить отдел. Удалить отдел?', 'Удалить отдел не смотря ни на что?'
			]
			var randElement = arrStr[Math.floor(Math.random() * arrStr.length)];

			if(!confirm(randElement)) {
				return;
			}

			this.axios
				.post('/playlists/groups/delete', {
					id: this.groups[i].id,
				})
				.then(() => {
					this.groups.splice(i, 1);
					this.$toast.success('Удалено!');
				})
				.catch((error) => {
					alert(error);
				});
		}
	}
}
VideoAccordion.components = {
	VideoAccordion,
	VideoList,
	VideoUploader,
}
export default VideoAccordion
</script>
