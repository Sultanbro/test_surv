<script>
import { updateOnlineStatus } from './stores/api'
export default {
	name: 'JobtronApp',
	data(){
		return {
			sendStatusTimer: null,
			sendStatusDelay: 30000,
			sendStatusDelayed: false
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
	},
	beforeUnmount(){
		const scriptTag = document.getElementById('bitrix-loader')
		if(!scriptTag) return
		scriptTag.remove()
		// remove bitrix site button
		this.stopOlineTracking()
	},
	methods: {
		sendStatus(){
			if(this.sendStatusTimer) return (this.sendStatusDelayed = true)
			updateOnlineStatus()
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
