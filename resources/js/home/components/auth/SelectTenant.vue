<template>
	<div class="SelectTenant">
		<AuthSubTitle>
			<a
				href="/logout"
				class="fw500"
				@click.prevent="logout"
			>
				{{ lang.logout }}
			</a>
		</AuthSubTitle>
		<AuthTitle>
			{{ lang.title }}
		</AuthTitle>
		<AuthSubTitle>
			{{ lang.subtitle }}
		</AuthSubTitle>

		<div class="SelectTenant-links">
			<a
				v-for="item in links"
				:key="item.id"
				:href="item.link"
				class="SelectTenant-link"
			>
				<div class="SelectTenant-icon">
					<img
						v-if="item.icon"
						:src="item.icon"
						alt=""
						class="SelectTenant-img"
					>
					<IconCabinet v-else />
				</div>
				<div class="SelectTenant-content">
					<div class="SelectTenant-title">
						{{ lang.workspace }} {{ item.id }}
					</div>
					<div class="SelectTenant-info">
						<!-- Неизвестный {{ lang.tariff }} &bull; 0/0 {{ lang.users }} -->
					</div>
				</div>
			</a>
		</div>
	</div>
</template>

<script>
import AuthTitle from './AuthTitle.vue';
import AuthSubTitle from './AuthSubTitle.vue';
import IconCabinet from './IconCabinet.vue';

import axios from 'axios'
import * as LANG from './SelectTenant.lang.js'

export default {
	name: 'SelectTenant',
	components: {
		AuthTitle,
		AuthSubTitle,
		IconCabinet,
	},
	props: {
		links: {
			type: Array,
			default: () => [],
		},
		csrfToken: {
			type: String,
			default: ''
		},
	},
	data(){
		return {}
	},
	computed: {
		lang(){
			return LANG[this.$root.$data.lang || 'ru']
		},
	},
	watch: {},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {
		logout(){
			axios.post('/logout', {
				_token: this.csrfToken
			}).then(() => {
				location.assign('/')
			})
		}
	},
}
</script>

<style lang="scss">
.SelectTenant{
	&-links{
		display: flex;
		flex-flow: column;
		gap: 4px;

		margin-top: 20px;
	}
	&-link{
		display: flex;
		flex-flow: row nowrap;
		gap: 16px;

		padding: 8px 4px;

		color: #333;
		text-decoration: none;

		border-radius: 12px;
		background-color: #fff;
		&:hover{
			background: #E8EEFD;
		}
	}
	&-icon{
		flex: 0 0 48px;
		width: 48px;
	}
	&-img{
		width: 100%;
		aspect-ratio: 1;
	}
	&-content{
		flex: 1;
	}
	&-title{
		margin-bottom: 4px;
		font-size: 16px;
		font-weight: 600;
		line-height: 24px;
		text-align: left;
		color: #333;
	}
	&-info{
		font-size: 14px;
		font-weight: 400;
		line-height: 20px;
		text-align: left;
		color: #737B8A;
	}
}
</style>
