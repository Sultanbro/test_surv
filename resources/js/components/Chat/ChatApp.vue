<template>
	<div
		class="messenger__wrapper"
		v-show="isOpen"
		@click.self="toggle"
	>
		<div
			@keydown.esc="escapeChat"
			id="messengerWindow"
			class="messenger__card-window"
		>
			<div class="messenger__chat-container">
				<ChatNav :fullscreen="true" />
				<MessengerConversation v-if="isDesktop || isChatSelected" />
			</div>

			<JobtronOverlay
				v-if="isInfoPanel && isChatSelected"
				@close="toggleInfoPanel"
			>
				<ChatInfo />
			</JobtronOverlay>

			<JobtronOverlay
				v-if="isAddUserDialog && isChatSelected"
				@close="toggleAddUserDialog"
			>
				<ChatUserAdd />
			</JobtronOverlay>

			<JobtronOverlay
				v-if="isNewChatDialog"
				@close="toggleNewChatDialog"
			>
				<ChatNewChat />
			</JobtronOverlay>

			<ConfirmDialog />
			<ChatIconsDemo
				v-if="isDemoOpen"
				@close="isDemoOpen = false"
			/>
		</div>
		<ImageGallery
			id="messenger_gallery"
			:images="galleryImages"
			:index="galleryIndex"
			@onopen="openGallery"
			@close="hideGallery"
		/>
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import ChatNav from './ChatNav/ChatNav.vue';
import MessengerConversation from './MessengerConversation/MessengerConversation.vue';
// import InfoPanel from './InfoPanel/InfoPanel';
import ChatInfo from './ChatInfo/ChatInfo.vue'
import ChatUserAdd from './ChatInfo/ChatUserAdd.vue'
import ChatNewChat from './ChatNewChat/ChatNewChat'
import clickOutside from './directives/clickOutside.ts';
import ImageGallery from './ImageGallery/ImageGallery.vue';
import ConfirmDialog from './ConfirmDialog/ConfirmDialog.vue';
import ChatIconsDemo from '@icons/ChatIconsDemo.vue'
import JobtronOverlay from '@ui/Overlay'

// noinspection JSUnusedGlobalSymbols
export default {
	name: 'ChatApp',
	components: {
		ChatNav,
		MessengerConversation,
		ChatInfo,
		ChatUserAdd,
		ChatNewChat,
		ImageGallery,
		ConfirmDialog,
		ChatIconsDemo,
		JobtronOverlay,
	},
	directives: {
		clickOutside
	},
	provide(){
		return {
			ChatApp: this
		}
	},
	data() {
		return {
			galleryOpened: false,
			isDemoOpen: false,
		};
	},
	computed: {
		...mapGetters([
			'isInitialized',
			'user',
			'isOpen',
			'galleryImages',
			'galleryIndex',
			'isChatSearchMode',
			'chat',
			'isInfoPanel',
			'isAddUserDialog',
			'isNewChatDialog',
		]),
		isDesktop() {
			return this.$viewportSize.width > 670
		},
		isChatSelected(){
			return !!this.chat
		}
	},
	watch: {
		isOpen: function (val) {
			if (val) {
				// set div messenger__open class
				document.body.classList.add('messenger__open');
			}
			else {
				// remove div messenger__open class
				document.body.classList.remove('messenger__open');
			}
		}
	},
	created() {
		this.boot();
	},
	methods: {
		...mapActions([
			'boot',
			'escapeChat',
			'toggleMessenger',
			'hideGallery',
			'toggleInfoPanel',
			'toggleAddUserDialog',
			'toggleNewChatDialog',
		]),
		toggle() {

			if (this.galleryOpened) {
				this.galleryOpened = false;
				return;
			}

			if (this.isOpen) {
				this.toggleMessenger();
			}
		},
		openGallery() {
			this.galleryOpened = true;
		},
	}
}
</script>

<style lang="scss">
#messenger_gallery{
	.slides{
		padding: 50px 0;
	}
}
/*noinspection CssUnusedSymbol*/
body.messenger__open {
	overflow: hidden;
	position: fixed;
}

.messenger__wrapper {
	display: flex;
	/* width: 90vw;
	height: 100vh; */
	width: 100%;
	height: 100%;

	position: fixed;
	z-index: 1000100; // чтобы перекрыть виджет битрикса с 1000000
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;

	background: rgba(0, 0, 0, 0.7);
	overflow-x: hidden;
	overflow-y: auto;
	opacity: 1;
	visibility: visible;
	transition: 0.2s;
}

.messenger__card-window {
	display: flex;
	width: 90vw;
	height: 100vh;

	position: fixed;
	z-index: 1000;
	right: 0;
	bottom: 50%;

	transform: translateY(50%);

	border-radius: 1.2rem 0 0 1.2rem;

	flex-direction: column;
	background: #ffffff;
	color: #0a0a0a;
	font-size: 14px;
	overflow-wrap: break-word;
	white-space: normal;
	-webkit-tap-highlight-color: transparent;
}

.messenger__chat-container {
	display: flex;
	flex: 1;
	flex-flow: row nowrap;
	overflow-y: hidden;
}

.ChatIcon{
	&-parent:hover,
	&-active{
		.ChatIcon-line{
			stroke: #3361FF;
		}
		.ChatIcon-shape{
			fill: #3361FF;
		}
	}
	&-parent_red:hover,
	&-active_red:hover,
	&-active_red{
		.ChatIcon-line{
			stroke: #F6264C;
		}
		.ChatIcon-shape{
			fill: #F6264C;
		}
	}
}

@media only screen and (max-width: 670px) {
	.messenger__card-window {
		width: 100vw;
	}
}

// чтобы кнопка битрикса чат не загораживала
.b24-widget-button-position-bottom-right{
	right: 7rem !important;
}
// bx-livechat-wrapper bx-livechat-show bx-livechat-position-bottom-right bx-livechat-logo-ru bx-livechat-custom-scroll
</style>
