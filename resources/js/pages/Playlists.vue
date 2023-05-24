<template>
	<div
		v-if="token"
		class="video-playlists"
	>
		<div class="d-flex">
			<div class="lp">
				<h1 class="page-title">
					Темы видео
				</h1>
				<div
					class="section d-flex aic jcsb my-2"
					v-for="(cat, index) in categories"
					:key="cat.id"
					:class="{'active': activeCat != null && activeCat.id == cat.id}"
					@click="selectCat(index)"
				>
					<p class="mb-0">
						{{ cat.title }}
					</p>

					<div class="d-flex">
						<i
							class="fa fa-pen ml-2"
							v-if="cat.id != 0 && mode == 'edit'"
							@click.stop="editCat(index)"
						/>
						<i
							class="fa fa-trash text-danger ml-2"
							v-if="cat.id != 0 && mode == 'edit'"
							@click.stop="deleteCat(index)"
						/>
					</div>
				</div>


				<button
					class="btn-add"
					@click="showAddCategory = true"
					v-if="mode == 'edit'"
				>
					Добавить категорию
				</button>
			</div>


			<div class="rp">
				<div class="hat">
					<div class="d-flex jsutify-content-between hat-top">
						<div class="bc">
							<a
								href="#"
								@click="back"
							>Темы</a>
							<template v-if="activeCat">
								<i class="fa fa-chevron-right" />
								<a
									href="#"
									@click="back"
								>{{ activeCat.title + ' (' + activeCat.playlists.length + ')' }}</a>
							</template>
							<template v-if="activePlaylist">
								<i class="fa fa-chevron-right" />
								<a href="#">{{ activePlaylist.title }}</a>
							</template>
							<!---->
						</div>
						<div class="control-btns d-flex">
							<div
								class="mode_changer"
								v-if="can_edit"
							>
								<i
									class="fa fa-pen"
									@click="toggleMode"
									:class="{'active': mode == 'edit'}"
								/>
							</div>

							<div
								class="mode_changer ml-2"
								v-if="activePlaylist == null && mode == 'edit'"
							>
								<i
									class="icon-nd-settings"
									@click="get_settings()"
								/>
							</div>

							<i
								class="btn btn-success fa fa-plus ml-2 d-flex px-2 aic"
								@click="showAddPlaylist = true"
								v-if="mode == 'edit' && activePlaylist == null"
							/>




							<!-- buttons for playlist like Save Group -->
							<template v-if="activePlaylist && mode == 'edit'">
								<i
									class="btn btn-info fa-upload fa ml-2 d-flex px-2 aic"
									@click="uploadVideo"
									title="Добавить видео"
								/>
								<i
									class="btn btn-info fa fa-folder-plus ml-2 d-flex px-2 aic"
									@click="addGroup"
									title="Добавить отдел"
								/>
								<i
									class="btn btn-success fa fa-save ml-2 d-flex px-2 aic"
									@click="savePlaylistEdit"
									title="Сохранить плейлист"
								/>
							</template>
						</div>
					</div>
					<div><!----></div>
				</div>


				<div class="rp-content">
					<div v-if="activeCat != null">
						<PlaylistEdit
							ref="playlist"
							@back="back"
							:token="token"
							:id="activePlaylist.id"
							:is_course="false"
							:auth_user_id="user_id"
							:mode="mode"
							:myvideo="myvideo"
							v-if="activePlaylist != null"
						/>
						<div v-else>
							<!-- playlists -->
							<div class="video-container">
								<div
									class="playlist mb-4"
									v-for="(playlist, p_index) in activeCat.playlists"
									:key="playlist.id"
									@click="selectPl(p_index)"
								>
									<div class="left">
										<img
											:src="playlist.img == '' || playlist.img == null ? '/images/author.jpg' : playlist.img"
											alt="image"
										>
										<div
											class="d-flex btns"
											v-if="mode == 'edit'"
										>
											<i
												class="fa fa-pen"
												v-if="playlist.id != 0"
												title="Переместить"
												@click.stop="movePl(p_index)"
											/>
											<i
												class="fa fa-trash text-danger ml-2"
												v-if="playlist.id != 0"
												@click.stop="deletePl(p_index)"
											/>
										</div>
									</div>

									<div class="right">
										<div class="title">
											{{ playlist.title }}
										</div>
										<div class="text">
											{{ playlist.text }}
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>


		<!-- Новый плейлист -->
		<b-modal
			v-model="showAddPlaylist"
			title="Новый плейлист"
			size="md"
			class="modalle"
			hide-footer
		>
			<input
				type="text"
				v-model="newPlaylist"
				placeholder="Название..."
				class="form-control mb-2"
			>
			<button
				class="btn btn-primary rounded m-auto"
				@click="addPlaylist"
			>
				<span>Сохранить</span>
			</button>
		</b-modal>

		<!-- Новый категория -->
		<b-modal
			v-model="showAddCategory"
			title="Новая категория"
			size="md"
			class="modalle"
			hide-footer
		>
			<input
				type="text"
				v-model="newcat"
				placeholder="Название категории..."
				class="form-control mb-2"
			>
			<button
				class="btn btn-primary rounded m-auto"
				@click="addCat"
			>
				<span>Сохранить</span>
			</button>
		</b-modal>

		<!-- Переименовать категорию -->
		<b-modal
			v-model="showEditCat"
			title="Переименовать категорию"
			size="md"
			class="modalle"
			hide-footer
		>
			<input
				type="text"
				v-model="newcat"
				placeholder="Название категории..."
				class="form-control mb-2"
			>
			<button
				class="btn btn-primary rounded m-auto"
				@click="saveCat"
			>
				<span>Сохранить</span>
			</button>
		</b-modal>

		<!-- Редактировать плейлист -->
		<SimpleSidebar
			title="Редактировать плейлист"
			:open="showEditPlaylist"
			@close="showEditPlaylist = false"
			width="50%"
		>
			<template #body>
				<div
					v-if="editingPlaylist != null"
					class="p-3"
				>
					<div class="d-flex">
						<div class="left f-70">
							<div class="d-flex mb-2">
								<p class="mb-2 font-bold">
									Название
								</p>
								<input
									type="text"
									v-model="editingPlaylist.title"
									placeholder="Название плейлиста..."
									class="form-control mb-2"
								>
							</div>

							<div class="d-flex mb-2">
								<p class="mb-0 mr-2">
									Описание
								</p>
								<textarea
									v-model="editingPlaylist.text"
									placeholder="Описание плейлиста..."
									class="form-control"
								/>
							</div>

							<div class="d-flex mb-2">
								<p class="mb-0 mr-2">
									Категория
								</p>
								<select
									class="form-control"
									v-model="editingPlaylist.category_id"
								>
									<option
										v-for="cat in categories"
										:value="cat.id"
										:key="cat.id"
									>
										{{ cat.title }}
									</option>
								</select>
							</div>
						</div>

						<div class="right f-30 pl-4">
							<img
								class="book-img mb-5"
								v-if="editingPlaylist.img != ''"
								:src="editingPlaylist.img"
							>
							<b-form-file
								ref="edit_img"
								v-model="file_img"
								:state="Boolean(file_img)"
								placeholder="Выберите или перетащите файл сюда..."
								drop-placeholder="Перетащите файл сюда..."
								class="mt-3"
							/>
						</div>
					</div>
				</div>
			</template>
			<template #footer>
				<button
					v-if="editingPlaylist != null"
					class="btn btn-success mr-2 rounded"
					@click="savePlaylist"
				>
					<span>Сохранить</span>
				</button>
			</template>
		</SimpleSidebar>


		<!-- Настройки раздела -->
		<SimpleSidebar
			title="Настройки видеокурсов"
			:open="showSettings"
			@close="showSettings = false"
			width="400px"
		>
			<template #body>
				<label class="d-flex">
					<input
						type="checkbox"
						v-model="allow_save_video_without_test"
						class="form- mb-2 mr-2"
					>
					<p>Разрешить сохранять видео без тестовых вопросов</p>
				</label>
			</template>

			<template #footer>
				<button
					class="btn btn-primary rounded m-auto"
					@click="save_settings()"
				>
					<span>Сохранить</span>
				</button>
			</template>
		</SimpleSidebar>
	</div>
