<template>
	<Sidebar
		id="award-user-sidebar"
		v-scroll-lock="open"
		title="Наградить пользователя"
		:open="open"
		:class="isShow ? 'show' : ''"
		width="70%"
		@close="open = false"
	>
		<b-tabs
			v-if="awards.length > 0"
			ref="tabAwardUser"
			v-model="tabIndex"
			class="overflow-hidden"
		>
			<div class="prev-next">
				<span
					class="prev"
					@click="tabIndex--"
				><i class="fa fa-chevron-left" /></span>
				<span
					class="next"
					@click="tabIndex++"
				><i class="fa fa-chevron-right" /></span>
			</div>
			<b-tab
				v-for="award in awards"
				:key="award.id"
			>
				<template #title>
					<span @click="changeTab(award)">{{ award.name }}</span>
				</template>
				<div class="d-flex justify-content-between align-items-center mt-4">
					<div>
						<button
							class="award-user-btn-tab"
							:class="{'active': switchTab === 1}"
							@click="switchTab = 1"
						>
							Доступные награды ({{ availableLength(award.available) }})
						</button>
						<button
							class="award-user-btn-tab"
							:class="{'active': switchTab === 2}"
							:disabled="!award.hasOwnProperty('my')"
							@click="switchTab = 2"
						>
							Выданные награды ({{ award.my ? award.my.length : 0 }})
						</button>
					</div>
					<button
						class="award-user-btn-tab upload"
						@click="modalAdd = !modalAdd"
					>
						<i class="fa fa-upload" />
						Загрузить файл награды
					</button>
				</div>

				<div v-show="switchTab === 1">
					<b-row class="avail mt-3">
						<template v-for="item in award.available">
							<b-col
								v-if="item.type === 'public'"
								:key="item.id"
								cols="12"
								md="2"
								class="mt-4 remove-award-modal"
							>
								<div class="award-image">
									<div @click="previewImage(item)">
										<img
											v-if="item.format !== 'pdf'"
											:src="item.tempPath"
											alt=""
										>
										<vue-pdf-embed
											v-else
											ref="vuePdfEmbeds"
											:source="item.tempPath"
										/>
									</div>
									<i
										class="fa fa-download button download"
										@click="downloadImage(item, award.name)"
									/>
									<i
										class="fa fa-award button award"
										@click="openModalSelect(item, award.name)"
									/>
								</div>
							</b-col>
						</template>
					</b-row>
				</div>
				<div v-show="switchTab === 2 && award.hasOwnProperty('my')">
					<b-row class="mt-3">
						<b-col
							v-for="item in award.my"
							:key="item.id"
							cols="12"
							md="2"
							class="mt-4"
						>
							<div
								class="award-image my-award"
								@click="removeReward(item)"
							>
								<img
									v-if="item.format !== 'pdf'"
									:src="item.tempPath"
									alt=""
								>
								<vue-pdf-embed
									v-else
									ref="vuePdfEmbeds"
									:source="item.tempPath"
								/>
								<i class="fa fa-trash" />
							</div>
						</b-col>
					</b-row>
				</div>
			</b-tab>
		</b-tabs>

		<a
			v-else
			href="/timetracking/settings?tab=9#nav-awards"
			target="_blank"
			class="courses-add"
		>
			Добавить награды
		</a>

		<BModal
			v-model="modalPreview"
			modal-class="preview-modal"
			title="Предосмотр награды"
			size="lg"
			centered
		>
			<img
				v-if="modalPreviewData.format !== 'pdf'"
				:src="modalPreviewData.tempPath"
				alt=""
			>
			<vue-pdf-embed
				v-else
				:source="modalPreviewData.tempPath"
			/>
			<template #modal-footer>
				<b-button
					variant="secondary"
					@click="modalPreview = !modalPreview"
				>
					Закрыть
				</b-button>
			</template>
		</BModal>

		<BModal
			v-model="modalRemoveReward"
			modal-class="remove-award-modal"
			title="Отмена награды"
			size="lg"
			centered
		>
			<h4 class="title-remove">
				Вы действительно хотите отозвать награду?
			</h4>
			<hr class="my-4">
			<div
				v-if="modalRemoveRewardData"
				class="award-image-remove"
			>
				<img
					v-if="modalRemoveRewardData.format !== 'pdf'"
					:src="modalRemoveRewardData.tempPath"
					alt=""
				>
				<vue-pdf-embed
					v-else
					:source="modalRemoveRewardData.tempPath"
				/>
			</div>
			<template #modal-footer>
				<b-button
					variant="secondary"
					:disabled="btnLoading"
					@click="modalRemoveReward = !modalRemoveReward"
				>
					Отмена
				</b-button>
				<b-button
					variant="danger"
					:disabled="btnLoading"
					@click="removeRewardUser(modalRemoveRewardData)"
				>
					<span
						v-if="btnLoading"
						class="btn-spinner"
					/>
					Отозвать награду
				</b-button>
			</template>
		</BModal>

		<BModal
			v-if="currentAward"
			v-model="modalAdd"
			modal-class="selected-modal"
			:title="'Добавление новой награды - ' + currentAward.name"
			size="lg"
			centered
		>
			<b-row class="mb-4">
				<b-col
					cols="12"
					md="6"
					offset-md="3"
				>
					<label
						ref="inputFileAdd"
						for="file-add"
						class="custom-file-upload"
						:class="modalAddFile ? '' : 'error'"
					/>
					<input
						id="file-add"
						type="file"
						accept="application/pdf, image/jpeg, image/png"
						style="display: none;"
						@change="modalAddEvent"
					>
				</b-col>
			</b-row>

			<template v-if="modalAddFile">
				<hr class="my-4">
				<div class="result-container">
					<img
						v-if="modalAddFile.type !== 'application/pdf'"
						:src="modalAddBase64"
						alt=""
					>
					<vue-pdf-embed
						v-else
						:source="modalAddBase64"
					/>
				</div>
			</template>
			<template #modal-footer>
				<b-button
					variant="secondary"
					:disabled="btnLoading"
					@click="modalAdd = !modalAdd"
				>
					Отмена
				</b-button>
				<b-button
					v-if="modalAddBase64"
					variant="success"
					:disabled="btnLoading"
					@click="addAndSaveReward"
				>
					<span
						v-if="btnLoading"
						class="btn-spinner"
					/>
					Наградить
				</b-button>
			</template>
		</BModal>

		<BModal
			v-model="modalSelect"
			modal-class="selected-modal"
			title="Награждение"
			:size="modalSize"
			centered
		>
			<b-row v-if="newFileCheck">
				<b-col
					cols="12"
					md="6"
					class="border-right-custom"
				>
					<div class="selected-modal-title with-image">
						{{ modalSelectData.type }}
						<span class="image-mini">
							<img
								v-if="modalSelectData.format !== 'pdf'"
								:src="modalSelectData.tempPath"
								alt=""
							>
							<vue-pdf-embed
								v-else
								:source="modalSelectData.tempPath"
							/>
						</span>
						<span class="text">Награждение сотрудника по выбранному шаблону</span>
					</div>
				</b-col>
				<b-col
					cols="12"
					md="6"
				>
					<label
						ref="inputFile"
						for="file"
						class="custom-file-upload"
						:class="modalSelectFile ? '' : 'error'"
					/>
					<input
						id="file"
						type="file"
						accept="application/pdf, image/jpeg, image/png"
						style="display: none;"
						@change="modalSelectDataUploadEvent"
					>
				</b-col>
			</b-row>
			<div v-else>
				<div class="simple-reward-title text-center">
					Наградить сотрудника выбранной наградой?
				</div>
			</div>
			<template v-if="modalSelectFile && newFileCheck">
				<hr class="my-4">
				<div class="result-container">
					<img
						v-if="modalSelectFile.type !== 'application/pdf'"
						:src="modalSelectBase64"
						alt=""
					>
					<vue-pdf-embed
						v-else
						:source="modalSelectBase64"
					/>
				</div>
			</template>
			<template #modal-footer>
				<div class="d-flex align-items-center justify-content-between w-100">
					<BFormGroup
						id="input-group-4"
						class="custom-switch custom-switch-sm m-0"
					>
						<b-form-checkbox
							v-model="newFileCheck"
							switch
							:disabled="btnLoading"
						>
							Загрузить другой шаблон
						</b-form-checkbox>
					</BFormGroup>
					<div>
						<b-button
							variant="secondary"
							:disabled="btnLoading"
							@click="modalSelect = !modalSelect"
						>
							Отмена
						</b-button>
						<b-button
							v-if="!newFileCheck"
							variant="success"
							:disabled="btnLoading"
							@click="reward"
						>
							<span
								v-if="btnLoading"
								class="btn-spinner"
							/>
							Наградить
						</b-button>
						<b-button
							v-if="newFileCheck && modalSelectBase64"
							variant="success"
							:disabled="btnLoading"
							@click="rewardNew"
						>
							<span
								v-if="btnLoading"
								class="btn-spinner"
							/>
							Наградить
						</b-button>
					</div>
				</div>
			</template>
		</BModal>
	</Sidebar>
