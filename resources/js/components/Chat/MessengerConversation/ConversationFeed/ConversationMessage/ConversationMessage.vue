<template>
	<div
		class="ConversationMessage"
		:class="[
			ownMessage
				? 'messenger__message-box-right'
				: 'messenger__message-box-left',
			{
				'ConversationMessage_userFirst': helper && helper.isUserFirst,
				'ConversationMessage_userLast': helper && helper.isUserLast,
				'ConversationMessage_textSelected': selectedBox,
			}
		]"
	>
		<JobtronAvatar
			v-if="!ownMessage && !chat.private"
			:title="`${message.sender.name} ${message.sender.last_name}`"
			:image="'/users_img/' + message.sender.img_url"
			:size="50"
			class="ConversationMessage-avatar"
		/>
		<div class="messenger__message-container">
			<div :class="messageCardClass">
				<div
					v-if="!chat.private && !ownMessage"
					class="ConversationMessage-name"
					:style="`color: #${nameColor}`"
				>
					{{ name }}
				</div>
				<div
					class="messenger__format-message-wrapper"
					@mouseup.stop="onTextSelect"
				>
					<!-- eslint-disable -->
					<div
						v-if="message.parent"
						class="messenger__format-container_parent"
						@click="goto(message.parent, $event)"
					>
						<div class="messenger__format-container_parent-author">
							{{ message.parent.sender.name }}
						</div>
						<div
							v-html="parentBody"
							class="messenger__format-container_parent-message"
						/>
					</div>

					<div
						v-html="messageBody"
						class="messenger__format-container"
						@click.capture="onClickMessage"
					/>
					<!-- eslint-enable -->
					<ConversationMessageGallery
						v-if="isGallery"
						:files="message.files"
						@loadImage="$emit('loadImage')"
					/>
					<!-- <div
						v-if="isGallery"
						class="messenger__message-files messenger__message-files_group"
					>
						<div
							v-for="(file, key) in message.files.slice(0, 3)"
							:key="key"
							:class="{
								'messenger__message-file_group': true,
								'messenger__last-row': key === 2 && message.files.length > 2,
								'messenger__last-column': (key === 1 && message.files.length > 2) || (key === 2 && message.files.length > 1)
							}"
						>
							<div
								@click="openImage(file)"
								class="messenger__message-file-image"
							>
								<img
									@load="$emit('loadImage')"
									:src="file.thumbnail_path ? file.thumbnail_path : file.file_path"
									alt="file.name"
								>
								<div
									v-if="message.files.length > 3 && key === 2"
									class="messenger__message-files_group-count"
								>
									<span>+{{ message.files.length - 3 }}</span>
								</div>
							</div>
						</div>
					</div> -->
					<div
						v-else
						class="messenger__message-files"
					>
						<div
							v-for="(file, index) in message.files"
							:key="index"
							class="messenger__message-file"
						>
							<template v-if="isImage(file)">
								<div
									class="messenger__message-file-image"
									@click="openImage(file)"
								>
									<img
										:src="file.thumbnail_path ? file.thumbnail_path : file.file_path"
										alt="file.name"
										@load="$emit('loadImage')"
									>
								</div>
							</template>
							<template v-else-if="isAudio(file)">
								<div class="messenger__message-file-audio">
									<VoiceMessage
										:audio-source="file.file_path"
										:is-active="active"
										@play="$emit('active')"
									/>
								</div>
							</template>
							<template v-else>
								<div class="messenger__message-file-icon">
									<svg
										xmlns="http://www.w3.org/2000/svg"
										viewBox="0 0 384 512"
										width="25"
										height="25"
									>
										<path
											d="M0 64C0 28.65 28.65 0 64 0H229.5C246.5 0 262.7 6.743 274.7 18.75L365.3 109.3C377.3 121.3 384 137.5 384 154.5V448C384 483.3 355.3 512 320 512H64C28.65 512 0 483.3 0 448V64zM336 448V160H256C238.3 160 224 145.7 224 128V48H64C55.16 48 48 55.16 48 64V448C48 456.8 55.16 464 64 464H320C328.8 464 336 456.8 336 448z"
										/>
									</svg>
								</div>
								<div class="messenger__message-file-name">
									<a
										:href="file.file_path"
										download
										target="_blank"
									>{{ file.name }}</a>
								</div>
							</template>
						</div>
					</div>
				</div>
				<ConversationMessageMeta
					:readers="message.readers || []"
					:time="time"
					:own="ownMessage"
					:reactions="reactions"
					@reaction-click="reactMessage({message: message, emoji_id: $event})"
				/>
				<i
					v-click-outside="onClickOutsideQuote"
					class="ConversationMessage-quoteButton fa fa-chevron-right"
					:style="selectedBox || ''"
					@click.stop="onClickQuote"
				/>
			</div>

			<div
				class="ConversationMessage-context"
				@click.stop="$emit('contextbutton', $event)"
			>
				<i class="fa fa-chevron-down mt-1" />
			</div>

			<!-- <div class="messenger__text-timestamp">
				<span>{{ message.created_at | moment }}</span>
			</div> -->
		</div>
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import moment from 'moment';
// import MessageReaders from './MessageReaders/MessageReaders.vue';
// import AlternativeAvatar from '../../../ChatsList/ContactItem/AlternativeAvatar/AlternativeAvatar.vue';
import VoiceMessage from './VoiceMessage/VoiceMessage.vue'
import ConversationMessageMeta from './ConversationMessageMeta.vue'
import ConversationMessageGallery from './ConversationMessageGallery'
import JobtronAvatar from '@ui/Avatar'
import { stringToColor } from '@/composables/stringToColor'

