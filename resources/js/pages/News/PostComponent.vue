<template>
	<div>
		<div
			:class="'news-item ' + ((showComments == true || showFiles == true) ? 'news-item--with-comments' : '')"
			v-observe-visibility="{
				callback: viewsChanged,
				once: true,
			}"
		>
			<div class="news-item__header">
				<div class="news-item__info">
					<img
						class="news-item__avatar"
						:src="currentPost.author ? currentPost.author.avatar : null"
					>
					<div class="news-item__name-time">
						<div class="news-item__name-access">
							<div class="news-item__info-block">
								<span class="news-item__name">
									{{ currentPost.author ? currentPost.author.name : null }}
								</span>
								<span class="news-item__time">
									{{ createdAt }}
								</span>
							</div>
							<img src="/icon/news/some-icons/arrow-right.svg">
							<div
								:class="'news-item__access ' + (currentPost.available_for == null ? '' : 'news-item__access--have-users')"
								:title="currentPost.available_for == null ? 'Всем пользователям'
									: currentPost.available_for.map(entry => entry.name).join(', ')"
							>
								<span
									:class="'news-item__access-text ' + (userAccessListShow ? 'news-item__access-text--active' : '')"
									@click="toggleUsersAccessList"
									v-html="currentPost.available_for == null ? 'Всем пользователям' : currentPost.available_for.map(entry => entry.name).join(', ')"
								/>
							</div>
						</div>
					</div>
				</div>

				<div class="news-item__header-actions">
					<div class="news-menu">
						<img
							class="news-item__header-action hover-pointer news-icon"
							@click="toggleShowPopup()"
							src="/icon/news/post-actions/menu.svg"
							alt="img"
						>
						<div
							v-show="showPopup"
							:class="'news-menu-popup ' + (currentPost.is_favourite ? 'news-menu-popup--favorite' : '')"
						>
							<div class="news-menu-popup__container">
								<div class="news-menu-popup__arrow" />
								<div
									class="news-menu-popup__item"
									:class="{
										active: currentPost.is_favourite
									}"
									@click="favouritePost(currentPost.id)"
								>
									<img
										class="news-menu-popup__img"
										alt="img"
										src="/icon/news/news-popup/favorite.svg"
									>
									<span
										class="news-menu-popup__text"
										v-html="currentPost.is_favourite ? 'Удалить из избранного' :'Добавить в избранное'"
									/>
								</div>
								<div
									class="news-menu-popup__item"
									@click="copyPostLink"
								>
									<img
										class="news-menu-popup__img"
										alt="img"
										src="/icon/news/news-popup/copy-link.svg"
									>
									<span
										class="news-menu-popup__text"
										v-html="'Скопировать ссылку'"
									/>
								</div>
								<div
									v-if="$can('news_edit')"
									v-show="currentPost.author ? (($can('news_edit') || currentPost.author.id === me.id) ? currentPost.author.id: null ) : null"
									class="news-menu-popup__item"
									@click="editPost"
								>
									<img
										class="news-menu-popup__img"
										alt="img"
										src="/icon/news/news-popup/edit.svg"
									>
									<span
										class="news-menu-popup__text"
										v-html="'Редактировать'"
									/>
								</div>
								<div
									v-if="$can('news_edit')"
									v-show="currentPost.author ? (($can('news_edit') || currentPost.author.id === me.id) ? currentPost.author.id: null ) : null"
									class="news-menu-popup__item"
									@click="deletePost(currentPost.id)"
								>
									<img
										class="news-menu-popup__img"
										alt="img"
										src="/icon/news/news-popup/delete.svg"
									>
									<span
										class="news-menu-popup__text"
										v-html="'Удалить'"
									/>
								</div>
							</div>
						</div>
					</div>
					<img
						@click="pinPost(currentPost.id)"
						:class="'news-item__header-action hover-pointer ' + (currentPost.is_pinned == false ? 'news-icon' : '')"
						:src="currentPost.is_pinned == true ? '/icon/news/post-actions/pinned.svg' : '/icon/news/post-actions/pin.svg'"
					>
				</div>
			</div>
			<div class="news-item__title">
				{{ currentPost.title }}
			</div>
			<div
				v-show="showFullContent"
				class="news-item__content"
				ref="newsItemContent"
				v-html="content"
			/>
			<div
				class="gallery-modal"
				v-if="showModalImages"
				@click="showModalImages = !showModalImages"
			>
				<div
					class="gallery-modal-content"
					@click.stop
				>
					<b-carousel
						id="modal-carousel"
						v-model="galleryIndex"
						:interval="0"
						controls
						indicators
					>
						<b-carousel-slide
							v-for="(image, indexIdx) in images"
							:key="indexIdx"
						>
							<template #img>
								<img
									:src="image"
									alt="Картинка"
								>
							</template>
						</b-carousel-slide>
					</b-carousel>
				</div>
			</div>
			<span
				v-show="currentPost.is_pinned"
				class="news-item__show-full"
				@click="toggleShowFullContent"
				v-html="showFullContent ? 'Скрыть подробности' :'Показать полностью'"
			/>
			<div class="news-item__footer">
				<div class="news-item__footer-actions">
					<div class="news-item__footer-action">
						<img
							class="hover-pointer"
							v-if="currentPost.is_liked == true"
							@click="likePost(currentPost.id)"
							src="/icon/news/post-actions/like-active.svg"
						>
						<img
							v-else
							class="news-icon hover-pointer"
							@click="likePost(currentPost.id)"
							src="/icon/news/post-actions/like.svg"
						>
						<span class="news-item__footer-count">{{ currentPost.likes_count }}</span>
					</div>
					<div class="news-item__footer-action">
						<img
							class="news-icon hover-pointer"
							src="/icon/news/post-actions/comments.svg"
							@click="toggleShowComments"
						>
						<span class="news-item__footer-count">{{ currentPost.comments_count }}</span>
					</div>

					<div
						v-show="this.currentPost.files.length != 0"
						class="news-item__footer-action"
						@click="toggleShowFiles"
					>
						<img
							class="news-icon hover-pointer"
							src="/icon/news/some-icons/file.svg"
						>
						<span
							class="news-item__footer-count"
							v-html="currentPost.files.length"
						/>
					</div>
					<!--                    <div class="news-item__footer-action">-->
					<!--                        <img class="news-icon hover-pointer" src="/icon/news/post-actions/menu.svg">-->
					<!--                    </div>-->
				</div>
				<div class="news-item__views">
					<img src="/icon/news/some-icons/view.svg">
					<span class="news-item__footer-count">{{ currentPost.views_count }}</span>
				</div>
			</div>
			<CommentsComponent
				v-show="showComments"
				@changeCommentsCount="changeCommentsCount"
				:me="me"
				@send="getData"
				ref="comments"
			/>
		</div>

		<div
			v-show="showFiles"
			class="news-file-preview"
		>
			<img
				v-for="(file, index) in currentPost.files"
				:key="index"
				class="news-file-preview__item"
				alt=""
				@click="downloadFile(file)"
				:src="getFilePreview(file)"
			>
		</div>

		<div
			ref="NewsCommentInput"
			v-show="showComments"
			class="news-comment-store"
		>
			<img
				:src="me ? me.avatar : null"
				class="news-comment-store__avatar"
			>
			<div class="news-comment-store__form">
				<input
					type="text"
					v-model="commentText"
					placeholder="Добавить комментарий"
					@keyup.enter="sendComment(currentPost.id)"
				>
				<img
					class="hover-pointer"
					@click="sendComment(currentPost.id)"
					src="/icon/news/comments/send.svg"
				>
			</div>
		</div>

		<div
			class="news-bg"
			v-show="showPopup"
			@click.self="toggleShowPopup"
		/>
	</div>