</template>

<script>
import Sidebar from '@/components/ui/Sidebar' // сайдбар table
import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed'
const base64Encode = (data) =>
	new Promise((resolve, reject) => {
		const reader = new FileReader();
		reader.readAsDataURL(data);
		reader.onload = () => resolve(reader.result);
		reader.onerror = (error) => reject(error);
	});
	// import AwardsCard from '../profile/UserEarnings/AwardsCard.vue'
	// import UploadModal from '../modals/Upload'

export default {
	name: 'AwardUserSidebar',
	components: {
		// UploadModal,
		Sidebar,
		VuePdfEmbed,
	},
	data() {
		return {
			switchTab: 1,
			btnLoading: false,
			newFileCheck: false,
			isShow: false,
			modalPreview: false,
			modalPreviewData: {},
			tabIndex: 0,
			modalRemoveReward: false,
			modalRemoveRewardData: null,
			open: false,
			uploadModalOpen: false,
			userId: null,
			awards: [],
			modalSelect: false,
			modalSelectData: {},
			modalSelectFile: null,
			modalSelectBase64: null,
			modalAdd: false,
			modalAddFile: null,
			modalAddBase64: null,
			awardCategories: [],
			value: null,
			responseAward: [],
			currentAward: null,

		}
	},
	computed: {
		modalSize() {
			if (this.newFileCheck) {
				return 'lg';
			} else {
				return 'md';
			}
		}
	},
	watch: {
		modalAdd(val) {
			if (!val) {
				this.value = null;
				this.modalAddFile = null;
				this.modalAddBase64 = null;
				this.btnLoading = false;
			}
			return this.modalAdd;
		},
		modalSelect(val) {
			if (!val) {
				this.modalSelectData = {};
				this.modalSelectFile = null;
				this.modalSelectBase64 = null;
				this.btnLoading = false;
			}
			return this.modalSelect;
		},
		tabIndex(val) {
			const buttons = this.$refs.tabAwardUser.$refs.buttons;
			if (!buttons) return;
			buttons[val].$refs.link.$el.scrollIntoView({inline: 'end', behavior: 'smooth'});
		}
	},
	mounted() {
		setTimeout(() => {
			this.isShow = true;
		}, 500);
		document.addEventListener('award-user-sidebar', (e) => {
			this.open = true;
			this.userId = e.detail;
			this.getAll();
		});
	},
	methods: {
		availableLength(available){
			const avalFilter = available.filter(a => a.type === 'public');
			return avalFilter.length;
		},
		changeTab(id) {
			this.currentAward = id;
			this.switchTab = 1;
		},
		previewImage(data) {
			this.modalPreview = !this.modalPreview;
			this.modalPreviewData = data;
		},
		downloadImage(data, name) {
			var xhr = new XMLHttpRequest();
			xhr.open('GET', data.tempPath, true);

			xhr.responseType = 'arraybuffer';

			xhr.onload = function () {
				var arrayBufferView = new Uint8Array(this.response);
				let options = {};
				if (data.format === 'png') {
					options.type = 'image/png'
				}
				if (data.format === 'jpg') {
					options.type = 'image/jpeg'
				}
				if (data.format === 'pdf') {
					options.type = 'application/pdf'
				}
				var blob = new Blob([arrayBufferView], options);
				var imageUrl = window.URL.createObjectURL(blob);
				var a = document.createElement('a');
				a.href = imageUrl;
				a.download = `${name}.${data.format}`;
				document.body.appendChild(a);
				a.click();
				document.body.removeChild(a);
			};

			xhr.send();
		},
		async getAll() {
			let loader = this.$loading.show();
			await this.axios
				.get('/awards/type?key=nomination&user_id=' + this.userId)
				.then(response => {
					this.awards = [];
					this.awards = response.data.data;
					this.currentAward = this.awards[0];
					loader.hide();
				})
				.catch(error => {
					console.error(error);
					loader.hide();
				})
		},
		removeRewardUser(item) {
			let loader = this.$loading.show();
			this.btnLoading = true;
			this.axios
				.delete('/awards/reward-delete', {data: {user_id: item.user_id, award_id: item.award_id}})
				.then(() => {
					this.modalRemoveReward = false;
					this.$toast.success('Награда убрана');
					this.btnLoading = false;
					loader.hide();
					this.getAll();
				})
				.catch(error => {
					console.error(error);
				})
		},
		removeReward(item) {
			this.modalRemoveReward = !this.modalRemoveReward;
			this.modalRemoveRewardData = item;
		},
		async addAndSaveReward() {
			let loader = this.$loading.show();
			this.btnLoading = true;
			const formData = new FormData();
			formData.append('award_category_id', this.currentAward.id);
			formData.append('file[]', this.modalAddFile);
			formData.append('type', 'personal');
			await this.axios
				.post('/awards/store', formData, {
					headers: {
						'Content-Type': 'multipart/form-data'
					},
				})
				.then(response => {
					this.responseAward = response.data.data;
				})
				.catch(function (error) {
					console.error(error);
					loader.hide();
				});

			const formDataReward = new FormData();
			formDataReward.append('user_id', this.userId);
			formDataReward.append('award_id', this.responseAward[0].id);
			formDataReward.append('file', this.modalAddFile);
			await this.axios
				.post('/awards/reward', formDataReward, {
					headers: {
						'Content-Type': 'multipart/form-data',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
					},
				})
				.then(() => {
					this.modalAdd = false;
					this.$toast.success('Награжден');
					setTimeout(() => {
						this.modalAddFile = null;
						this.modalAddBase64 = null;
					}, 300);
					this.btnLoading = false;
					this.getAll();
					loader.hide();
				})
				.catch(function (error) {
					console.error(error);
					loader.hide();
				});
		},
		modalAddEvent(e) {
			let files = e.target.files || e.dataTransfer.files;
			if (!files.length) return;
			this.modalAddFile = files[0];
			if (this.modalAddFile.size > 2097152) {
				this.$toast.error('Максимальный размер файла - 2 МБ', {
					timeout: 5000
				});
			} else {
				base64Encode(this.modalAddFile)
					.then((val) => {
						this.modalAddBase64 = val;
					})
					.catch(() => {
						this.modalAddBase64 = null;
					});
			}

		},
		modalSelectDataUploadEvent(e) {
			let files = e.target.files || e.dataTransfer.files;
			if (!files.length) return;
			this.modalSelectFile = files[0];
			if (this.modalSelectFile.size > 2097152) {
				this.$toast.error('Максимальный размер файла - 2 МБ', {
					timeout: 5000
				});
			} else {
				base64Encode(this.modalSelectFile)
					.then((val) => {
						this.modalSelectBase64 = val;
					})
					.catch(() => {
						this.modalSelectBase64 = null;
					});
			}

		},
		openModalSelect(item, name) {
			this.modalSelect = !this.modalSelect;
			this.modalSelectData = item;
			this.modalSelectData.name = name;
			this.modalSelectFile = null;
			this.modalSelectBase64 = null;
		},
		reward() {
			let loader = this.$loading.show();
			this.btnLoading = true;
			const formData = new FormData();
			formData.append('user_id', this.userId);
			formData.append('award_id', this.modalSelectData.id);
			this.axios
				.post('/awards/reward', formData, {
					headers: {
						'Content-Type': 'multipart/form-data',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
					},
				})
				.then(() => {
					this.$toast.success('Добавлено');
					setTimeout(() => {
						this.modalSelectData = {};
						this.modalSelectFile = null;
						this.modalSelectBase64 = null;
						this.newFileCheck = false;
					}, 300);
					this.btnLoading = false;
					this.modalSelect = false;
					loader.hide();
					this.getAll();
				})
				.catch(function (error) {
					console.error(error);
					loader.hide();
				});
		},
		rewardNew() {
			let loader = this.$loading.show();
			this.btnLoading = true;
			const formData = new FormData();
			formData.append('user_id', this.userId);
			formData.append('award_id', this.modalSelectData.id);
			formData.append('file', this.modalSelectFile);
			this.axios
				.post('/awards/reward', formData, {
					headers: {
						'Content-Type': 'multipart/form-data',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
					},
				})
				.then(() => {
					this.$toast.success('Добавлено');
					setTimeout(() => {
						this.modalSelectData = {};
						this.modalSelectFile = null;
						this.modalSelectBase64 = null;
						this.newFileCheck = false;
					}, 300);
					this.btnLoading = false;
					this.modalSelect = false;
					loader.hide();
					this.getAll();
				})
				.catch(function (error) {
					console.error(error);
					loader.hide();
				});
		}
	}
}
</script>