</template>

<script>
const PlaylistEdit = () => import(/* webpackChunkName: "PlaylistEdit" */ '@/pages/PlaylistEdit') // редактирование плейлиста
import SimpleSidebar from '@/components/ui/SimpleSidebar'
export default {
	name: 'PlayLists',
	components: {
		SimpleSidebar,
		PlaylistEdit
	},
	props: {
		token: String,
		can_edit: {
			type: Boolean,
			default: false
		},
		category: Number,
		playlist: Number,
		video: Number
	},
	data: function() {
		return {
			categories: [],
			showEditCat: false,
			file_img: null,
			editingPlaylist: {
				title: '',
				text: '',
				category_id: ''
			},
			showEditPlaylist: false,
			user_id: 0,
			mode: 'read',
			activeCat: null,
			newcat: '',
			newPlaylist: '',
			activePlaylist: null,
			showAddPlaylist: false,
			showAddCategory: false,
			showSettings: false,
			allow_save_video_without_test: false,
			mylink: window.location.protocol + '//' + window.location.host + window.location.pathname.substring(0,16),
			data_category: this.category,
			data_playlist: this.playlist,
			myvideo: this.video,
		};
	},
	watch:{
		token(){
			this.init()
		}
	},
	created() {
		if(this.token){
			this.init()
		}
	},

	methods: {
		init(){
			this.fetchData();
		},
		addGroup() {
			this.$refs.playlist.addGroup()
		},

		uploadVideo() {
			this.$refs.playlist.uploadVideo()
		},

		savePlaylistEdit() {
			this.$refs.playlist.savePlaylist()
		},

		clearUrl(){
			var newUrl = window.location.protocol + '//' + window.location.host + window.location.pathname.substring(0,16);
			history.pushState(null, null, newUrl);
		},

		fetchData() {
			this.axios
				.get('/playlists/get')
				.then((response) => {
					this.categories = response.data.categories;
					this.user_id = response.data.user_id;
					if(this.categories.length > 0) {
						this.activeCat = this.categories[this.category-1];
						if(this.playlist > 0){
							this.activePlaylist = this.activeCat.playlists[this.playlist-1];
						}
					}
				})
				.catch((error) => {
					alert(error);
				});
		},

		selectPl(i) {
			this.activePlaylist = this.activeCat.playlists[i];
			this.data_playlist = i+1;


			if (history.pushState) {
				var newUrl = this.mylink.concat('/'+this.data_category, '/'+this.data_playlist);
				history.pushState(null, null, newUrl);
			}
			else {
				console.warn('History API не поддерживает ваш браузер');
			}

		},

		editCat(i) {
			this.showEditCat = true;
			this.newcat = this.categories[i].title;
			this.activeCat = this.categories[i];
		},

		movePl(i) {
			this.editingPlaylist = this.activeCat.playlists[i];
			this.showEditPlaylist = true;
		},

		deletePl(i) {
			if (confirm('Вы уверены что хотите удалить плейлист?')) {
				this.axios
					.post('/playlists/delete', {
						id: this.activeCat.playlists[i].id
					})
					.then(() => {
						this.activeCat.playlists.splice(i, 1);
						this.$toast.success('Удалено');
					});
			}
		},
		selectCat(i) {
			this.activeCat = this.categories[i];
			this.activePlaylist = null;
			this.data_category = i+1;
			this.data_playlist = 0;


			if (history.pushState) {
				var newUrl = this.mylink.concat('/'+this.data_category, '/'+this.data_playlist);
				history.pushState(null, null, newUrl);
			}
			else {
				console.warn('History API не поддерживает ваш браузер');
			}
			this.myvideo = 0;

		},

		deleteCat(i) {
			if (confirm('Вы уверены что хотите удалить категорию?')) {
				this.axios
					.post('/playlists/delete-cat', {
						id: this.categories[i].id
					})
					.then(() => {
						this.categories.splice(i, 1);
						this.activeCat = null;
						this.$toast.success('Удалено');
					});
			}
		},

		savePlaylist() {
			let loader = this.$loading.show();

			let formData = new FormData();
			formData.append('file', this.file_img);
			formData.append('playlist', JSON.stringify(this.editingPlaylist));

			this.axios.post( '/playlists/save-fast', formData)
				.then((response) => {
					if(response.data !== '') this.editingPlaylist.img = response.data;

					if(this.editingPlaylist.category_id != this.activeCat.id) {
						this.deleteItemFrom(this.editingPlaylist.id, this.activeCat.playlists);
						let i = this.categories.findIndex(el => el.id == this.editingPlaylist.category_id);

						if(i != -1) {
							if(response.data !== '') this.editingPlaylist.img = response.data;
							this.categories[i].playlists.push(this.editingPlaylist);
						}

						this.showEditPlaylist = false;
						this.editingPlaylist = {};
					}

					loader.hide();
					this.$toast.success('Сохранено');
				})
				.catch((error) => {
					console.log(error);
					loader.hide();
				})
		},

		deleteItemFrom(id, from) {
			let i = from.findIndex(el => el.id == id);
			if(i != -1) from.splice(i, 1);
		},

		back() {
			this.activePlaylist = null;
			window.history.replaceState({ id: '100' }, 'Плейлисты', '/video_playlists');
		},

		addCat() {
			if (this.newcat.length <= 2) {
				alert('Слишком короткое название!');
				return '';
			}

			let loader = this.$loading.show();

			this.axios
				.post('/playlists/add-cat', {
					title: this.newcat,
				})
				.then((response) => {
					this.showAddCategory = false;
					this.newcat = '';

					this.categories.push(response.data);

					this.$toast.success('Успешно создана!');
					loader.hide();
				})
				.catch((error) => {
					loader.hide();
					alert(error);
				});
		},

		saveCat() {
			if (this.activeCat.title.length <= 2) {
				alert('Слишком короткое название!');
				return '';
			}

			let loader = this.$loading.show();

			this.axios
				.post('/playlists/save-cat', {
					title: this.newcat,
					id: this.activeCat.id,
				})
				.then(() => {
					this.showEditCat = false;
					this.activeCat.title = this.newcat;
					this.newcat = '';
					this.$toast.success('Сохранено!');
					loader.hide();
				})
				.catch((error) => {
					loader.hide();
					alert(error);
				});
		},

		addPlaylist() {
			if (this.newPlaylist.length <= 2) {
				alert('Слишком короткое название!');
				return '';
			}

			let loader = this.$loading.show();

			this.axios
				.post('/playlists/add', {
					title: this.newPlaylist,
					cat_id: this.activeCat.id,
				})
				.then((response) => {
					this.showAddPlaylist = false;
					this.newPlaylist = '';

					this.activeCat.playlists.push(response.data);

					this.$toast.success('Успешно создан!');
					loader.hide();
				})
				.catch((error) => {
					loader.hide();
					alert(error);
				});
		},

		toggleMode() {
			this.mode = (this.mode == 'read') ? 'edit' : 'read';
		},

		get_settings() {
			this.axios
				.post('/settings/get', {
					type: 'video'
				})
				.then((response) => {
					this.allow_save_video_without_test = response.data.settings.allow_save_video_without_test;
					this.showSettings = true;
				})
				.catch((error) => {
					alert(error);
				});
		},

		save_settings() {
			this.axios
				.post('/settings/save', {
					type: 'video',
					allow_save_video_without_test: this.allow_save_video_without_test,
				})
				.then(() => {
					this.showSettings = false;
				})
				.catch((error) => {
					alert(error);
				});
		},

	},
};
</script>
