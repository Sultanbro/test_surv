<script>
export default {
	name: 'JobtronApp',
	mounted(){
		(function(w,d,u){
			var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);s.id='bitrix-loader';
			var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
		})(window,document,'https://cdn-ru.bitrix24.kz/b1734679/crm/site_button/loader_8_dzfbjh.js');
	},
	beforeUnmount(){
		const scriptTag = document.getElementById('bitrix-loader')
		if(!scriptTag) return
		scriptTag.remove()
		// remove bitrix site button
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
	.b24-widget-button-shadow,
	.b24-widget-button-wrapper{
		display: none;
	}
	.profile-page{
		.b24-widget-button-shadow{
			display: block;
		}
		.b24-widget-button-wrapper{
			display: flex;
		}
	}
}
</style>