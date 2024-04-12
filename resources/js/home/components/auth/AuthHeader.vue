<template>
	<div class="AuthHeader">
		<div class="AuthHeader-left">
			<a
				v-if="back"
				:href="root"
				class="main__link"
			>
				<IconBack />
				<span>На главную</span>
			</a>
		</div>
		<div class="AuthHeader-right">
			<AuthLang
				:value="lang"
				@input="$root.$data.setLang($event)"
			/>
			<router-link
				to="/"
				class="main__link"
			>
				<IconChat />
				<span>Поддержка</span>
			</router-link>
		</div>
	</div>
</template>

<script>
import AuthLang from './AuthLang.vue';
import IconBack from './IconBack.vue'
import IconChat from './IconChat.vue'

export default {
	components: {
		AuthLang,
		IconBack,
		IconChat,
	},
	props: {
		back: Boolean,
	},
	data(){
		return {
			root: 'https://jobtron.org',
		}
	},
	computed: {
		lang() {
			return this.$root.$data.lang;
		},
	},
};
</script>

<style lang="scss">
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
@container auth-header (max-width: 415px){
	.AuthHeader{
		.AuthLangItem-label{
			display: none;
		}
		.AuthHeader-right{
			.main__link{
				span{
					display: none;
				}
			}
		}
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

	&:hover{
		color: #0C50FF;
		.AuthIcon{
			&-stroke{
				stroke: #0C50FF;
			}
			&-fill{
				fill: #0C50FF;
			}
		}
	}
}
.AuthIcon{
	&-stroke{
		stroke: #333333;
	}
}
</style>
