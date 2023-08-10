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
								v-html="'Удалить'"
							/>
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
								v-html="'Удалить'"
							/>
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
export default {
	name: 'CommentsComponent',
	components: {
		ReactionComponent,
	},
	props: {
		me: {
			required: true
		}
	},
	data() {
		return {

			comments: [],
			comments_count: 0,
			postId: null,
		}
	},

	methods: {


		async getComments(postId) {
			this.postId = postId;

			await this.axios.get('/news/' + postId + '/comments')
				.then(response => {
					this.comments = response.data.data.comments;
					this.comments_count = response.data.data.comments_count;

					this.$emit('changeCommentsCount', {
						comments_count: this.comments_count,
					});
				})
				.catch(() => {
				});
		},

		async likeComment(commentId) {
			await this.axios.post('news/' + this.postId + '/comments/' + commentId + '/like')
				.then(response => {
					this.changeLikeComment(commentId, response.data.data)
				})
				.catch(() => {
				});
		},

		sendData(parentId, name) {
			this.$emit('send', {
				parentId: parentId,
				userName: name
			})
		},

		async destroyComment(commentId) {
			await this.axios.delete('news/' + this.postId + '/comments/' + commentId)
				.then(() => {
					this.getComments(this.postId);
				})
				.catch(res => {
					console.error(res);
				});
		},

		changeLikeComment(searchId, data) {
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
		}
	}
}
</script>