<style lang="scss">
	.remove-award-modal {
		.title-remove {
			font-size: 20px;
			color: red;
			text-align: center;
		}

		img, canvas {
			width: 100% !important;
			height: auto !important;
		}

		.btn-spinner {
			display: inline-block;
			width: 15px;
			height: 15px;
			border: 3px solid rgba(255, 255, 255, .3);
			border-radius: 50%;
			margin-bottom: -2px;
			border-top-color: #fff;
			animation: spin 1s ease-in-out infinite;
			-webkit-animation: spin 1s ease-in-out infinite;
		}

		@keyframes spin {
			to {
				-webkit-transform: rotate(360deg);
			}
		}
		@-webkit-keyframes spin {
			to {
				-webkit-transform: rotate(360deg);
			}
		}
	}

	.preview-modal {
		img, canvas {
			width: 100% !important;
			height: auto !important;
		}
	}

	.selected-modal {
		.simple-reward-title {
			font-size: 16px;
			font-weight: 600;
			color: #666;
		}

		.btn-spinner {
			display: inline-block;
			width: 15px;
			height: 15px;
			border: 3px solid rgba(255, 255, 255, .3);
			border-radius: 50%;
			margin-bottom: -2px;
			border-top-color: #fff;
			animation: spin 1s ease-in-out infinite;
			-webkit-animation: spin 1s ease-in-out infinite;
		}

		@keyframes spin {
			to {
				-webkit-transform: rotate(360deg);
			}
		}
		@-webkit-keyframes spin {
			to {
				-webkit-transform: rotate(360deg);
			}
		}

		.custom-switch {
			padding-left: 0;

			.custom-control-label {
				font-size: 14px;
			}

			input[type="checkbox"] {
				position: absolute;
				margin: 8px 0 0 16px;
			}

			input[type="checkbox"] + label {
				position: relative;
				padding: 5px 0 0 50px;
				line-height: 1;
				margin: 10px 0;
			}

			input[type="checkbox"]:disabled + label {
				opacity: 0.5;
			}

			input[type="checkbox"] + label:before {
				content: "";
				position: absolute;
				display: block;
				left: 0;
				top: 0;
				width: 40px; /* x*5 */
				height: 24px; /* x*3 */
				border-radius: 16px; /* x*2 */
				background: #fff;
				border: 1px solid #d9d9d9;
				-webkit-transition: all 0.3s;
				transition: all 0.3s;
			}

			input[type="checkbox"] + label:after {
				content: "";
				position: absolute;
				display: block;
				left: 0px;
				top: 0px;
				width: 24px; /* x*3 */
				height: 24px; /* x*3 */
				border-radius: 16px; /* x*2 */
				background: #fff;
				border: 1px solid #d9d9d9;
				-webkit-transition: all 0.3s;
				transition: all 0.3s;
			}

			input[type="checkbox"] + label:hover:after {
				box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
			}

			input[type="checkbox"]:checked + label:after {
				margin-left: 16px;
			}

			input[type="checkbox"]:checked + label:before {
				background: #55D069;
			}

			&.custom-switch-small {
				input[type="checkbox"] {
					margin: 5px 0 0 10px;
				}

				input[type="checkbox"] + label {
					position: relative;
					padding: 0 0 0 32px;
					line-height: 1.3em;
				}

				input[type="checkbox"] + label:before {
					width: 25px; /* x*5 */
					height: 15px; /* x*3 */
					border-radius: 10px; /* x*2 */
				}

				input[type="checkbox"] + label:after {
					width: 15px; /* x*3 */
					height: 15px; /* x*3 */
					border-radius: 10px; /* x*2 */
				}

				input[type="checkbox"] + label:hover:after {
					box-shadow: 0 0 3px rgba(0, 0, 0, 0.3);
				}

				input[type="checkbox"]:checked + label:after {
					margin-left: 10px; /* x*2 */
				}
			}
		}

		.multiselect {
			.multiselect__tags {
				border: 1px solid #28a745;
			}

			&.error {
				.multiselect__tags {
					border: 1px solid red;
				}
			}
		}

		.border-right-custom {
			border-right: 1px solid #ddd;
		}

		.selected-modal-title {
			display: flex;
			align-items: center;

			.text {
				font-size: 16px;
				line-height: 1.5;
				color: #888;
				margin-left: 20px;
			}

			.image-mini {
				min-width: 75px;
				min-height: 75px;
				max-height: 75px;
				border-radius: 8px;
				overflow: hidden;

				img, .vue-pdf-embed {
					width: 100%;
					height: 100px;
					object-fit: cover;
				}
			}
		}

		.result-container {
			overflow: hidden;
			cursor: pointer;
			display: flex;
			align-items: center;
			justify-content: center;
			position: relative;
			border-radius: 10px;

			img {
				width: 100%;
				height: auto;
			}

			canvas {
				width: 100% !important;
				height: auto !important;
			}
		}

		.download-image-container {
			overflow: hidden;
			cursor: pointer;
			display: flex;
			align-items: center;
			justify-content: center;
			position: relative;
			border-radius: 10px;

			img {
				width: 100%;
				height: auto;
				transition: 0.2s all ease;
			}

			canvas {
				width: 100% !important;
				height: auto !important;
			}

			&:before {
				content: 'Скачать';
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				z-index: 1;
				opacity: 0;
				background-color: #333;
				transition: 0.2s all ease;
			}

			&:after {
				content: 'Скачать';
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				text-transform: uppercase;
				font-size: 24px;
				display: flex;
				align-items: center;
				justify-content: center;
				z-index: 2;
				color: #fff;
				opacity: 0;
				transition: 0.2s all ease;
			}

			&:hover {
				img {
					transform: scale(1.1);
				}

				&:before {
					opacity: 0.5;
				}

				&:after {
					opacity: 1;
				}
			}
		}

		.custom-file-upload {
			width: 100%;
			margin: 0 auto;
			height: 75px;
			border-radius: 10px;
			border: 1px dashed #28a745;
			position: relative;
			cursor: pointer;
			transition: 0.2s all ease;

			&:before {
				content: 'Нажмите, чтобы загрузить другой файл';
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				display: flex;
				align-items: center;
				justify-content: center;
				font-size: 14px;
				color: #999;
				text-transform: uppercase;
				transition: 0.2s all ease;
			}

			&.error {
				border: 1px dashed red;

				&:before {
					content: 'Нажмите, чтобы загрузить файл';
				}
			}


			&:hover {
				background-color: #f2f2f2;

				&:before {
					color: #333;
					transform: scale(1.1);
				}
			}
		}
	}

	#award-user-sidebar {
		&.show {
			.ui-sidebar__body {
				transform: translateX(0);
			}
		}

		.ui-sidebar__body {
			border-radius: 20px 0 0 20px;
			transform: translateX(100%);
			overflow: hidden !important;
		}

		.ui-sidebar__header {
			padding: 20px 25px !important;
			background: #ffffff !important;
			border-bottom: 1px solid #ddd;

			span {
				font-size: 24px;
				color: #333 !important;
				font-weight: 700;
			}
		}

		.ui-sidebar__content {
			padding: 20px 25px !important;
		}

		@media screen and (min-width: 768px) {
			.col-md-20 {
				flex: 0 0 20%;
				max-width: 20%;
			}
		}

		.prev-next {
			position: absolute;
			top: -1px;
			right: 0;
			height: 63px;
			display: flex;
			align-items: center;
			justify-content: center;
			border-bottom: 1px solid #dee2e6;
			background-color: #fff;
			width: 120px;

			span {
				width: 40px;
				height: 40px;
				border-radius: 50px;
				display: inline-flex;
				align-items: center;
				justify-content: center;
				border: 1px solid #ED2353;
				cursor: pointer;

				i {
					font-size: 18px;
					color: #ED2353;
				}

				&:hover {
					background-color: #ED2353;

					i {
						color: #fff;
					}
				}
			}

			.next {
				margin-left: 10px;
			}
		}

		.tabs {
			position: relative;

			.accrual-tab {
				margin-top: 30px;
				overflow-x: hidden;
				padding-bottom: 30px;
			}

			.nav-tabs {
				flex-wrap: nowrap;
				white-space: nowrap;
				overflow: hidden;
				margin-right: 120px;

				.nav-item {
					.nav-link {
						font-size: 2.1rem;
						border-bottom: none;
						margin-top: 0.1rem;
						line-height: 2em;
						color: #8D8D8D;
						font-family: "Open Sans", sans-serif;
						font-weight: 600;
						transition: color 0.3s;
						padding: 1.5rem 0 0 0;
						cursor: pointer;
						margin-right: 40px;
						background-color: transparent;
						border-top: 4px solid transparent;

						&:hover {
							border-color: transparent;
							color: #ED2353;
						}

						&.active {
							border-top: 4px solid #ED2353;
							color: #ED2353;
						}
					}
				}
			}
		}

		.award-image.my-award {
			border: 2px solid green;
			position: relative;

			canvas {
				width: 100% !important;
				height: auto !important;
			}

			img, .vue-pdf-embed {
				transition: 0.15s all ease;
			}

			i {
				position: absolute;
				top: 50%;
				left: 50%;
				font-size: 16px;
				transform: translate(-50%, -50%) scale(0.9);
				opacity: 0;
				transition: 0.2s all ease;
				color: red;
			}

			&:hover {
				border: 2px solid red;

				img, .vue-pdf-embed {
					filter: grayscale(1);
				}

				i {
					transform: translate(-50%, -50%) scale(1.1);
					opacity: 1;
				}
			}
		}

		.or {
			font-size: 18px;
			color: #999;
			text-align: center;
			margin: 15px 0 10px 0;
		}

		.title-not-rewards {
			font-size: 16px;
			color: #aaa;
		}

		.or2 {
			line-height: 1;
			margin-bottom: 20px;
			font-size: 22px;
			color: #999;
			text-align: center;
		}

		.card-title {
			font-size: 18px;
			color: #999;
		}

		.award-image {
			position: relative;
			height: 100px;
			width: 100%;
			display: flex;
			align-items: center;
			justify-content: center;
			overflow: hidden;
			border-radius: 8px;
			border: 1px solid #ddd;
			cursor: pointer;
			transition: 0.15s all ease;

			.button {
				position: absolute;
				bottom: 0;
				padding: 10px 12px;
				font-size: 20px;
				z-index: 22;
				background-color: #fff;
				box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;

				&.download {
					left: 0;
					color: #045e92;
					border-radius: 0 0 0 4px;
				}

				&.award {
					right: 0;
					color: #28a745;
					border-radius: 0 0 4px 0;
				}
			}

			img {
				width: 100%;
				height: 100px;
				object-fit: cover;
				transition: 0.15s all ease;

				&:hover {
					transform: scale(1.1);
				}
			}

			.vue-pdf-embed {
				width: 100%;
				object-fit: cover;
				transition: 0.15s all ease;

				&:hover {
					transform: scale(1.1);
				}
			}
		}
	}

</style>
