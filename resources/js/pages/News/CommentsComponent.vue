<template>
	<div class="news-item__comments">
		<div
			v-for="comment in comments"
			:key="comment.id + comment.content"
			class="news-comments"
		>
			<div class="news-comment">
				<img
					class="news-comment__avatar"
					:src="comment.author.avatar"
				>
				<div class="news-comment__content">
					<span class="news-comment__name">{{ comment.author.name }}</span>
					<span class="news-comment__text">{{ comment.content }}</span>
					<div class="news-comment__footer news-footer">
						<div class="news-footer__main">
							<div class="news-comment__action">
								{{ comment.created_at }}
							</div>
							<div
								class="news-comment__action hover-pointer"
								@click="sendData(comment.id, comment.author.name)"
							>
								Ответить
							</div>
							<div class="news-comment__action hover-pointer">
								Поделиться
							</div>
							<div
								v-show="comment.author.id == me.id"
								class="news-comment__action hover-pointer"
								@click="destroyComment(comment.id)"
							>
								Удалить
							</div>
						</div>
						<div class="news-footer__reactions">
							<img
								v-if="comment.is_liked == true"
								class="hover-pointer"
								src="/icon/news/post-actions/like-active.svg"
								@click="likeComment(comment.id)"
							>
							<img
								v-else
								class="news-icon hover-pointer"
								src="/icon/news/post-actions/like.svg"
								@click="likeComment(comment.id)"
							>
							<span class="news-item__footer-count">{{ comment.likes_count }}</span>
						</div>
					</div>

					<ReactionComponent
						:article-id="postId"
						:comment-id="comment.id"
						:reactions="comment.reactions"
					/>
				</div>
			</div>

			<div
				v-for="childComment in comment.comments"
				:key="childComment.id + childComment.content"
				class="news-comment news-comment--child"
			>
				<img
					class="news-comment__avatar"
					:src="childComment.author.avatar"
				>
				<div class="news-comment__content">
					<span class="news-comment__name">{{ childComment.author.name }}</span>
					<span class="news-comment__text">{{ childComment.content }}</span>
					<div class="news-comment__footer news-footer">
						<div class="news-footer__main">
							<div class="news-comment__action">
								{{ childComment.created_at }}
							</div>
							<div
								class="news-comment__action hover-pointer"
								@click="sendData(comment.id, childComment.author.name)"
							>
								Ответить
							</div>
							<div class="news-comment__action hover-pointer">
								Поделиться
							</div>
							<div
								v-show="childComment.author.id == me.id"
								class="news-comment__action hover-pointer"
								@click="destroyComment(childComment.id)"
							>
								Удалить
							</div>
						</div>
						<div class="news-footer__reactions">
							<img
								v-if="childComment.is_liked == true"
								class="hover-pointer"
								src="/icon/news/post-actions/like-active.svg"
								@click="likeComment(childComment.id)"
							>
							<img
								v-else
								class="news-icon hover-pointer"
								src="/icon/news/post-actions/like.svg"
								@click="likeComment(childComment.id)"
							>
							<span class="news-item__footer-count">{{ childComment.likes_count }}</span>
						</div>
					</div>

					<ReactionComponent
						:article-id="postId"
						:comment-id="childComment.id"
						:reactions="childComment.reactions"
					/>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import ReactionComponent from '@/pages/News/ReactionComponent'
import * as API from '@/stores/api/news.js'

export default {
	name: 'CommentsComponent',
	components: {
		ReactionComponent,
	},
	props: {
		me: {
			type: Object,
			required: true
		}
	},
	data() {
		return {
			comments: [],
			commentsCount: 0,
			postId: null,
		}
	},

	methods: {
		async getComments(postId) {
			this.postId = postId;

			let data
			try {
				data = await API.newsCommentsFetch(postId)
			}
			catch (error) {
				console.error(error)
				return
			}
			if(!data) return

			this.comments = data.comments;
			this.commentsCount = data.comments_count;

			this.$emit('changeCommentsCount', {
				/* eslint-disable-next-line camelcase */
				comments_count: this.commentsCount,
			})
		},

		async likeComment(commentId) {
			let data

			try {
				data = await API.newsCommentsLike(this.postId, commentId)
			}
			catch (error) {
				console.error(error)
				return
			}
			if(!data) return

			this.changeLikeComment(commentId, data)
		},

		sendData(parentId, name) {
			this.$emit('send', {
				parentId: parentId,
				userName: name
			})
		},

		async destroyComment(commentId) {
			try {
				await API.newsCommentsDelete(this.postId, commentId)
			}
			catch (error) {
				console.error(error)
				return
			}

			this.getComments(this.postId)
		},

		changeLikeComment(searchId, data) {
			/* eslint-disable camelcase */
			let comment = this.comments.find(comment => comment.id === searchId);

			if (comment != null) {
				comment.likes_count = data.likes_count;
				comment.is_liked = data.is_liked;
			}

			this.comments.forEach(comment => {
				comment.comments.forEach(childComment => {
					if (childComment.id == searchId) {
						childComment.likes_count = data.likes_count;
						childComment.is_liked = data.is_liked;
					}
				})
			});
			/* eslint-enable camelcase */
		}
	}
}
</script>
