<script>
import { mapGetters } from 'vuex'
import {
	updateOnlineStatus,
	// checkVersion,
} from './stores/api'

const DEFAULT_TITLE = 'Jobtron.org';

export default {
	name: 'JobtronApp',
	data(){
		return {
			sendStatusTimer: null,
			sendStatusDelay: 30000,
			sendStatusDelayed: false,
			favicon: null,
		}
	},
	computed: {
		...mapGetters(['unreadCount'])
	},
	watch: {
		$route(){
			this.updateTitle()
		},
		unreadCount(){
			this.updateTitle()
			this.updateIcon()
		}
	},
	mounted(){
		if(window.location.hostname.split('.')[0] === 'bp'){
			(function(w,d,u){
				var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);s.id='bitrix-loader';
				var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
			})(window,document,'https://cdn-ru.bitrix24.kz/b1734679/crm/site_button/loader_8_dzfbjh.js');
		}
		this.startOlineTracking()
		this.updateTitle()
		this.updateIcon()
	},
	beforeUnmount(){
		const scriptTag = document.getElementById('bitrix-loader')
		if(!scriptTag) return
		scriptTag.remove()
		// remove bitrix site button
		this.stopOlineTracking()
	},
	methods: {
		async sendStatus(){
			if(this.sendStatusTimer) return (this.sendStatusDelayed = true)
			updateOnlineStatus()
			// const newVerion = await checkVersion()
			// if(newVerion) this.$toast.info('Достуна новая версия сайта, обновите страницу')
			this.sendStatusTimer = setTimeout(() => {
				this.sendStatusTimer = null
				if(this.sendStatusDelayed) this.sendStatus()
				this.sendStatusDelayed = false
			}, this.sendStatusDelay)
		},
		startOlineTracking(){
			document.body.addEventListener('click', this.sendStatus)
			document.body.addEventListener('keyup', this.sendStatus)
		},
		stopOlineTracking(){
			document.body.removeEventListener('click', this.sendStatus)
			document.body.removeEventListener('keyup', this.sendStatus)
		},
		updateTitle(){
			// Use next tick to handle router history correctly
			// see: https://github.com/vuejs/vue-router/issues/914#issuecomment-384477609
			this.$nextTick(() => {
				document.title = (this.unreadCount ? `(${this.unreadCount}) ` : '')  + this.$route.meta.title || DEFAULT_TITLE
				document.body.className = this.$route.meta.bodyClass || ''
			})
		},
		loadIcon(){
			return new Promise((resolve) => {
				const img = document.createElement('img');
				img.addEventListener('load', () => {
					resolve(img)
				})
				img.src = '/favicon.ico?ver1.2'
			})
		},
		async updateIcon(){
			const img = await this.loadIcon()
			const iconNode = document.querySelector('link[rel="icon"][type="image/x-icon"]')

			if(this.unreadCount === 0){
				iconNode.href = '/favicon.ico?ver1.2'
				return
			}
			const size = 48
			const canvas = document.createElement('canvas')
			canvas.width = size
			canvas.height = size
			const context = canvas.getContext('2d')

			context.drawImage(img, 0, 0, size, size)
			context.beginPath()
			context.arc(
				size - size / 2.5,
				size / 2.5,
				size / 2.5,
				0,
				2 * Math.PI
			)
			context.fillStyle = '#FF0000'
			context.fill()


			context.font = '700 32px "helvetica", sans-serif'
			if(this.unreadCount > 99){
				context.font = '700 20px "helvetica", sans-serif'
			}
			context.textAlign = 'center'
			context.textBaseline = 'middle'
			context.fillStyle = '#FFFFFF'
			context.fillText(this.unreadCount > 99 ? '99+' : this.unreadCount, size - size / 2.5, size / 2.5)

			iconNode.href = canvas.toDataURL('image/png')
		}
	}
}
</script>
<template>
	<div class="right-panel-app">
		<router-view />
	</div>
</template>

<style lang="scss">
body{
	.bx-livechat-wrapper,
	.b24-widget-button-shadow,
	.b24-widget-button-wrapper{
		display: none;
	}
	&.profile-page{
		.bx-livechat-wrapper,
		.b24-widget-button-shadow{
			display: block;
		}
		.b24-widget-button-wrapper{
			display: flex;
		}
	}
	.Vue-Toastification__container{
		z-index: 2000000;
	}
}
</style>
