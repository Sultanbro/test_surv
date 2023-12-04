<template>
	<div
		class="NewsPostHeaderMobile"
		:class="{
			'NewsPostHeaderMobile_favorite': post.is_favourite,
			'NewsPostHeaderMobile_pinned': post.is_pinned,
		}"
	>
		<div class="NewsPostHeaderMobile-header">
			<JobtronAvatar
				:image="author.avatar"
				:title="author.name"
			/>
			<div class="NewsPostHeaderMobile-name">
				{{ author.name }}
			</div>
			<div class="NewsPostHeaderMobile-actions">
				<div class="NewsPostHeaderMobile-menu">
					<img
						class="NewsPostHeaderMobile-menuIcon NewsPostHeaderMobile-icon"
						src="/icon/news/post-actions/menu.svg"
						alt="img"
						@click.stop.prevent="onClickMenu"
					>
					<div
						v-if="isPopup"
						v-click-outside="onClickOutside"
						class="NewsPostHeaderMobile-menuBody"
					>
						<div
							class="NewsPostHeaderMobile-menuItem"
							@click="$emit('favorite')"
						>
							<img
								class="NewsPostHeaderMobile-menuIcon"
								:class="{
									'NewsPostHeaderMobile-menuIcon_active': post.is_favourite
								}"
								alt="img"
								src="/icon/news/news-popup/favorite.svg"
							>
							{{ post.is_favourite ? 'Удалить из избранного' :'Добавить в избранное' }}
						</div>
						<div
							class="NewsPostHeaderMobile-menuItem"
							@click="$emit('copy-link')"
						>
							<img
								class="NewsPostHeaderMobile-menuIcon"
								alt="img"
								src="/icon/news/news-popup/copy-link.svg"
							>
							Скопировать ссылку
						</div>
						<div
							v-if="canEdit"
							class="NewsPostHeaderMobile-menuItem"
							@click="$emit('edit')"
						>
							<img
								class="NewsPostHeaderMobile-menuIcon"
								alt="img"
								src="/icon/news/news-popup/edit.svg"
							>
							Редактировать
						</div>
						<div
							v-if="canEdit"
							class="NewsPostHeaderMobile-menuItem"
							@click="$emit('delete')"
						>
							<img
								class="NewsPostHeaderMobile-menuIcon"
								alt="img"
								src="/icon/news/news-popup/delete.svg"
							>
							Удалить
						</div>
					</div>
				</div>
				<div class="NewsPostHeaderMobile-pin">
					<img
						:src="post.is_pinned == true ? '/icon/news/post-actions/pinned.svg' : '/icon/news/post-actions/pin.svg'"
						class="NewsPostHeaderMobile-icon"
						@click="$emit('toggle-pinned')"
					>
				</div>
			</div>
		</div>
		<div class="NewsPostHeaderMobile-meta">
			<div class="NewsPostHeaderMobile-date">
				{{ createdAt }}
			</div>
			<div
				:title="access"
				class="NewsPostHeaderMobile-access"
			>
				<img
					src="/icon/news/some-icons/arrow-right.svg"
					class="NewsPostHeaderMobile-chevron mr-2"
				>
				{{ access }}
			</div>
		</div>
	</div>
</template>

<script>
import { pluralForm } from '@/composables/pluralForm.js'

import JobtronAvatar from '@ui/Avatar'

export default {
	name: 'NewsPostHeaderMobile',
	components: {
		JobtronAvatar,
	},
	props: {
		post: {
			type: Object,
			required: true,
		},
		canEdit: {
			type: Boolean
		},
	},
	data(){
		return {
			isPopup: false,
		}
	},
	computed: {
		author(){
			return this.post.author
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
		access(){
			if(!this.post.available_for) return 'Всем пользователям'
			return this.post.available_for.map(entry => entry.name).join(', ')
		},
	},
	watch: {},
	created(){},
	mounted(){},
	methods: {
		onClickMenu(){
			const currentState = this.isPopup
			setTimeout(() => {
				this.isPopup = !currentState
			}, 100)
		},
		onClickOutside(){
			this.isPopup = false
		},
	},
}
</script>

<style lang="scss">
.NewsPostHeaderMobile{
	display: flex;
	flex-flow: column nowrap;
	gap: 10px;

	font-family: "Inter", sans-serif;
	font-style: normal;

	&-header{
		display: flex;
		align-items: center;
		gap: 10px;
	}
	&-name{
		flex: 1;

		font-weight: 600;
		font-size: 15px;
		line-height: 20px;
		letter-spacing: -0.02em;
		color: #156AE8;

		overflow: hidden;
		text-overflow: ellipsis;
	}
	&-menu{
		position: relative;
	}
	&-menuBody{
		padding: 12px 0;
		margin-top: 7px;

		position: absolute;
		z-index: 11;
		top: 100%;
		right: -30px;

		background-color: #FFFFFF;
		box-shadow: 0 0 3px rgba(0, 0, 0, 0.05), 0 15px 60px rgba(45, 50, 90, 0.2);
		border-radius: 10px;

		&:before{
			content: '';
			height: 15px;
			width: 15px;

			position: absolute;
			top: -5px;
			right: 33px;

			background-color: #fff;
			transform: rotate(-45deg);
		}
	}
	&-menuItem{
		display: flex;
		align-items: center;
		gap: 10px;
		padding: 4px 20px;

		white-space: nowrap;
		cursor: pointer;
		transition: all 0.3s ease;
		&:hover{
			color: #156AE8;
			.NewsPostHeaderMobile{
				&-menuIcon{
					transition: all 0.3s ease;
					filter: invert(46%) sepia(54%) saturate(7424%) hue-rotate(206deg) brightness(91%) contrast(99%);
				}
			}
		}
	}
	&-menuIcon{
		transition: all 0.3s ease;
		filter: invert(82%) sepia(21%) saturate(340%) hue-rotate(181deg) brightness(98%) contrast(93%);
		&_active{
			filter: invert(46%) sepia(54%) saturate(7424%) hue-rotate(206deg) brightness(91%) contrast(99%);
		}
	}
	&-icon{
		filter: invert(99%) sepia(1%) saturate(1439%) hue-rotate(238deg) brightness(107%) contrast(69%);
		cursor: pointer;
		&:hover{
			filter: invert(27%) sepia(73%) saturate(2928%) hue-rotate(209deg) brightness(96%) contrast(89%);
		}
	}
	&-meta{
		display: flex;
		gap: 10px;
	}
	&-date{
		font-weight: 400;
		font-size: 12px;
		line-height: 20px;
		letter-spacing: -0.01em;
		color: #C1C9D0;
	}
	// &-menuIcon{}
	&-access{
		flex: 1;

		font-weight: 400;
		font-size: 14px;
		line-height: 20px;
		letter-spacing: -0.02em;
		color: rgba(166, 183, 212, 0.8);

		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}
	&-chevron{
		vertical-align: middle;
	}
	&-actions{
		display: flex;
		flex-flow: row nowrap;
		gap: 10px;
	}
	&-popup{
		padding: 12px 0;

		position: absolute;
		z-index: 11;
		top: 30px;
		right: -10px;

		background-color: #FFFFFF;
		box-shadow: 0 0 3px rgba(0, 0, 0, 0.05), 0 15px 60px rgba(45, 50, 90, 0.2);
		border-radius: 10px;
	}
	// &-pin{}
}
</style>
