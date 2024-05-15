<template>
	<div class="AuthHeader">
		<div class="AuthHeader-left">
			<a
				:href="root"
				class="main__link"
			>
				<IconBack />
				<span>
					{{ LANG.index }}
				</span>
			</a>
		</div>
		<div class="AuthHeader-right">
			<AuthLang
				:value="lang"
				@input="$root.$data.setLang($event)"
			/>
			<button
				class="main__link"
				@click="$emit('open-chat')"
			>
				<IconChat />
				{{ LANG.support }}
			</button>
			<!-- <router-link
				to="/"
				class="main__link"
			>
				<IconChat />
				<span>
					{{ LANG.support }}
				</span>
			</router-link> -->
		</div>
	</div>
</template>

<script>
import AuthLang from './AuthLang.vue';
import IconBack from './IconBack.vue';
import IconChat from './IconChat.vue';

import * as LANG from './AuthHeader.lang.js';

export default {
	components: {
		AuthLang,
		IconBack,
		IconChat,
	},
	props: {
		back: Boolean,
	},
	data() {
		return {
			root: 'https://jobtron.org',
		};
	},
	computed: {
		LANG() {
			return LANG[this.$root.$data.lang || 'ru'];
		},
		lang() {
			return this.$root.$data.lang;
		},
	},
	methods: {
		openWindowChat() {
			this.$emit('open-chat')
		}
	},
};
</script>

<style lang="scss">
button {
	border: 0;
	cursor: pointer;
}

.AuthHeader {
	display: flex;
	justify-content: space-between;
	gap: 10px;

	margin-bottom: 32px;
	container: auth-header / inline-size;

	&-left,
	&-right {
		display: flex;
		gap: 10px;
	}
}
@container auth-info (max-width: 991.98px) {
	.AuthHeader {
		margin-bottom: 10px;
	}
}

@container auth-header (max-width: 415px) {
	.AuthHeader {
		.AuthLangItem-label {
			display: none;
		}
		// .AuthHeader-right{
		// 	.main__link{
		// 		span{
		// 			display: none;
		// 		}
		// 	}
		// }
	}
}

.main__link {
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 10px;

	background: #ffffff;
	color: #333333;
	text-decoration: none;
	padding: 0px 10px;
	border-radius: 10px;
	height: 40px;
	font-size: 16px;
	line-height: 20px;

	&:hover {
		color: #0c50ff;
		.AuthIcon {
			&-stroke {
				stroke: #0c50ff;
			}
			&-fill {
				fill: #0c50ff;
			}
		}
	}
}
.AuthIcon {
	&-stroke {
		stroke: #333333;
	}
}
</style>
