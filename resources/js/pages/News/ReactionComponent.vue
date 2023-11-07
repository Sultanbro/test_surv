<template>
	<div class="news-comment-reactions">
		<div
			v-for="(reaction, index) in reactionsList"
			v-show="reaction.value != 0"
			:key="index"
			:class="'news-comment-reactions__item '
				+ (reaction.is_reacted ? 'news-comment-reactions__item--reacted' : '')"
			@click="sendReaction(reaction.icon)"
		>
			<!-- eslint-disable-next-line -->
			<span v-html="reaction.value" />
			<!-- eslint-disable-next-line -->
			<span v-html="reaction.icon" />
		</div>

		<div
			v-show="hasReactionWithZero()"
			class="news-comment-reactions__keyboard-container"
		>
			<span
				class="news-comment-reactions__show-keyboard"
				@click="toggleEmojiKeyboard(true)"
			>+</span>
			<div
				v-show="showKeyboard"
				class="news-emoji-keyboard"
			>
				<div class="news-emoji-keyboard__container">
					<div class="news-emoji-keyboard__arrow" />
					<div class="news-emoji-keyboard__items">
						<!-- eslint-disable -->
						<span
							v-for="reaction in reactionsList"
							:key="reaction.icon"
							class="news-emoji-keyboard__item"
							@click="sendReaction(reaction.icon)"
							v-html="reaction.icon"
						/>
						<!-- eslint-enable -->
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
/* eslint-disable camelcase */

import {reactions} from './helper.js'
import * as API from '@/stores/api/news.js'

export default {
	name: 'ReactionComponent',
	props: {
		articleId: {
			type: Number,
			required: true,
		},
		commentId: {
			type: Number,
			required: true
		},
		reactions: {
			type: Array,
			required: true
		},
	},
	data() {
		return {
			showKeyboard: false,

			commentReactions: this.reactions,

			reactionsList: reactions,

			zeroList: [],
		}
	},
	mounted() {
		this.zeroList = JSON.parse(JSON.stringify(this.reactionsList));
		this.updateReactionList();
	},
	methods: {
		updateReactionList() {
			this.reactionsList = JSON.parse(JSON.stringify(this.zeroList));

			this.commentReactions.forEach(el => {
				const item = this.reactionsList.filter(item => {
					return (el.reaction == item.icon);
				})[0];
				item.value = el.reaction_count;
				item.is_reacted = el.is_reacted;
			});
		},

		hasReactionWithZero() {
			return this.reactionsList.some(el => {
				return el.value === 0
			});
		},

		toggleEmojiKeyboard(newValue) {
			this.showKeyboard = newValue;
			this.$root.$emit('toggle-white-bg', newValue);
		},

		async sendReaction(reactionCode) {
			this.toggleEmojiKeyboard(false);

			const formData = new FormData();
			formData.append('reaction', reactionCode);

			let data
			try {
				data = await API.newsCommentsReaction(this.articleId, this.commentId, formData)
			}
			catch (error) {
				console.error(error)
				return
			}
			if(!data) return

			this.commentReactions = JSON.parse(JSON.stringify(data.reactions))
			this.updateReactionList()
		},
	}
}
</script>
