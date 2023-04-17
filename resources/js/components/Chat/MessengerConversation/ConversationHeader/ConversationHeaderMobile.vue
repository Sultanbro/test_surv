<template>
	<div class="ConversationHeaderMobile">
		<slot name="before" />
		<div class="ConversationHeaderMobile-cols">
			<div class="ConversationHeaderMobile-left">
				<slot name="left">
					<div
						class="ConversationHeaderMobile-icon ChatIcon-parent"
						@click="$emit('back')"
					>
						<ChatIconBack />
					</div>
				</slot>
			</div>
			<div class="ConversationHeaderMobile-center">
				<slot>
					<div class="ConversationHeaderMobile-title">
						{{ chat.title }}
					</div>
					<div class="ConversationHeaderMobile-subtitle">
						<template v-if="chat.private">
							{{ chat.position }}
						</template>
						<template v-else>
							{{ chat.users.length }} участников
						</template>
					</div>
				</slot>
			</div>
			<div class="ConversationHeaderMobile-right">
				<slot name="right">
					<div
						class="ConversationHeaderMobile-icon ChatIcon-parent"
						@click="$emit('close')"
					>
						<ChatIconSearchClose
							width="30"
							height="30"
						/>
					</div>
				</slot>
			</div>
		</div>
		<slot name="after" />
	</div>
</template>

<script>
import {mapGetters} from 'vuex';
import {
	ChatIconSearchClose,
	ChatIconBack,
} from '@icons'

export default {
	name: 'ConversationHeaderMobile',
	components: {
		ChatIconSearchClose,
		ChatIconBack,
	},
	computed: {
		...mapGetters(['chat'])
	}
}
</script>

<style lang="scss">
.ConversationHeaderMobile{
	padding: 2rem;

	background-color: #f5f8fc;
	&-cols{
		display: flex;
		flex-flow: row nowrap;
		align-items: flex-start;
		justify-content: space-between;
	}
	&-icon{
		padding: 0.5rem;
	}
	&-center{
		display: flex;
		flex-flow: column nowrap;
		align-items: center;
		justify-content: flex-start;

		overflow: hidden;
	}
	&-title{
		max-width: 100%;
		margin-top: 1rem;
		margin-bottom: 0.8rem;

		font-weight: 600;
		font-size: 18px;
		line-height: 16px;
		letter-spacing: -0.01em;
		color: #13223F;

		-webkit-line-clamp: 1;
		text-overflow: ellipsis;
		overflow: hidden;
		white-space: nowrap;
	}
	&-subtitle{
		font-weight: 400;
		font-size: 13px;
		line-height: 14px;
		letter-spacing: -0.02em;
		color: #8DA0C1;
	}
}
</style>
