<template>
	<div
		class="ConversationFeedReaders"
		:class="{'ConversationFeedReaders_own': user.id === message.sender_id}"
	>
		<ChatIconMassReaded />
		Собщение просмотрено:
		{{ getFirstReaders(message.readers) }}
		<ConversationFeedMoreReaders :readers="getLastReaders(message.readers)" />
	</div>
</template>

<script>
import { mapGetters } from 'vuex'
import ConversationFeedMoreReaders from './ConversationFeedMoreReaders'
import {
	ChatIconMassReaded,
} from '@icons'

export default {
	name: 'ConversationFeedReaders',
	components: {
		ConversationFeedMoreReaders,
		ChatIconMassReaded,
	},
	props:{
		message: {
			type: Object,
			default: null
		}
	},
	computed: {
		...mapGetters(['user'])
	},
	methods: {
		getFirstReaders(readers, limit = 60){
			let text = ''
			const names = []
			readers.every(reader => {
				const fullName = `${reader.name} ${reader.last_name}`
				const inSize = (text.length + fullName.length) < limit
				if(inSize) {
					text += ', ' + fullName
					names.push(fullName)
				}
				return inSize
			})
			return names.join(', ')
		},
		getLastReaders(readers, limit = 60){
			let text = ''
			const users = []
			readers.forEach(reader => {
				const fullName = `${reader.name} ${reader.last_name}`
				const inSize = (text.length + fullName.length) < limit
				if(inSize) {
					text += ', ' + fullName
				}
				else{
					users.push(reader)
				}
			})
			return users
		},
	}
}
</script>

<style lang="scss">
.ConversationFeedReaders{
	display: flex;
	justify-content: flex-start;
	align-items: center;
	gap: 12px;

	padding: 18px 5px;
	margin: 0 0 0 20px;

	font-weight: 400;
	font-size: 11px;
	line-height: 17px;
	letter-spacing: -0.02em;

	color: #4F6386;
	&_own{
		justify-content: flex-end;
		margin: 0 20px 0 0;
	}
}
</style>
