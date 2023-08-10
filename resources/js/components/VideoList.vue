<template>
	<div class="v-list">
		<Draggable
			class="dfsdf"
			tag="div"
			handle=".fa-bars"
			:list="videos"
			:group="{ name: 'g1' }"
			@end="saveOrder"
		>
			<div
				v-for="(video, v_index) in videos"
				:id="video.id"
				:key="video.id"
				class="video-block"
				:class="{
					'active': (active == video.id),
					'disabled': active != video.id && mode == 'read' && video.item_model == null
				}"
				@click="showVideo(video, v_index)"
			>
				<div
					v-if="mode == 'edit' && !group_edit"
					class="mover"
				>
					<i class="fa fa-bars" />
				</div>
				<!--				<div class="img">-->
				<!--					<img-->
				<!--						src="/images/author.jpg"-->
				<!--						alt="text"-->
				<!--					>-->
				<!--				</div>-->
				<div class="desc">
					<div class="d-flex align-items-start">
						<i
							v-if="active != video.id && mode == 'read' && video.item_model == null"
							class="fa fa-lock mr-3"
						/>
						<i
							v-if="active == video.id"
							class="fa fa-play mr-3"
						/>
						<h4>{{ video.title }}</h4>
					</div>
					<div
						class="text"
						v-html="video.desc"
					/>
				</div>
				<div
					v-if="mode == 'edit' && !group_edit"
					class="controls d-flex"
				>
					<div class="more">
						<i class="fas fa-ellipsis-h mr-2" />
						<div
							class="show"
							@click.stop="$emit('showTests', video, true)"
						>
							<div class="el">
								<i
									class="fa fa-edit  mr-2"
									title="Текст"
								/>
								<span>Переименовать</span>
							</div>
							<div
								class="el"
								@click.stop="moveTo(video, v_index)"
							>
								<i
									class="fas fa-angle-double-right  mr-2"
									title="Текст"
								/>
								<span>Переместить</span>
							</div>
							<div
								class="el"
								@click.stop="$emit('showTests', video, false)"
							>
								<i
									class="far fa-question-circle mr-2"
									title="Вопросы к видео"
								/>
								<span>Вопросы к видео</span>
							</div>
						</div>
					</div>

					<i
						class="far fa-trash-alt"
						title="Убрать из плейлиста"
						@click.stop="$emit('deleteVideo', {
							video: video,
							v_index: v_index,
							g_index: g_index,
							c_index: c_index
						})"
					/>
				</div>
			</div>
		</Draggable>


		<!-- Переместить -->
		<Sidebar
			v-model="modal"
			title="Переместить видео"
			:open="modal"
			width="50%"
			@close="modal = false"
		>
			<div class="d-flex mb-2 p-3 aic">
				<p class="mb-0 mr-2">
					Плейлист
				</p>
				<v-select
					v-model="playlist"
					:options="playlists"
					label="title"
					class="group-select w-full"
				/>
			</div>

			<div class="d-flex mb-2 p-3 aic">
				<p class="mb-0 mr-2">
					Отдел
				</p>
				<v-select
					v-model="group"
					:options="groups"
					label="title"
					class="group-select w-full"
				/>
			</div>


			<div class="mb-3">
				<button
					class="btn btn-primary rounded m-auto "
					@click="move"
				>
					<span>Сохранить</span>
				</button>
			</div>
		</Sidebar>
	</div>
</template>

<script>
/* eslint-disable vue/no-mutating-props */
import Draggable from 'vuedraggable'
import Sidebar from '@/components/ui/Sidebar' // сайдбар table

export default {
	name: 'VideoList',
	components: {
		Sidebar,
		Draggable,
	},
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
			this.axios.post('/videos/get-playlists-to-move')
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
			this.axios.post('/videos/move-to-playlist', {
				video_id: this.videos[this.index].id,
				playlist_id: this.playlist.id,
				group_id: this.group.id
			})
				.then(() => {
					this.$toast.success('Видео перемещено');
					this.videos.splice(this.index,1);
				})
		},

		saveOrder(e) {
			let loader = this.$loading.show();
			this.axios.post('/videos/save-order', {
				id: e.item.id,
				order: e.newIndex, // oldIndex
			})
				.then(() => {
					loader.hide()
					this.$emit('order-changed')
					this.$toast.success('Очередь сохранена');
				})
				.catch(e => {
					loader.hide()
					console.error(e)
				})
		},

		showVideo(video, i) {
			if(video.item_model == null && this.mode == 'read') return;
			this.$emit('showVideo', video, i);
		},
	}
}
</script>

