<template>
	<div>
		<!-- Шаги -->
		<div class="steps">
			<p
				class="mr-2"
				:class="{ active: step == 1 }"
				@click="moveToStep(1)"
			>
				<b>1. Загрузить видео ></b>
			</p>
			<p
				:class="{ active: step == 2 }"
				@click="moveToStep(2)"
			>
				<b>2. Редактировать видео</b>
			</p>
		</div>

		<!-- first step -->
		<div v-if="step == 1">
			<UploadFiles
				:id="playlist_id"
				:token="token"
				type="video"
				:file_types="['mp4', 'flv']"
				@onupload="onupload"
			/>

			<div
				v-if="file !== null"
				class="uploaded_files"
			>
				<p>
					<b>Загружено {{ Number(file.size / 1024 / 1024).toFixed(3) }}mb</b>
				</p>
				<p>{{ file.name }}</p>
			</div>
		</div>

		<!-- second step -->
		<div v-if="step == 2">
			<div
				v-if="file !== null"
				class="row mb-2"
			>
				<div class="col-md-4">
					Название
				</div>
				<div class="col-md-8">
					<input
						v-model="file.model.title"
						type="text"
						class="form-control"
					>
				</div>

				<div class="col-md-12">
					<p>
						<b>Загружено {{ Number(file.size / 1024 / 1024).toFixed(3) }}mb</b>
					</p>
					<p>{{ file.name }}</p>
				</div>
			</div>
			<div class="d-flex mt-3">
				<button
					class="btn mr-1"
					@click="saveVideo"
				>
					Сохранить
				</button>
				<button
					class="btn"
					@click="deleteVideo"
				>
					Удалить
				</button>
			</div>
		</div>
	</div>
</template>

<script>
import UploadFiles from '@/components/UploadFiles' // загрузка файлов
export default {
	name: 'VideoUploader',
	components: {
		UploadFiles,
	},
	props: {
		token: String,
		playlist_id: Number,
		group_id: Number
	},
	data(){
		return {
			step: 1,
			file: null,
			video: {
				title: '',
				links: '',
				text: '',
			},
		}
	},

	created() {

	},

	methods: {
		onupload(item) {
			this.file = item;
			this.step = 2;
		},

		moveToStep(i) {
			if (i == 2 && this.file === null) return;
			this.step = i;
		},

		deleteVideo() {
			this.axios
				.post('/playlists/delete-video', {
					id: this.file.model.id,
				})
				.then(() => {
					this.$toast.success('Файл удален');
					this.file = null;
					this.step = 1;
				})
				.catch((error) => {
					alert(error);
				});
		},

		saveVideo() {
			let loader = this.$loading.show();
			this.axios
				.post('/playlists/save-video', {
					id: this.playlist_id,
					video: this.file.model,
					group_id: this.group_id
				})
				.then((response) => {
					loader.hide()
					this.step = 1;
					this.$emit('addVideoToPlaylist', response.data.video)
					this.$toast.success('Добавлен');
					this.file = null;

					this.$emit('close');
				})
				.catch((error) => {
					loader.hide()
					alert(error);
				});
		},

	}
}
</script>