const linkRegExp = /https?:\/\/[^\s]*|\[\s*?https?:\/\/[^\s]*\s*?\|[^\]]*\]/g;
const mentionRegExp = /@([A-Za-z0-9_А-я\s]+)#(\d+);/gi

export default {
	name: 'ConversationMessage',
	components: {
		// MessageReaders,
		// AlternativeAvatar,
		VoiceMessage,
		ConversationMessageMeta,
		ConversationMessageGallery,
		JobtronAvatar,
	},
	filters: {
		moment: function (date) {
			// if today show only hour and minutes
			if (moment(date).isSame(moment(), 'day')) {
				return moment(date).format('HH:mm');
			}
			// if yesterday show only hour and minutes and yesterday
			if (moment(date).isSame(moment().subtract(1, 'day'), 'day')) {
				return 'Вчера, ' + moment(date).format('HH:mm');
			}
			// if older than yesterday show hour, minutes, day and month
			return moment(date).format('DD.MM, HH:mm');
		},
	},
	inject: [
		'ChatApp'
	],
	props: {
		message: {
			type: Object,
			required: true
		},
		last: {
			type: Boolean,
			default: false
		},
		active: {
			type: Boolean,
			default: false
		},
		helper: {
			type: Object,
			default: null
		}
	},
	data(){
		return {
			selectedBox: null
		}
	},
	computed: {
		...mapGetters(['user', 'users', 'chat']),
		messageCardClass() {
			return {
				'messenger__message-card': true,
				'messenger__message__failed': this.message.failed,
			}
		},
		reactions() {
			// go through each reader and if include reaction type
			// add to reactions array
			const reactions = []
			this.message.readers?.forEach(reader => {
				if (reader.pivot && reader.pivot.reaction) {
					// increment reaction count if already in array
					let reaction = reactions.find(reaction => reaction.type === reader.pivot.reaction);
					if (reaction) {
						reaction.count++;
					} else {
						reactions.push({
							type: reader.pivot.reaction,
							count: 1,
						});
					}
				}
			});
			return reactions;
		},
		ownMessage(){
			return this.message.sender_id === this.user.id
		},
		countReaders(){
			if(!this.message.readers) return 0
			return this.message.readers.length
		},
		time(){
			return this.$moment(this.message.created_at).format('HH:mm')
		},
		messageBody() {
			const { body } = this.message;
			if (typeof body != 'string') return ''
			return body
				.split('\n')
				.map(line => line[0] === '>' ? `<div class="ConversationMessage-quote">${line.slice(1).trim()}</div>` : line + '\n')
				.join('')
				.trim()
				.replace(linkRegExp, '<a href="$&" target="_blank" class="ConversationMessage-link">$&</a>')
				.replace(mentionRegExp, (match, p1, p2) => `<a href="javascript:void(0)" data-user="${p2}" class="ConversationMessage-mention">${p1}${this.getUserTooltip(+p2)}</a>`)
		},
		parent(){
			return this.message.parent
		},
		parentBody(){
			if(!this.parent) return ''
			if(typeof this.parent.body !== 'string') return ''
			return this.parent.body.replace(mentionRegExp, (match, p1) => `<a href="javascript:void(0)" class="ConversationMessage-mention">${p1}</a>`)
		},
		name() {
			return `${this.message.sender.name} ${this.message.sender.last_name}`
		},
		nameColor() {
			return stringToColor(this.name)
		},
		isGallery() {
			return this.message.files && this.message.files.length > 1 && this.message.files.every(file => this.isImage(file));
		}
	},
	methods: {
		...mapActions([
			'showGallery',
			'reactMessage',
			'loadMessages',
			'loadMoreNewMessages',
			'requestScroll',
			'setLoading',
			'loadChat',
		]),
		isImage(file) {
			const ext = file.name.split('.').pop();
			return ['jpg', 'jpeg', 'png', 'gif'].includes(ext);//todo: другой способ определения. Хранить тип файла в БД.
		},
		isAudio(file) {
			const ext = file.name.split('.').pop();
			return ['mp3', 'wav', 'ogg', 'webm'].includes(ext);
		},

		getImages() {
			return this.message.files.filter(file => this.isImage(file)).map(file => file.file_path);
		},
		openImage(image) {
			this.showGallery({
				images: this.getImages(),
				index: this.message.files.findIndex(f => f.id === image.id),
			});
		},
		goto(message, event) {
			event.stopPropagation();
			this.setLoading(true);
			this.loadMessages({
				reset: false, goto: message.id, callback: () => {
					// after a second
					setTimeout(() => {
						this.setLoading(false);
					}, 1000);
				}
			});
		},
		trim(value){
			return ('' + value).trim()
		},
		getUserTooltip(userId){
			const user = this.users.find(user => user.id === userId)
			if(!user) return ''
			return `<span class="ConversationMessage-mentionTooltip"><img width="20" src="/users_img/${user.img_url}" class="radius-full"> ${user.name} ${user.last_name}</span>`
		},
		onTextSelect(event){
			setTimeout(() => {
				if(!window.getSelection) return
				const selection = window.getSelection()
				if(!selection){
					this.selectedBox = null
					return console.error('no selection')
				}
				const range = selection.getRangeAt(0)
				if(!range.toString()){
					this.selectedBox = null
					return console.error('no text')
				}
				const card = range.startContainer?.parentElement?.offsetParent
				if(!card) return console.error('no card')
				const cardRect = card.getBoundingClientRect()
				this.selectedBox = [
					`top: ${event.clientY - cardRect.top - 4 }px`,
					`left: ${event.clientX - cardRect.left + 10 }px`,
				].join(';')
			}, 100)
		},
		onClickOutsideQuote(){
			this.selectedBox = null
		},
		onClickQuote(){
			if(!window.getSelection){
				this.selectedBox = null
				return
			}

			const selection = window.getSelection()
			if(selection){
				const text = selection.getRangeAt(0).toString()
				this.ChatApp.$emit('addQuote', text)
			}

			this.selectedBox = null
		},
		onClickMessage(event){
			const userId = event.target.getAttribute('data-user')
			if(!userId) return
			this.loadChat({chatId: 'user' + userId})
		},
	},
}
</script>

