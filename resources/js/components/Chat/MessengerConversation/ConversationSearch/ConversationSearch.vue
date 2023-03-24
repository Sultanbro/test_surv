<template>
	<div class="messenger__container-scroll">
		<div
			class="messenger__messages-container"
			style="flex-grow: 3"
		>
			<ConversationHeader />
			<div class="messenger__messages-search">
				<div class="messenger__messages-search-input">
					<ChatIconSearch />
					<input
						type="text"
						placeholder="Введите текст для поиска"
						v-model="searchMessagesQuery"
					>
					<div
						v-show="searchMessagesQuery"
						class="messenger__messages-search-clear ChatIcon-parent"
						@click="searchMessagesQuery = ''"
					>
						<ChatIconSearchClose />
					</div>
				</div>
				<div class="messenger__messages-search-date">
					<input
						type="date"
						v-model="searchMessagesDate"
					>
				</div>
			</div>
			<div class="messenger__messages-search-results messenger__messages-search-results_bg">
				<div
					v-for="(message, index) in chatSearchMessagesResults"
					:key="index"
					class="messenger__message-wrapper"
					@click="goto(message, $event)"
				>
					<ConversationMessage :message="message" />
				</div>
			</div>
		</div>
		<div class="messenger__messages-container">
			<ConversationSearchFilter v-model="searchFilesFilter" />
			<div class="messenger__messages-container">
				<div class="messenger__messages-search">
					<div class="messenger__messages-search-input">
						<ChatIconSearch />
						<input
							type="text"
							placeholder="Введите имя файла"
							v-model="searchFilesQuery"
						>
						<div
							v-show="searchFilesQuery"
							class="messenger__messages-search-clear ChatIcon-parent"
							@click="searchFilesQuery = ''"
						>
							<ChatIconSearchClose />
						</div>
					</div>
				</div>
				<div class="messenger__messages-search-results">
					<div
						v-for="(file, index) in chatSearchFilesResults"
						:key="index"
						class="messenger__message-wrapper"
						@click="goto(file, $event)"
					>
						<ConversationMessage :message="file" />
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import ConversationMessage from '../ConversationFeed/ConversationMessage/ConversationMessage.vue';
import ConversationHeader from '../ConversationHeader/ConversationHeader.vue';
import ConversationSearchFilter from './ConversationSearchFilter.vue';
import { ChatIconSearch, ChatIconSearchClose } from '../../icons/chat-icons.js'

export default {
	name: 'ConversationSearch',
	components: {
		ConversationMessage,
		ConversationHeader,
		ConversationSearchFilter,
		ChatIconSearch,
		ChatIconSearchClose,
	},
	data() {
		return {
			searchMessagesQuery: '',
			searchFilesQuery: '',
			searchMessagesDate: '',
			searchFilesFilter: '',
		}
	},
	computed: {
		...mapGetters([
			'chat',
			'chatSearchMessagesResults',
			'chatSearchFilesResults'
		]),
	},
	watch: {
		searchMessagesDate() {
			this.searchMessages();
		},
		searchMessagesQuery() {
			this.searchMessages();
		},
		searchFilesQuery() {
			this.searchFiles();
		},
	},
	methods: {
		...mapActions([
			'findMessagesInChat',
			'findFilesInChat',
			'loadMessages',
			'toggleChatSearchMode'
		]),
		goto(message, event) {
			event.stopPropagation();
			this.loadMessages({
				reset: false,
				goto: message.id
			});
			this.toggleChatSearchMode();
		},
		searchMessages() {
			this.findMessagesInChat({
				text: this.searchMessagesQuery,
				chat_id: this.chat.id,
				date: this.searchMessagesDate,
			});
		},
		searchFiles() {
			this.findFilesInChat({
				text: this.searchFilesQuery,
				chat_id: this.chat.id,
			});
		},
	}
}
</script>

<style scoped>

.messenger__container-scroll {
	height: 100%;
	display: flex;
	flex-flow: column;
	flex-direction: row;
	flex-wrap: wrap;
	flex: 1;

	position: relative;
	overflow: auto;
}

.messenger__messages-container {
	display: flex;
	height: 100%;
	width: 100%;
	min-width: 250px;
	flex: 1;
	flex-flow: column;
	flex-wrap: nowrap;
	position: relative;
	overflow: hidden;
	/* border: 1px solid #e8e7e7; */
}
.messenger__messages-container ~ .messenger__messages-container{
	border-left: 1px solid #E1EBF9;
}
.messenger__messages-container .messenger__chat-header_search{
	border-bottom: 2px solid #E1EBF9;
}

.messenger__messages-search {
	display: flex;
	flex-flow: row;
	padding: 10px;
	border-bottom: 0.5px solid #DAE5F3;
}

.messenger__messages-search-input {
	display: flex;
	flex-flow: row nowrap;
	align-items: center;
	flex: 1;
	background: rgba(235, 242, 250, 0.5);
	border-radius: 5px;
}
.messenger__messages-search-input .ChatIcon{
	margin: 0 10px;
}

.messenger__messages-search-input input {
	width: 100%;
	padding: 10px;
	background-color: transparent;
}

.messenger__messages-search-date {
	margin-left: 10px;
	flex-flow: row nowrap;
	align-items: center;
	/* flex: 1; */
	background: rgba(235, 242, 250, 0.5);
	border-radius: 5px;
}

.messenger__messages-search-date input {
	padding: 10px;
	border-radius: 5px;
	background-color: transparent;
}

.messenger__messages-search-results {
	max-width: 100%;
	flex-grow: 1;
	padding: 10px;
}
.messenger__messages-search-results_bg{
	background: url('../../../../../assets/chat/bg.jpg') repeat, #F7F8FA;
	background-size: cover;
}

.messenger__message-wrapper {
	cursor: pointer;
}

</style>
