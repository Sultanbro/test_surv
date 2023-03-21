<template>
	<div class="messenger__meta">
		<ConversationMessageReactions
			class="messenger__meta-reactions"
			:reactions="reactions"
		/>
		<div
			v-if="countReaders > 1 && own"
			class="messenger__meta-views"
		>
			{{ countReaders }}
		</div>
		<div class="messenger__meta-date">
			{{ time }}
		</div>
		<div
			v-if="own"
			class="messenger__meta-ckeck"
		>
			<ChatIconStatusReaded v-if="countReaders" />
			<ChatIconStatusSended v-else />
		</div>
		<div
			v-if="false"
			class="messenger__meta-ckeck"
			:class="{'messenger__meta-ckeck_checked': countReaders}"
		/>
		<div class="messenger__meta-inner">
			<ConversationMessageReactions
				class="messenger__meta-actual"
				:reactions="reactions"
				@reaction-click="$emit('reaction-click', $event)"
			/>
			<div
				v-if="countReaders > 1 && own"
				class="messenger__meta-views messenger__meta-actual"
				:title="readersString"
			>
				{{ countReaders }}
			</div>
			<div class="messenger__meta-date messenger__meta-actual">
				{{ time }}
			</div>
			<div
				v-if="own"
				class="messenger__meta-ckeck messenger__meta-actual"
			>
				<ChatIconStatusReaded v-if="countReaders" />
				<ChatIconStatusSended v-else />
			</div>
			<div
				v-if="false"
				class="messenger__meta-ckeck messenger__meta-actual"
				:class="{'messenger__meta-ckeck_checked': countReaders}"
			/>
		</div>
	</div>
</template>

<script>
import ConversationMessageReactions from './ConversationMessageReactions.vue'
import { ChatIconStatusSended, ChatIconStatusReaded } from '../../../icons/chat-icons.js'

export default {
	name: 'ConversationMessageMeta',
	components: {
		ConversationMessageReactions,
		ChatIconStatusSended,
		ChatIconStatusReaded,
	},
	props: {
		readers: {
			type: Array,
			default: () => [],
		},
		time: {
			type: String,
			default: '',
		},
		own: {
			type: Boolean,
			default: false,
		},
		reactions: {
			type: Array,
			default: () => [],
		},
	},
	computed: {
		countReaders(){
			return this.readers.length
		},
		readersString(){
			return this.readers.reduce((list, reader) => {
				list.push(`${ reader.name } ${ reader.last_name }`)
				return list
			}, []).join(', ')
		}
	},
}
</script>

<style lang="scss">
.messenger__meta{
	margin-left: 1rem;
	float: right;
	white-space: nowrap;
	user-select: none;
	font-size: 1rem;
	&-reactions,
	&-views,
	&-date,
	&-ckeck{
		display: inline-flex;
		visibility: hidden;
	}
	&-ckeck{
		font-size: 1.5rem;
		align-items: center;
		// &::before{
		// 	content: '✓';
		// }
	}
	&-views{
		&::before{
			content: '👁';
		}
	}
	&-ckeck_checked{
		&::after{
			content: '✓';
			margin-left: -0.6em;
		}
	}
	&-inner{
		display: flex;
		flex-flow: row nowrap;
		gap: .5rem;
		align-items: center;
		margin-bottom: .2rem;
		margin-right: .8rem;
		position: absolute;
		right: 0;
		bottom: 0;
	}
	&-actual{
		visibility: visible;
	}
}
</style>
