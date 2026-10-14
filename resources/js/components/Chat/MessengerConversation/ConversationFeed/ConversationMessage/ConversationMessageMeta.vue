<template>
	<div class="ConversationMessageMeta">
		<!-- <div
			v-if="countReaders > 1 && own"
			class="ConversationMessageMeta-views"
		>
			{{ countReaders }}
		</div> -->
		<div class="ConversationMessageMeta-date">
			{{ time }}
		</div>
		<div
			v-if="own"
			class="ConversationMessageMeta-ckeck"
		>
			<ChatIconStatusReaded v-if="countReaders" />
			<ChatIconStatusSended v-else />
		</div>
		<!-- <div
			class="ConversationMessageMeta-ckeck"
			:class="{'ConversationMessageMeta-ckeck_checked': countReaders}"
		/> -->

		<ConversationMessageReactions
			class="ConversationMessageMeta-reactions"
			:reactions="reactions"
		/>

		<div class="ConversationMessageMeta-inner">
			<!-- <div
				v-if="countReaders > 1 && own"
				class="ConversationMessageMeta-views ConversationMessageMeta-actual"
				:title="readersString"
			>
				{{ countReaders }}
			</div> -->
			<div class="ConversationMessageMeta-date ConversationMessageMeta-actual">
				{{ time }}
			</div>
			<div
				v-if="own"
				class="ConversationMessageMeta-ckeck ConversationMessageMeta-actual"
			>
				<ChatIconStatusReaded v-if="countReaders" />
				<ChatIconStatusSended v-else />
			</div>

			<ConversationMessageReactions
				class="ConversationMessageMeta-actual"
				:reactions="reactions"
				@reaction-click="$emit('reaction-click', $event)"
			/>
			<!-- <div
				class="ConversationMessageMeta-ckeck ConversationMessageMeta-actual"
				:class="{'ConversationMessageMeta-ckeck_checked': countReaders}"
			/> -->
		</div>
	</div>
</template>

<script>
import ConversationMessageReactions from './ConversationMessageReactions.vue'
import {
	ChatIconStatusSended,
	ChatIconStatusReaded,
} from '@icons'

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
.ConversationMessageMeta{
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

		font-weight: 400;
		font-size: 11px;
		line-height: 14px;

		letter-spacing: -0.02em;
		color: #A6B7D4;
	}
	&-ckeck{
		font-size: 1.5rem;
		align-items: center;
		// &::before{
		// 	content: '✓';
		// }
	}
	// &-views{
	// 	&::before{
	// 		content: '👁';
	// 	}
	// }
	// &-ckeck_checked{
	// 	&::after{
	// 		content: '✓';
	// 		margin-left: -0.6em;
	// 	}
	// }
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
