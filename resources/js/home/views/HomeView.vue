<template>
	<div id="HomeView">
		<Navbar />
		<Section1 />
		<Section2 />
		<Reviews />
		<Tariffs />
		<Section3 />
		<Section4 />
		<SectionFooter />
	</div>
</template>

<script>
import Navbar from '../components/sections/Navbar.vue';
import Section1 from '../components/sections/Section1.vue';
import Section2 from '../components/sections/Section2.vue';
import Section3 from '../components/sections/Section3.vue';
import Section4 from '../components/sections/Section4.vue';
import Reviews from '../components/sections/Reviews.vue';
import Tariffs from '../components/sections/Tariffs.vue';
import SectionFooter from '../components/sections/Footer.vue';

export default {
	name: 'HomeView',
	components: {
		Navbar,
		Section1,
		Section2,
		Section3,
		Section4,
		Reviews,
		Tariffs,
		SectionFooter,
	},
	mounted() {
		this.initChat();
	},
	methods: {
		initChat() {
			if (!window.jChatWidget) {
				window.addEventListener('onBitrixLiveChat', this.onInitChatWidget);
				const url =
					'https://cdn-ru.bitrix24.kz/b1734679/crm/site_button/loader_12_koodzo.js';
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
				parent.className = 'hidden';
				window.jChatWidgetBtn = parent;
			});
			this.openChat();
		},
		openChat() {
			if (!this.isBp) {
				window.jChatWidget.open();
			}
		},
	},
};
</script>