<style lang="scss">
$ConversationMessage-radius: 18px;
.ConversationMessage{
	gap: 10px;
	position: relative;
	&_textSelected{
		.ConversationMessage-quoteButton{
			display: flex;
		}
	}
	&_userFirst{
		&.messenger__message-box-left{
			.messenger__message-card{
				border-top-left-radius: $ConversationMessage-radius;
			}
		}
		&.messenger__message-box-right{
			.messenger__message-card{
				border-top-right-radius: $ConversationMessage-radius;
			}
		}
	}
	&_userLast{
		&.messenger__message-box-left{
			.messenger__message-card{
				border-bottom-left-radius: $ConversationMessage-radius;
			}
		}
		&.messenger__message-box-right{
			.messenger__message-card{
				border-bottom-right-radius: $ConversationMessage-radius;
			}
		}
	}
	&-avatar{
		flex: 0 0 auto;
	}
	&-name{
		margin-bottom: 8px;

		font-weight: 600;
		font-size: 14px;
		line-height: 16px;
		letter-spacing: -0.015em;
	}
	&-context{
		display: none;
		align-items: center;
		justify-content: center;

		width: 16px;
		height: 16px;
		border-radius: 16px;

		position: absolute;
		top: 4px;
		right: -24px;

		font-size: 12px;
		color: #fff;
		background-color: #6986B8;
		cursor: pointer;
		&:hover{
			background-color: #3361FF;
		}
	}
	&-link{
		color: #007bff;
		&:hover{
			color: #007bff;
			text-decoration: underline;
		}
	}
	&-mentionTooltip{
		display: none;
		width: fit-content;
		padding: 5px 10px;
		margin-bottom: 10px;
		border: 1px solid #eee;

		position: absolute;
		z-index: 10;
		bottom: 100%;
		right: 0;

		color: #333;
		white-space: nowrap;

		background-color: #fff;
		box-shadow: 0px 10px 30px rgba(38, 51, 77, 0.3);
		border-radius: 16px;
	}
	&-mention{
		position: relative;
		color: #007bff;
		cursor: pointer;
		&:hover{
			color: #007bff;
			text-decoration: underline;
			.ConversationMessage{
				&-mentionTooltip{
					display: block;
				}
			}
		}
	}
	&-quote{
		padding-left: 0.5rem;
		border-left: 2px solid #828c94;
	}
	&-quoteButton{
		display: none;
		align-items: center;
		justify-content: center;

		width: 16px;
		height: 16px;
		position: absolute;

		color: #fff;
		border-radius: 50em;
		background-color: #8BABD8;
		box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.05), 0px 15px 60px -40px rgba(45, 50, 90, 0.2);
		outline: 1px solid #fff;
		user-select: none;
		cursor: pointer;
		&:hover{
			background-color: #3361FF;
		}
	}

	&:hover{
		.ConversationMessage-context{
			display: flex;
		}
	}
}

