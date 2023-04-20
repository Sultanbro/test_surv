<template>
	<span
		v-if="readers && readers.length"
		class="ConversationFeedMoreReaders"
	>
		ещё {{ readers.length }}
		<span class="ConversationFeedMoreReaders-popup">
			<span
				v-for="reader in readers"
				:key="reader.id"
				class="ConversationFeedMoreReaders-popupItem"
			>
				<JobtronAvatar
					:image="'/users_img/' + reader.img_url"
					:title="`${reader.name} ${reader.last_name}`"
					:size="24"
				/>
				{{ `${reader.name} ${reader.last_name}` }}
			</span>
		</span>
	</span>
</template>

<script>
import JobtronAvatar from '@ui/Avatar'

export default {
	name: 'ConversationFeedMoreReaders',
	components: {
		JobtronAvatar,
	},
	props: {
		readers: {
			type: Array,
			default: () => []
		}
	}
}
</script>

<style lang="scss">
.ConversationFeedMoreReaders{
	position: relative;

	font-weight: 600;
	font-size: 14px;
	line-height: 17px;

	letter-spacing: -0.02em;

	color: #3361FF;
	&:hover{
		.ConversationFeedMoreReaders-popup{
			display: block;
		}
	}
	&-popup{
		display: none;
		min-width: 100px;
		max-height: 30vh;
		border-radius: 8px;
		padding: 10px 0;

    overflow-y: auto;

		position: absolute;
		z-index: 1000;
		right: 0;
		bottom: 100%;

		background-color: #fff;
		box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.05), 0px 15px 60px -40px rgba(45, 50, 90, 0.2);
	}
	&-popupItem{
		display: flex;
		align-items: center;
		justify-content: flex-start;
		gap: 10px;

		width: 100%;
		padding: 10px 20px;

		text-decoration: none;
		color: #0a0a0a;
		white-space: nowrap;
	}
}
</style>
