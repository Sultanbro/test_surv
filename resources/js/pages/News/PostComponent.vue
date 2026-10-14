<template>
	<div class="PostComponent">
		<div
			v-observe-visibility="{
				callback: viewsChanged,
				once: true,
			}"
			:class="'news-item ' + ((showComments == true || showFiles == true) ? 'news-item--with-comments' : '')"
		>
			<NewsPostHeaderMobile
				v-if="isMobile"
				:post="currentPost"
				:can-edit="isAdmin || isAuthor"
				class="PostComponent-header"
				@favorite="favouritePost(currentPost.id)"
				@copy-link="copyPostLink"
				@edit="editPost"
				@delete="deletePost(currentPost.id)"
				@toggle-pinned="pinPost(currentPost.id)"
			/>
			<div
				v-else
				class="news-item__header"
			>
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
								>
									{{ currentPost.available_for == null ? 'Всем пользователям' : currentPost.available_for.map(entry => entry.name).join(', ') }}
								</span>
							</div>
						</div>
					</div>
				</div>

				<div class="news-item__header-actions">
					<div class="news-menu">
						<img
							class="news-item__header-action hover-pointer news-icon"
							src="/icon/news/post-actions/menu.svg"
							alt="img"
							@click="toggleShowPopup()"
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
									<span class="news-menu-popup__text">
										{{ currentPost.is_favourite ? 'Удалить из избранного' :'Добавить в избранное' }}
									</span>
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
									<span class="news-menu-popup__text">
										Скопировать ссылку
									</span>
								</div>
								<div
									v-if="isAdmin || isAuthor"
									class="news-menu-popup__item"
									@click="editPost"
								>
									<img
										class="news-menu-popup__img"
										alt="img"
										src="/icon/news/news-popup/edit.svg"
									>
									<span class="news-menu-popup__text">
										Редактировать
									</span>
								</div>
								<div
									v-if="isAdmin || isAuthor"
									class="news-menu-popup__item"
									@click="deletePost(currentPost.id)"
								>
									<img
										class="news-menu-popup__img"
										alt="img"
										src="/icon/news/news-popup/delete.svg"
									>
									<span class="news-menu-popup__text">
										Удалить
									</span>
								</div>
							</div>
						</div>
					</div>
					<img
						:class="'news-item__header-action hover-pointer ' + (currentPost.is_pinned == false ? 'news-icon' : '')"
						:src="currentPost.is_pinned == true ? '/icon/news/post-actions/pinned.svg' : '/icon/news/post-actions/pin.svg'"
						@click="pinPost(currentPost.id)"
					>
				</div>
			</div>
			<div class="news-item__title">
				{{ currentPost.title }}
			</div>
			<!-- eslint-disable -->
			<div
				v-show="showFullContent"
				ref="newsItemContent"
				class="news-item__content"
				v-html="content"
			/>
			<!-- eslint-enable -->
			<div
				v-if="showModalImages"
				class="gallery-modal"
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
			>
				{{ showFullContent ? 'Скрыть подробности' : 'Показать полностью' }}
			</span>

			<NewsQNA
				v-if="currentPost.questions && currentPost.questions.length"
				:qna="currentPost.questions"
				@vote="onVote"
			/>

			<div class="news-item__footer">
				<div class="news-item__footer-actions">
					<div class="news-item__footer-action">
						<img
							v-if="currentPost.is_liked == true"
							class="hover-pointer"
							src="/icon/news/post-actions/like-active.svg"
							@click="likePost(currentPost.id)"
						>
						<img
							v-else
							class="news-icon hover-pointer"
							src="/icon/news/post-actions/like.svg"
							@click="likePost(currentPost.id)"
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
						v-show="currentPost.files.length != 0"
						class="news-item__footer-action"
						@click="toggleShowFiles"
					>
						<img
							class="news-icon hover-pointer"
							src="/icon/news/some-icons/file.svg"
						>
						<span class="news-item__footer-count">
							{{ currentPost.files.length }}
						</span>
					</div>
				</div>
				<div
					class="news-item__views"
					@click.stop="toggleViews"
					@mouseover="mouseEnterViews"
					@mouseleave="mouseLeaveViews"
				>
					<img
						class="news-icon2"
						src="/icon/news/some-icons/view.svg"
					>
					<span class="news-item__footer-count">{{ currentPost.views_count }}</span>
					<PopupMenu
						v-click-outside="closeViews"
						:class="{
							PostComponent_hidden: !isViewsPopup
						}"
						position="topRight"
						max-height="250px"
					>
						<template #before>
							<div class="px-3">
								Просмотры
							</div>
							<hr>
						</template>
						<div
							v-for="view, index in currentPost.viewers || []"
							:key="index"
							class="PostComponent-viewer"
						>
							<JobtronAvatar
								:size="24"
								:image="view.avatar"
								:title="view.name"
							/>
							{{ view.name }}
						</div>
					</PopupMenu>
				</div>
			</div>

			<!-- <CommentsComponent
				v-show="showComments"
				ref="comments"
				:me="me"
				@changeCommentsCount="changeCommentsCount"
				@send="getData"
			/> -->
			<NewsComments
				v-if="showComments"
				:post-id="currentPost.id"
				@changeCommentsCount="changeCommentsCount"
				@send="getData"
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
				:src="getFilePreview(file)"
				@click="downloadFile(file)"
			>
		</div>

		<div
			v-show="showComments"
			ref="NewsCommentInput"
			class="news-comment-store"
		>
			<img
				:src="$laravel.avatar"
				class="news-comment-store__avatar"
			>
			<div class="news-comment-store__form">
				<input
					v-model="commentText"
					type="text"
					placeholder="Добавить комментарий"
					@keyup.enter="sendComment(currentPost.id)"
				>
				<img
					class="hover-pointer"
					src="/icon/news/comments/send.svg"
					@click="sendComment(currentPost.id)"
				>
			</div>
		</div>

		<div
			v-show="showPopup"
			class="news-bg"
			@click.self="toggleShowPopup"
		/>
	</div>
