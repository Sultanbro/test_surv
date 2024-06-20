<template>
	<div
		v-if="active"
		class="faq-content"
	>
		<p class="faq-content-title">
			{{ active.title }}
		</p>
		<div class="fac-content-wrapper">
			<!-- eslint-disable-next-line -->
			<div class="faq-content-body" v-html="markText"></div>
			<button
				class="faq-content-button"
				@click="openChatButton"
			>
				Связаться с техподдержкой
				<img
					v-b-popover.hover.right.html="'Спросите у нас о чем угодно'"
					:src="
						require('../../../../sass/newdesign/images/dist/profit-info-white.svg')
							.default
					"
					alt=""
				>
			</button>
		</div>
	</div>
</template>

<script>
import Mark from 'mark.js/dist/mark.es6.js';

const markOptions = {
	element: 'span',
	className: 'KBArticle-mark',
	exclude: ['.KBArticle-definition'],
	accuracy: 'exactly',
	separateWordSearch: false,
};

export default {
	name: 'FaqContent',
	props: {
		active: {
			type: Object,
			default: null,
		},
		search: {
			type: String,
			default: '',
		},
	},
	data() {
		return {
			isOpenButtonChat: false,
		};
	},
	computed: {
		markText() {
			const div = document.createElement('div');
			div.innerHTML = this.active.body;
			const instance = new Mark(div);

			instance.mark(this.search, {
				...markOptions,
				accuracy: 'partially',
				each: (el) => {
					el.classList.add('mark-red');
				},
			});

			return div.innerHTML;
		},
	},
	methods: {
		initChat() {
			if (!window.jChatWidget) {
				window.addEventListener('onBitrixLiveChat', this.onInitChatWidget);
				const url =
					'https://cdn-ru.bitrix24.kz/b1734679/crm/site_button/loader_14_qetlt8.js';
				const s = document.createElement('script');
				s.async = true;
				s.src = url + '?' + ((Date.now() / 60000) | 0);
				const h = document.getElementsByTagName('script')[0];
				h.parentNode.insertBefore(s, h);
			} else {
				this.onInitChatWidget({ detail: { widget: window.jChatWidget } });
			}
		},
		onInitChatWidget(event) {
			window.jChatWidget = event.detail.widget;

			this.$nextTick(() => {
				const elem = document.querySelector('.b24-widget-button-shadow');
				if (!elem) return;
				const parent = elem.parentNode;
				parent.classList.add('hidden');
				window.jChatWidgetBtn = parent;
			});
		},
		openChatButton() {
			this.isOpenButtonChat = !this.isOpenButtonChat;

			if (this.isOpenButtonChat) {
				this.initChat();
				window.jChatWidgetBtn.style.display = 'block';
				window.jChatWidgetBtn.style.zIndex = '9999';

				const chatWidgetElements = window.jChatWidgetBtn.querySelectorAll('*');
				chatWidgetElements.forEach((el) => {
					el.style.zIndex = '9999';
				});
			} else {
				window.jChatWidgetBtn.style.display = 'none';
			}
		},
	},
};
</script>

<style lang="scss">
.mark-red {
	background-color: orange;
	color: white;
}

.faq-content {
	width: 100%;
	height: calc(100vh - 130px);
	padding: 20px;
	overflow: auto;
	z-index: 1;
	position: relative;
	scrollbar-width: none;
	// -ms-overflow-style: none;
	&-title {
		font-size: 20px;
		font-weight: 700;
		text-align: center;
		padding-bottom: 20px;
		margin-bottom: 20px;
		border-bottom: 1px solid #ddd;
	}

	.fac-content-wrapper {
		height: 90%;
		display: flex;
		flex-direction: column;
		justify-content: space-between;

		.faq-content-body {
			font-size: 16px;
			line-height: 1.3;
		}

		.faq-content-button {
			margin-top: 2%;
			background-color: #00ceff;
			color: white;
			border-radius: 30px;
			font-size: 20px;
			padding: 1.2%;
			transition: all 100ms ease-out;
			display: flex;
			align-items: center;
			justify-content: center;
			width: 348px;
			gap: 7px;
			outline: none;

			&:hover {
				background-color: #4fdcff;
			}
		}
	}
}

.hidden {
	display: none;
}

.b24-widget-button-shadow,
.b24-widget-button-shadow * {
	z-index: 9999 !important;
}
</style>
