<template>
	<div class="ConversationSearchTitle">
		<div class="ConversationSearchTitle-title">
			{{ title }}
		</div>
		<div class="ConversationSearchTitle-actions">
			<!-- <div
				class="ConversationSearchTitle-icon ChatIcon-parent"
				@click="$emit('forward')"
			>
				<ChatIconHistoryBack />
			</div>
			<div
				class="ConversationSearchTitle-icon ChatIcon-parent"
				@click="$emit('delete')"
			>
				<ChatIconHistoryDelete />
			</div> -->
		</div>
	</div>
</template>

<script>
// import {
// 	ChatIconHistoryBack,
// 	ChatIconHistoryDelete,
// } from '@icons'

export default {
	name: 'ConversationSearchTitle',
	components: {
		// ChatIconHistoryBack,
		// ChatIconHistoryDelete,
	},
	props: {
		title: {
			type: String,
			default: '',
		}
	}
}
</script>

<style lang="scss">
.ConversationSearchTitle{
	display: flex;
	flex-flow: row nowrap;
	align-items: center;
	justify-content: space-between;

	margin-bottom: 1rem;

	&-title{
		font-weight: 500;
		font-size: 16px;
		line-height: 16px;
		letter-spacing: -0.02em;
		color: #152136;
	}
	// &-actions{}
	&-icon{
		display: inline-flex;
		align-items: center;
		justify-content: center;

		padding: 0.5rem;
	}
}
</style>
