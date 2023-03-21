<template>
	<div
		class="messenger__wrapper"
		v-show="isOpen"
	>
		<div
			@keydown.esc="escapeChat"
			class="messenger__card-window"
			id="messengerWindow"
			v-click-outside="toggle"
		>
			<div class="messenger__chat-container">
				<ChatNav
					v-show="!isChatSearchMode"
					:fullscreen="true"
				/>
				<MessengerConversation />
			</div>
		</div>
		<InfoPanel />
		<ImageGallery
			id="messenger_gallery"
			:images="galleryImages"
			:index="galleryIndex"
			@onopen="openGallery"
			@close="hideGallery"
		/>
		<ConfirmDialog />
		<!-- <ChatIconsDemo /> -->
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import ChatNav from './ChatNav/ChatNav.vue';
import MessengerConversation from './MessengerConversation/MessengerConversation.vue';
import InfoPanel from './InfoPanel/InfoPanel';
import clickOutside from './directives/clickOutside.ts';
import ImageGallery from './ImageGallery/ImageGallery.vue';
import ConfirmDialog from './ConfirmDialog/ConfirmDialog.vue';
// import ChatIconsDemo from './icons/ChatIconsDemo.vue'

// noinspection JSUnusedGlobalSymbols
export default {
	name: 'ChatApp',
	components: {
		ChatNav,
		MessengerConversation,
		InfoPanel,
		ImageGallery,
		ConfirmDialog,
		// ChatIconsDemo,
	},
	directives: {
		clickOutside
	},
	watch: {
		isOpen: function (val) {
			if (val) {
				// set div messenger__open class
				document.body.classList.add('messenger__open');
			} else {
				// remove div messenger__open class
				document.body.classList.remove('messenger__open');
			}
		}
	},
	data() {
		return {
			galleryOpened: false,
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
		]),
	},
	created() {
		this.boot();
	},
	methods: {
		...mapActions([
			'boot',
			'escapeChat',
			'toggleMessenger',
			'hideGallery'
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
	z-index: 20000;
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
	// &-line{
	// 	stroke: #8DA0C1;
	// }
	&:hover{
		.ChatIcon-line{
			stroke: #3361FF;
		}
		.ChatIcon-shape{
			fill: #3361FF;
		}
	}
	&-parent{
		&:hover{
			.ChatIcon-line{
				stroke: #3361FF;
			}
			.ChatIcon-shape{
				fill: #3361FF;
			}
		}
	}
}

@media only screen and (max-width: 670px) {
	.messenger__card-window {
		width: 100vw;
	}
}

// чтобы кнопка битрикса чат не загораживала
.b24-widget-button-position-bottom-right.b24-widget-button-position-bottom-right{
	right: 7rem;
}
</style>
