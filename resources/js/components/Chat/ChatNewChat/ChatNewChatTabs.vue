<template>
	<div class="ChatNewChatTabs">
		<div
			class="ChatNewChatTabs-tab ChatIcon-parent"
			:class="{'ChatNewChatTabs-tab_active': value === 'chat'}"
			@click="$emit('input', 'chat')"
		>
			<ChatIconCreateChat />
			Новое сообщение
		</div>
		<div
			class="ChatNewChatTabs-tab ChatIcon-parent"
			:class="{'ChatNewChatTabs-tab_active': value === 'group'}"
			@click="$emit('input', 'group')"
		>
			<ChatIconCreateGroup />
			Новая группа
		</div>
	</div>
</template>

<script>
import {
	ChatIconCreateChat,
	ChatIconCreateGroup,
} from '@icons'
export default {
	name: 'ChatNewChatTabs',
	components: {
		ChatIconCreateChat,
		ChatIconCreateGroup,
	},
	props: {
		value: {
			type: String,
			default: 'chat'
		}
	}
}
</script>

<style lang="scss">
.ChatNewChatTabs {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: flex-start;
	gap: 50px;
	padding-right: 20px;
	border-bottom: 1px solid #EBEBF9;

	&-tab {
		padding-bottom: 16px;
		font-family: "Inter", sans-serif;
		font-style: normal;
		font-weight: 600;
		font-size: 16px;
		line-height: 20px;
		letter-spacing: -0.02em;
		color: #8DA0C1;
		cursor: pointer;

		&_active {
			margin-bottom: -1px;
			border-bottom: 1px solid #156AE8;
			color: #156AE8;
		}

		&:hover {
			color: #156AE8;
		}
	}
}
</style>