/*noinspection CssUnusedSymbol*/
.messenger__message-box-right,
.messenger__message-box-left {
	display: flex;
	flex: 0 0 50%;
	align-items: flex-end;
	line-height: 1.4;
}

/*noinspection CssUnusedSymbol*/
.messenger__message-box-left {
	justify-content: flex-start;
	margin-left: 20px;
	.messenger__message-card {
		background: #FFFFFF;
		box-shadow: 0px 10px 30px rgba(38, 51, 77, 0.03);
		border-top-right-radius: $ConversationMessage-radius;
		border-bottom-right-radius: $ConversationMessage-radius;
	}
}

/*noinspection CssUnusedSymbol*/
.messenger__message-box-right {
	justify-content: flex-end;
	margin-right: 20px;
	.messenger__message-card{
		background: #EDF6FF;
		border-top-left-radius: $ConversationMessage-radius;
		border-bottom-left-radius: $ConversationMessage-radius;
	}
}

.messenger__message-container {
	position: relative;
	padding: 2px 0;
	min-width: 75px;
	box-sizing: content-box;
	display: flex;
	flex-wrap: wrap;
	flex-direction: column;
	align-content: stretch;
	align-items: stretch;
	margin-top: 4px;
}

/*noinspection CssUnusedSymbol*/
.messenger__message-card {
	position: relative;
	font-weight: 500;
	font-size: 14px;
	line-height: 17px;
	padding: 12px 24px;
	max-width: 560px;
	-webkit-transition-property: box-shadow, opacity;
	transition-property: box-shadow, opacity;
	transition: box-shadow .28s cubic-bezier(.4, 0, .2, 1);
	will-change: box-shadow;
	color: #152136;
	letter-spacing: -0.02em;
}

