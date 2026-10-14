<template>
	<div class="ConversationSearchFilter">
		<div
			class="ConversationSearchFilter-item ChatIcon-parent"
			:class="{'ConversationSearchFilter-item_current': value === 'users'}"
			@click="$emit('input', 'users')"
		>
			<ChatIconHistoryUsers />
		</div>
		<div
			class="ConversationSearchFilter-item ChatIcon-parent"
			:class="{'ConversationSearchFilter-item_current': value === 'images'}"
			@click="$emit('input', 'images')"
		>
			<ChatIconHistoryImages />
		</div>
		<div
			class="ConversationSearchFilter-item ChatIcon-parent"
			:class="{'ConversationSearchFilter-item_current': value === 'videos'}"
			@click="$emit('input', 'videos')"
		>
			<ChatIconHistoryVideos />
		</div>
		<div
			class="ConversationSearchFilter-item ChatIcon-parent"
			:class="{'ConversationSearchFilter-item_current': value === 'docs'}"
			@click="$emit('input', 'docs')"
		>
			<ChatIconHistoryDoc />
		</div>
		<div
			class="ConversationSearchFilter-item ChatIcon-parent"
			:class="{'ConversationSearchFilter-item_current': value === 'music'}"
			@click="$emit('input', 'music')"
		>
			<ChatIconHistoryMusic />
		</div>
		<div
			class="ConversationSearchFilter-item ChatIcon-parent"
			:class="{'ConversationSearchFilter-item_current': value === 'links'}"
			@click="$emit('input', 'links')"
		>
			<ChatIconHistoryLinks />
		</div>
	</div>
</template>

<script>
import {
	ChatIconHistoryUsers,
	ChatIconHistoryImages,
	ChatIconHistoryVideos,
	ChatIconHistoryDoc,
	ChatIconHistoryMusic,
	ChatIconHistoryLinks,
} from '@icons'
export default {
	name: 'ConversationSearchFilter',
	components: {
		ChatIconHistoryUsers,
		ChatIconHistoryImages,
		ChatIconHistoryVideos,
		ChatIconHistoryDoc,
		ChatIconHistoryMusic,
		ChatIconHistoryLinks,
	},
	props: {
		value: {
			type: String,
			default: 'users'
		}
	}
}
</script>

<style lang="scss">
	.ConversationSearchFilter{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		justify-content: space-between;
		height: 65px;
		border-bottom: 2px solid #E1EBF9;
		&-item{
			padding: 1rem;
			&_current{
				background-color: #F5F8FC;
			}
		}
	}
</style>
