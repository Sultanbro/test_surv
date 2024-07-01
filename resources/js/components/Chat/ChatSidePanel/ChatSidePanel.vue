<template>
	<div class="header__right-messages">
		<ChatsList />
	</div>
</template>

<script>
import ChatsList from '../ChatsList/ChatsList';

// noinspection JSUnusedGlobalSymbols
export default {
	components: {
		ChatsList,
	}
}
</script>

<style>

.header__right-messages {
	width: 60px;
  overflow: hidden;
}
.header__right-messages .ChatsList{
	height: 100%;
	width: 90px;
	padding-right: 30px;
	overflow-y: auto;
	padding-left: 0px;
	box-sizing: content-box;
	padding-left: 3px;
}
</style>
