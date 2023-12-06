<template>
	<div class="NewsComments">
		<div
			v-if="loading"
			class="NewsComments-loading"
		>
			<i class="fas fa-spinner fa-pulse" />
		</div>

		<div
			v-for="comment in comments"
			:key="comment.id"
			class="NewsComment"
		>
			<div class="NewsComment-body">
				<JobtronAvatar
					:image="comment.author.avatar"
					:title="comment.author.name"
					:size="40"
				/>
				<div class="NewsComment-content">
					<div class="NewsComment-name">
						{{ comment.author.name }}
					</div>
					<div class="NewsComment-text">
						{{ comment.content }}
					</div>
				</div>
			</div>
			<div class="NewsComment-footer">
				<div class="NewsComment-actions">
					<div class="NewsComment-date">
						{{ comment.created_at }}
					</div>
					<div
						class="NewsComment-action"
						@click="sendComment({
							parentId: comment.id,
							userName: comment.author.name,
						})"
					>
						Ответить
					</div>
					<div
						v-if="false"
						class="NewsComment-action"
					>
						Поделиться
					</div>
					<div
						v-if="comment.author.id === $laravel.userId"
						class="NewsComment-action"
						@click="destroyComment(comment.id)"
					>
						Удалить
					</div>
				</div>
				<div class="NewsComment-likes">
					<img
						:src="comment.is_liked ? '/icon/news/post-actions/like-active.svg' : '/icon/news/post-actions/like.svg'"
						class="NewsComment-likesIcon"
						:class="{
							'NewsComment-likesIcon_liked': comment.is_liked
						}"
						@click="likeComment(comment.id)"
					>
					<span class="NewsComment-likesCount">
						{{ comment.likes_count }}
					</span>
				</div>
			</div>
			<ReactionComponent
				:article-id="postId"
				:comment-id="comment.id"
				:reactions="comment.reactions"
			/>
			<div
				v-for="response in comment.comments"
				:key="response.id"
				class="NewsComment"
			>
				<div class="NewsComment-body">
					<JobtronAvatar
						:image="response.author.avatar"
						:title="response.author.name"
						:size="40"
					/>
					<div class="NewsComment-content">
						<div class="NewsComment-name">
							{{ response.author.name }}
						</div>
						<div class="NewsComment-text">
							{{ response.content }}
						</div>
					</div>
				</div>
				<div class="NewsComment-footer">
					<div class="NewsComment-actions">
						<div class="NewsComment-date">
							{{ response.created_at }}
						</div>
						<div
							class="NewsComment-action"
							@click="sendComment({
								parentId: response.id,
								userName: response.author.name,
							})"
						>
							Ответить
						</div>
						<div
							v-if="false"
							class="NewsComment-action"
						>
							Поделиться
						</div>
						<div
							v-if="response.author.id === $laravel.userId"
							class="NewsComment-action"
							@click="destroyComment(response.id)"
						>
							Удалить
						</div>
					</div>
					<div class="NewsComment-likes">
						<img
							:src="response.is_liked ? '/icon/news/post-actions/like-active.svg' : '/icon/news/post-actions/like.svg'"
							class="NewsComment-likesIcon"
							@click="likeComment(response.id)"
						>
						<span class="NewsComment-likesCount">
							{{ response.likes_count }}
						</span>
					</div>
				</div>
				<ReactionComponent
					:article-id="postId"
					:comment-id="response.id"
					:reactions="response.reactions"
				/>
			</div>
		</div>
	</div>
</template>

<script>
import * as API from '@/stores/api/news.js'

import JobtronAvatar from '@ui/Avatar'
import ReactionComponent from '@/pages/News/ReactionComponent'

export default {
	name: 'NewsComments',
	components: {
		JobtronAvatar,
		ReactionComponent,
	},
	props: {
		postId: {
			type: Number,
			default: 0
		}
	},
	data(){
		return {
			comments: [],
			loading: true,
		}
	},
	computed: {},
	watch: {},
	created(){},
	mounted(){
		this.fetchComments()
	},
	methods: {
		async fetchComments() {
			this.loading = true

			let data
			try {
				data = await API.newsCommentsFetch(this.postId)
			}
			catch (error) {
				console.error(error)
				this.loading = false
				return
			}
			this.loading = false
			if(!data) return

			this.comments = data.comments

			this.$emit('changeCommentsCount', {
				/* eslint-disable-next-line camelcase */
				comments_count: data.comments_count,
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

			const comment = this.comments.find(comment => comment.id === commentId)
			const countProp = 'likes_count'
			const stateProp = 'is_liked'

			if(comment) {
				comment[countProp] = data.likes_count
				comment[stateProp] = data.is_liked
				return
			}

			this.comments.forEach(parent => {
				parent.comments.forEach(comment => {
					if (comment.id === commentId) {
						comment[countProp] = data.likes_count
						comment[stateProp] = data.is_liked
					}
				})
			})
		},
		async destroyComment(commentId) {
			if(!confirm('Удалить комментарий?')) return

			try {
				await API.newsCommentsDelete(this.postId, commentId)
			}
			catch (error) {
				console.error(error)
				return
			}

			this.fetchComments()
		},
		sendComment(request){
			this.$emit('send', request)
		},
	},
}
</script>

<style lang="scss">
.NewsComments{
	&-loading{
		display: flex;
		justify-content: center;
		align-items: center;
	}
}
.NewsComment{
	display: flex;
  flex-flow: column nowrap;
  gap: 5px;

	padding: 20px 0;
	border-top: 1px solid #EBEBF9;
	font-family: "Inter", sans-serif;
	font-style: normal;


	&-body{
		display: flex;
		flex-flow: row nowrap;
		gap: 20px;
	}
	&-content{
		display: flex;
		flex-direction: column;
		gap: 5px;
		width: 100%;
	}
	&-name{
		color: #156AE8;
		font-size: 15px;
		line-height: 20px;
		font-weight: 600;
		letter-spacing: -0.02em;
	}
	&-text{
		font-weight: 500;
		font-size: 14px;
		line-height: 26px;
		letter-spacing: -0.01em;
		color: #4A5568;
	}

	&-footer{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		justify-content: space-between;
		gap: 10px;

		padding-top: 15px;
	}
	&-actions{
		display: flex;
		gap: 10px;
		align-items: center;
	}
	&-date{
		font-weight: 500;
		font-size: 12px;
		line-height: 14px;
		letter-spacing: -0.03em;
		color: #C1C9D0;
	}
	&-action{
		font-weight: 500;
		font-size: 12px;
		line-height: 14px;
		letter-spacing: -0.03em;
		color: #4A5568;
		cursor: pointer;
		&:hover{
			text-decoration: underline;
		}
	}
	&-likesIcon{
		filter: invert(99%) sepia(1%) saturate(1439%) hue-rotate(238deg) brightness(107%) contrast(69%);
		cursor: pointer;
		&:hover{
			filter: invert(27%) sepia(73%) saturate(2928%) hue-rotate(209deg) brightness(96%) contrast(89%);
		}
		&_liked{
			filter: none;
		}
	}
	.NewsComment{
		margin-left: 20px;

		&:last-of-type{
			padding-bottom: 0;
		}
	}
}

@media (min-width: 768px) {
	.NewsComment{
		.NewsComment{
			margin-left: 60px;
		}
		&-actions{
			gap: 20px;
		}
	}
}
</style>
