<template>
	<div
		class="ConversationSearch"
		@click.self="toggleChatSearchMode"
	>
		<div class="messenger__container-scroll">
			<div
				class="messenger__messages-container"
				style="flex-grow: 3"
			>
				<ConversationHeader
					:actions="false"
				/>
				<div class="messenger__messages-search">
					<div class="messenger__messages-search-input">
						<ChatIconSearch />
						<input
							v-model="searchMessagesQuery"
							type="text"
							placeholder="Введите текст для поиска"
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
							v-model="searchMessagesDate"
							v-mask="'##.##.####'"
							class="messenger__messages-search-date-input"
						>
						<div
							class="messenger__messages-search-date-calendar ChatIcon-parent"
							@click.stop="showDatePicker = !showDatePicker"
						>
							<ChatIconSearchDate />
						</div>
						<CalendarInput
							v-if="showDatePicker"
							v-model="date"
							:open="showDatePicker"
							:tabs="['Текущий месяц', 'Прошлый месяц']"
							@close="showDatePicker = false"
						/>
					</div>
				</div>
				<div class="messenger__messages-search-results messenger__messages-search-results_bg">
					<template v-for="(message, index) in chatSearchMessagesResults">
						<div
							v-if="message.body !== 'Event'"
							:key="index"
							class="messenger__message-wrapper"
							@click="goto(message, $event)"
						>
							<ConversationMessage :message="message" />
						</div>
					</template>
				</div>
			</div>
			<div class="messenger__messages-container">
				<ConversationSearchFilter v-model="searchFilesFilter" />
				<div class="messenger__messages-container">
					<div class="messenger__messages-search">
						<div class="messenger__messages-search-input">
							<ChatIconSearch />
							<input
								v-model="searchFilesQuery"
								type="text"
								placeholder="Введите имя файла"
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
					<div class="ConversationSearch-files">
						<ConversationSearchFile
							v-for="(file, index) in chatSearchFilesFiles"
							:key="index"
							:file="file"
						/>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import {mask} from 'vue-the-mask'
import {mapActions, mapGetters} from 'vuex';
import ConversationMessage from '../ConversationFeed/ConversationMessage/ConversationMessage.vue';
import ConversationHeader from '../ConversationHeader/ConversationHeader.vue';
import ConversationSearchFilter from './ConversationSearchFilter.vue';
import ConversationSearchFile from './ConversationSearchFile.vue';
import CalendarInput from '@/components/ui/CalendarInput/CalendarInput.vue'
import {
	ChatIconSearch,
	ChatIconSearchClose,
	ChatIconSearchDate,
} from '@icons'

export default {
	name: 'ConversationSearch',
	components: {
		ConversationMessage,
		ConversationHeader,
		ConversationSearchFilter,
		ConversationSearchFile,
		CalendarInput,
		ChatIconSearch,
		ChatIconSearchClose,
		ChatIconSearchDate,
	},
	directives: {mask},
	data() {
		return {
			searchMessagesQuery: '',
			searchFilesQuery: '',
			searchMessagesDate: '',
			searchFilesFilter: '',
			showDatePicker: false,
			date: ['']
		}
	},
	computed: {
		...mapGetters([
			'chat',
			'chatSearchMessagesResults',
			'chatSearchFilesResults',
			'chatSearchFilesFiles',
			'isChatSearchMode',
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
		chat(value){
			if(value){
				this.searchFiles()
				this.searchMessages()
			}
		},
		date(value){
			this.searchMessagesDate = value[0]
		},
		isChatSearchMode(value){
			if(value){
				this.searchFiles()
				this.searchMessages()
			}
		}
	},
	methods: {
		...mapActions([
			'findMessagesInChat',
			'findFilesInChat',
			'loadMessages',
			'toggleChatSearchMode',
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
			if(!this.chat) return
			const mement = this.$moment(this.searchMessagesDate, 'DD.MM.YYYY')
			this.findMessagesInChat({
				text: this.searchMessagesQuery,
				/* eslint-disable-next-line camelcase */
				chat_id: this.chat.id,
				month: +mement.format('M'),
				year: +mement.format('YYYY'),
			});
		},
		searchFiles() {
			if(!this.chat) return
			this.findFilesInChat({
				text: '' + this.searchFilesQuery,
				/* eslint-disable-next-line camelcase */
				chat_id: this.chat.id,
			});
		},
	},
}
</script>

<style scoped>
.ConversationSearch{
	width: 100vw;
	height: 100vh;
	position: fixed;
	z-index: 20;
	right: 0;
	bottom: 0;
	background-color: rgba(0, 0, 0, 0.25);
}
.ConversationSearch-files{
	padding: 10px 0;
	overflow-y: auto;
}
/* .ConversationSearch-files .ConversationSearchFile{
	padding: 10px 30px;
} */

.messenger__container-scroll {
	width: 50%;
	height: 100%;
	margin-left: auto;

	display: flex;
	flex-flow: column;
	flex-direction: row;
	flex-wrap: wrap;
	flex: 1;

	position: relative;
	background-color: #fff;
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
	gap: 1rem;
	padding: 1rem;
	border-bottom: 0.5px solid #DAE5F3;
}

.messenger__messages-search-input {
	display: flex;
	flex-flow: row nowrap;
	align-items: center;
	flex: 1;
	background: rgba(235, 242, 250, 0.5);
	border-radius: 0.5rem;
}
.messenger__messages-search-input .ChatIcon{
	margin: 0 1rem;
}

.messenger__messages-search-input input {
	width: 100%;
	padding: 1rem;
	background-color: transparent;
}

.messenger__messages-search-date {
	display: flex;
	flex-flow: row nowrap;
	align-items: center;
	/* flex: 1; */
	position: relative;
	background: rgba(235, 242, 250, 0.5);
	border-radius: 0.5rem;
}

.messenger__messages-search-date-input {
	padding: 10px;
	border-radius: 5px;
	background-color: transparent;
}
.messenger__messages-search-date-input::-webkit-inner-spin-button,
.messenger__messages-search-date-input::-moz-inner-spin-button,
.messenger__messages-search-date-input::-webkit-calendar-picker-indicator,
.messenger__messages-search-date-input::-moz-calendar-picker-indicator{
	display: none;
	-webkit-appearance: none;
	-moz-appearance: none;
}
.messenger__messages-search-date-calendar{
	padding: 0 0.5rem;
}

.messenger__messages-search-results {
	flex: 1;

	max-width: 100%;
	padding: 10px;

	overflow-y: auto;
}
.messenger__messages-search-results_bg{
	background: url('../../../../../assets/chat/bg.jpg') repeat, #F7F8FA;
	background-size: cover;
}

.messenger__message-wrapper {
	cursor: pointer;
}

</style>