.messenger__format-message-wrapper{
	display: inline;
}
.messenger__format-container{
	display: inline;
}

/*noinspection CssUnusedSymbol*/
.messenger__message__failed {
	background-color: #ffcdd2 !important;
}

/*noinspection CssUnusedSymbol*/
.messenger__message-box-right .messenger__message-card {
	float: right;
}

/*noinspection CssUnusedSymbol*/
.messenger__message-box-left .messenger__message-card {
	float: left;
}

.messenger__text-timestamp {
	font-size: 10px;
	line-height: 10px;
	margin-top: 10px;
	color: #828c94;
	text-align: right;
}

.messenger__message-box-right .messenger__text-timestamp {
	text-align: left;
}

.messenger__message-files {
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	&:empty{
		display: none;
	}
}

.messenger__message-file {
	display: flex;
	align-items: center;
	margin: 5px;
	max-width: 100%;
}

.messenger__message-file-icon {
	font-size: 20px;
	margin-right: 5px;
}

.messenger__message-file-name {
	font-size: 14px;
	max-width: 100%;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}

.messenger__message-file-name a {
	color: #001da1;
}

.messenger__message-file-name a:hover {
	text-decoration: underline;
}

.messenger__message-file-name a:visited {
	color: rgba(0, 9, 72, 0.94);
}

.messenger__message-file-image {
	cursor: pointer;
}

.messenger__message-file-image img {
	max-width: 100%;
}

audio {
	max-width: 230px;
}

.messenger__message-files_group {
	flex-wrap: wrap;
	width: 210px;
	height: 160px;
	align-content: space-around;
}

.messenger__message-files_group-count {
	position: relative;
	top: -75px;
	height: 100%;
	width: 100%;
	color: white;
	background: rgba(0, 0, 0, 0.5);
}

.messenger__message-files_group-count span {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	-ms-transform: translate(-50%, -50%);
}

.messenger__message-file_group {
	width: 100px;
	height: 155px;
	margin-bottom: 5px;
}

.messenger__message-file_group .messenger__message-file-image {
	height: 100%;
}

.messenger__message-file_group .messenger__message-file-image img {
	height: 100%;
}

.messenger__last-row {
	width: 100px;
	height: 75px;
}

.messenger__last-column {
	width: 100px;
	height: 75px;
}

.messenger__format-container{
	white-space: pre-line;
	line-break: anywhere;
}
.messenger__format-container_parent {
	border-left: 2px solid #5ebee9;
	cursor: pointer;
	padding: 2px 10px;
}

.messenger__format-container_parent-author {
	font-weight: bold;
}

.messenger__format-container_parent-message {
	overflow: hidden;
	white-space: nowrap;
	text-overflow: ellipsis;
}

.messenger__format-link:hover{
	color: #0056b3;
	text-decoration: underline;
}

.messenger__message-card{

}

@media only screen and (max-width: 670px) {
	.ConversationMessage{
		&-context{}
		&:hover{
			.ConversationMessage-context{
				display: none !important;
			}
		}
	}
	.messenger__message-card{
		max-width: 100%;
	}
	.messenger__message-box-right{
		margin-right: 0;
	}
	.messenger__message-box-left{
		margin-left: 0;
	}
}
</style>