</template>

<script>
/* eslint-disable camelcase */
/* eslint-disable vue/no-mutating-props */
import { mapGetters } from 'vuex'
import { mapState, mapActions } from 'pinia'
import { useUnviewedNewsStore } from '@/stores/UnviewedNewsCount'
import { usePortalStore } from '@/stores/Portal'
import { pluralForm } from '@/composables/pluralForm.js'
import * as API from '@/stores/api/news.js'

import NewsComments from '@/pages/News/Comment/NewsComments'
import PopupMenu from '@ui/PopupMenu'
import JobtronAvatar from '@ui/Avatar'
import NewsQNA from './NewsQNA'
import NewsPostHeaderMobile from './Post/NewsPostHeaderMobile'


const imageTypes = {
	png: 'image/png',
	bmp: 'image/bmp',
	gif: 'image/gif',
	jpg: 'image/jpeg',
	jpeg: 'image/jpeg',
	tif: 'image/tiff',
	tiff: 'image/tiff',
	webp: 'image/webp',
}

export default {
	name: 'PostComponent',
	components: {
		NewsComments,
		PopupMenu,
		JobtronAvatar,
		NewsQNA,
		NewsPostHeaderMobile,
	},
	props: {
		post: {
			type: Object,
			required: true
		},
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
			showModalImages: false,

			isViewsPopup: false,
			viewsPopupTimeout: null,
			isVoteProgress: false,
		}
	},
	computed: {
		...mapState(usePortalStore, ['isAdmin']),
		...mapGetters(['user']),

		content(){
			return this.currentPost.content.replaceAll('<a ', '<a target="_blank" ')
		},
		isAuthor(){
			return this.currentPost?.author?.id === this.$laravel.userId
		},
		isMobile(){
			return this.$viewportSize.width <= 900
		},
		createdAt(){
			const created = this.$moment.utc(this.post.created_at)
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
	},
	mounted() {
		this.showFullContent = this.currentPost.is_pinned == false
		const imagesPost = this.$refs.newsItemContent.querySelectorAll('img')
		imagesPost.forEach(i => this.images.push(i.src))
		for(let i = 0; i < imagesPost.length; i++){
			imagesPost[i].addEventListener('click', () => {
				this.galleryIndex = i
				this.showModalImages = true
			})
		}
	},
	methods: {
		...mapActions(useUnviewedNewsStore, ['getUnviewedNewsCount']),
		getFileTypeByExtension(extension) {
			return imageTypes[extension] || 'file'
		},

		async downloadFile(file) {
			try {
				const {data} = await this.axios.get(file.url, {responseType: 'blob'})
				const blob = new Blob([data], {type: this.getFileTypeByExtension(file.extension)})
				const link = document.createElement('a')
				link.href = URL.createObjectURL(blob)
				link.download = file.original_name
				link.click()
				URL.revokeObjectURL(link.href)
			}
			catch (error) {
				console.error(error)
			}
		},

		toggleShowFullContent() {
			this.showFullContent = !this.showFullContent
		},

		toggleUsersAccessList() {
			if (this.currentPost.available_for != null) {
				this.userAccessListShow = !this.userAccessListShow
			}
		},

		toggleShowPopup() {
			this.showPopup = !this.showPopup
		},

		toggleShowFiles() {
			this.showFiles = !this.showFiles
		},

		async toggleShowComments() {
			if (!this.showComments) {
				await this.getPostComments(this.currentPost.id)
				this.showComments = true
			}
			else {
				this.showComments = false
			}
		},

		copyPostLink() {
			this.toggleShowPopup()
			navigator.clipboard.writeText(location.protocol + '//' + location.host + location.pathname + '?post_id=' + this.currentPost.id)
		},

		getFilePreview(file) {
			if (Object.keys(imageTypes).includes(file.extension)) return file.url
			if (file.extension == 'doc' || file.extension == 'docx') return '/images/some-files/word.png'
			if (file.extension == '7z' || file.extension == 'zip' || file.extension == 'rar') return '/images/some-files/rar.png'
			return '/images/some-files/img.png'
		},

		async getPostComments(/* postId */) {
			// await this.$refs.comments.getComments(postId)
		},

		getData(data) {
			const el = this.$refs.NewsCommentInput
			if (el) {
				el.scrollIntoView({block: 'center', behavior: 'smooth'})
				el.querySelector('input')?.focus()
			}
			this.parentId = data.parentId
			this.commentText = data.userName + ', '
		},

		changeCommentsCount(data) {
			this.showComments = true
			this.currentPost.comments_count = data.comments_count
		},

		async likePost(id) {
			try {
				await API.newsLike(id)
				if (this.currentPost.is_liked) {
					this.currentPost.likes_count--
				}
				else {
					this.currentPost.likes_count++
				}
				this.currentPost.is_liked = !this.currentPost.is_liked
			}
			catch (error) {
				console.error(error)
			}
		},

		async viewsChanged() {
			try {
				const {views_count} = await API.newsViews(this.currentPost.id)
				this.currentPost.views_count = views_count
			}
			catch (error) {
				console.error(error)
			}
			this.getUnviewedNewsCount()
		},

		async favouritePost(id) {
			try {
				await API.newsFavourite(id)
				this.toggleShowPopup()
				this.$emit('update-news-list')
			}
			catch (error) {
				console.error(error)
			}
		},

		async pinPost(id) {
			try {
				const {is_pinned} = await API.newsPin(id)
				this.post.is_pinned = is_pinned
				this.showFullContent = false
			}
			catch (error) {
				console.error(error)
			}
		},

		async sendComment(postId) {
			if (this.commentText == '') return

			const formData = new FormData
			formData.set('content', this.commentText)
			formData.append('parent_id', this.parentId == null ? '' : this.parentId)

			try {
				await API.newsComment(postId, formData)
				this.currentPost.comments_count = this.currentPost.comments_count + 1
				this.showComments = false
				this.$nextTick(() => {
					this.showComments = true
				})
			}
			catch (error) {
				console.error(error)
			}

			this.commentText = ''
			this.parentId = null
		},

		editPost() {
			this.toggleShowPopup()
			window.scrollTo({
				top: 0,
				behavior: 'smooth',
			})

			this.$emit('editPost', {
				id: this.currentPost.id,
				available_for: this.currentPost.available_for,
				title: this.currentPost.title,
				content: this.currentPost.content,
				files: this.currentPost.files,
				questions: JSON.parse(JSON.stringify(this.currentPost.questions)),
			})
		},

		async deletePost(postId) {
			if(!confirm('Вы действительно хотите удалить новость?')) return
			try {
				await API.newsDelete(postId)
				this.toggleShowPopup()
				this.$emit('update-news-list')
			}
			catch (error) {
				console.error(error)
			}
		},

		openViews(){
			this.isViewsPopup = true
		},
		closeViews(){
			this.isViewsPopup = false
		},
		toggleViews(){
			this.isViewsPopup = !this.isViewsPopup
		},
		mouseEnterViews(){
			// this.viewsPopupTimeout = setTimeout(this.openViews, 1000)
		},
		mouseLeaveViews(){
			// clearTimeout(this.viewsPopupTimeout)
		},
		async onVote(data){
			if(this.isVoteProgress) return
			this.isVoteProgress = true
			const votes = Object.entries(data)
			try {
				await API.newsVote(this.currentPost.id, {
					votes: votes.map(([key, value]) => {
						return {
							question_id: key,
							answers_ids: Array.isArray(value) ? value : [value]
						}
					})
				})
				votes.forEach(([key, value]) => {
					const question = this.currentPost.questions.find(question => question.id === parseInt(key))
					const answers = question.answers.filter(answer => Array.isArray(value) ? value.includes(answer.id) : value === answer.id)
					answers.forEach(answer => {
						if(!answer.votes) answer.votes = []
						answer.votes.push({
							id: this.user.id,
							name: `${this.user.name} ${this.user.last_name}`,
							avatar: `/users_img/${this.user.img_url}`
						})
					})
				})
			}
			catch (error) {
				console.error(error)
			}
			this.isVoteProgress = false
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
.news-item__views{
	position: relative;
}

.PostComponent{
	.PopupMenu{
		margin-bottom: 10px;
		box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.15), 0px 15px 60px -40px rgba(45, 50, 90, 0.25);
		transition: all 0.3s;
	}
	&_hidden{
		margin-right: -20px;
		opacity: 0;
		visibility: hidden;
	}
	&-viewer{
		display: flex;
		align-items: center;
		justify-content: flex-start;
		gap: 10px;

		padding: 3px 10px;

		white-space: nowrap;

		.JobtronAvatar{
			flex: 0 0 24px;
		}
	}
	&-header{
		margin-bottom: 20px;
	}
}
</style>