</template>

<script>
/* eslint-disable vue/no-mutating-props */
import CommentsComponent from '@/pages/News/CommentsComponent'
import { useUnviewedNewsStore } from '@/stores/UnviewedNewsCount'
import { mapActions } from 'pinia'
import { pluralForm } from '@/composables/pluralForm.js'

export default {
	name: 'PostComponent',
	components: {
		CommentsComponent
	},
	props: {
		post: {
			required: true
		},
		me: {
			required: true
		}
	},
	data() {
		return {
			currentPost: this.post,

			showFiles: false,
			showComments: false,
			showPopup: false,

			userAccessListShow: false,
			showFullContent: false,

			commentText: '',
			parentId: null,
			images: [],
			galleryIndex: null,
			showModalImages: false
		}
	},
	computed: {
		createdAt(){
			const created = this.$moment.utc(this.currentPost.created_at)
			const now = this.$moment.utc(Date.now())
			const diff = now.diff(created, 'hours')
			const min = now.diff(created, 'minutes')
			const local = created.local()
			return diff > 48
				? local.format('DD.MM.YYYY в HH:mm')
				: diff > 24
					? '1 день назад'
					: diff > 0
						? `${diff} ${pluralForm(diff, ['час', 'часа', 'часов'])} назад`
						: `${min} ${pluralForm(diff, ['минуту', 'минуты', 'минут'])} назад`
		},
		content(){
			return this.currentPost.content.replaceAll('<a ', '<a target="_blank" ')
		},
	},
	mounted() {
		this.showFullContent = this.currentPost.is_pinned == false;
		const imagesPost = this.$refs.newsItemContent.querySelectorAll('img');
		imagesPost.forEach(i => this.images.push(i.src));
		for(let i = 0; i < imagesPost.length; i++){
			imagesPost[i].addEventListener('click', () => {
				this.galleryIndex = i;
				this.showModalImages = true;
			})
		}
	},
	methods: {
		...mapActions(useUnviewedNewsStore, ['getUnviewedNewsCount']),
		getFileTypeByExtension(extension) {
			switch (extension) {
			case 'png': {
				return 'image/png';
			}
			case 'bmp': {
				return 'image/bmp';
			}
			case 'gif': {
				return 'image/git';
			}
			case 'jpg': {
				return 'image/jpeg';
			}
			case 'jpeg': {
				return 'image/jpeg';
			}
			case 'tif': {
				return 'image/tiff';
			}
			case 'tiff': {
				return 'image/tiff';
			}
			case 'webp': {
				return 'image/webp';
			}
			default: {
				return 'file';
			}
			}
		},

		async downloadFile(file) {
			await this.axios.get(file.url, {responseType: 'blob'})
				.then(response => {
					const blob = new Blob([response.data], {type: this.getFileTypeByExtension(file.extension)});
					const link = document.createElement('a');
					link.href = URL.createObjectURL(blob);
					link.download = file.original_name;
					link.click();
					URL.revokeObjectURL(link.href);
				})
				.catch(console.error)
		},

		toggleShowFullContent() {
			this.showFullContent = !this.showFullContent;
		},

		toggleUsersAccessList() {
			if (this.currentPost.available_for != null) {
				this.userAccessListShow = !this.userAccessListShow;
			}
		},

		toggleShowPopup() {
			this.showPopup = !this.showPopup;
		},

		toggleShowFiles() {
			this.showFiles = !this.showFiles;
		},

		toggleShowComments() {
			if (!this.showComments) {
				this.getPostComments(this.currentPost.id)
			} else {
				this.showComments = false;
			}
		},

		copyPostLink() {
			this.toggleShowPopup();
			navigator.clipboard.writeText(location.protocol + '//' + location.host + location.pathname + '?post_id=' + this.currentPost.id);
		},

		getFilePreview(file) {
			if (file.extension == 'png' ||
                file.extension == 'jpg' ||
                file.extension == 'gif' ||
                file.extension == 'tif' ||
                file.extension == 'tiff' ||
                file.extension == 'webp' ||
                file.extension == 'jpeg') {

				return file.url;
			} else if (file.extension == 'doc' || file.extension == 'docx') {

				return '/images/some-files/word.png';
			} else if (file.extension == '7z' || file.extension == 'zip' || file.extension == 'rar') {

				return '/images/some-files/rar.png';
			} else {
				return '/images/some-files/img.png';
			}
		},

		getPostComments(postId) {
			this.$refs.comments.getComments(postId)
		},

		getData(data) {
			const el = this.$refs.NewsCommentInput;
			if (el) {
				el.scrollIntoView({block: 'center', behavior: 'smooth'});
				el.focus();
			}
			this.parentId = data.parentId;
			this.commentText = data.userName + ', ';
		},

		changeCommentsCount(data) {
			this.showComments = true;
			this.currentPost.comments_count = data.comments_count;
		},

		async likePost(id) {
			await this.axios.post('/news/' + id + '/like')
				.then(() => {
					if (this.currentPost.is_liked) {
						this.currentPost.likes_count--;
					} else {
						this.currentPost.likes_count++;
					}

					this.currentPost.is_liked = !this.currentPost.is_liked;
				})
				.catch(() => {
				});
		},

		async viewsChanged() {
			await this.axios.post('news/' + this.currentPost.id + '/views')
				.then(res => {
					this.currentPost.views_count = res.data.data.views_count;
				})
				.catch(res => {
					console.log(res)
				})
			this.getUnviewedNewsCount();
		},

		async favouritePost(id) {
			await this.axios.post('news/' + id + '/favourite')
				.then(res => {
					console.log(res);
					this.toggleShowPopup();
					this.$emit('update-news-list');
				})
				.catch()
		},

		async pinPost(id) {
			await this.axios.post('/news/' + id + '/pin')
				.then(response => {
					this.post.is_pinned = response.data.data.is_pinned;
					this.showFullContent = false;
				})
				.catch(response => {
					console.log(response);
				});
		},

		async sendComment(postId) {
			if (this.commentText == '') {
				return
			}

			let formData = new FormData;
			formData.set('content', this.commentText);
			this.commentText = '';
			formData.append('parent_id', this.parentId == null ? '' : this.parentId);
			this.parentId = null;

			await this.axios.post('/news/' + postId + '/comments', formData)
				.then(() => {
					this.currentPost.comments_count = this.currentPost.comments_count + 1;
					this.getPostComments(postId);
				})
				.catch(response => {
					console.log(response);
				});
		},

		editPost() {
			this.toggleShowPopup();
			window.scrollTo({
				top: 0,
				behavior: 'smooth',
			});

			this.$emit('editPost', {
				id: this.currentPost.id,
				available_for: this.currentPost.available_for,
				title: this.currentPost.title,
				content: this.currentPost.content,
				files: this.currentPost.files,
			});
		},

		async deletePost(postId) {
			await this.axios.delete('/news/' + postId)
				.then(() => {
					this.toggleShowPopup();
					this.$emit('update-news-list');
				})
				.catch(response => {
					console.log(response);
				});
		}
	}
}
</script>

<style lang="scss">
	.gallery-modal{
		position: fixed;
		z-index: 9999;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-color: rgba(0,0,0,0.5);
		display: flex;
		align-items: center;
		justify-content: center;
		.gallery-modal-content{
			max-width: 95%;
			max-height: calc(100vh - 100px);
			img{
				width: 100%;
				height: calc(100vh - 150px);
			}
		}
		.carousel-control-next, .carousel-control-prev{
			position: fixed !important;
			z-index: 10;
			bottom: unset;
			top: 50%;
			transform: translateY(-50%);
			width: 50px;
			height: 50px;
			border-radius: 50%;
			background-color: #333;
		}
		.carousel-control-next{
			right: 50px;
		}
		.carousel-control-prev{
			left: 50px;
		}
	}
</style>
