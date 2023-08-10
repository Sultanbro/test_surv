<template>
	<div class="ConversationSearchMobile">
		<ConversationHeaderMobile
			@back="toggleChatSearchMode"
			@close="toggleChatSearchMode"
		/>

		<ConversationSearchFilter
			v-model="searchFilesFilter"
			class="ConversationSearchMobile-filters"
		/>

		<!-- Заголовок списка результатов -->
		<ConversationSearchTitle
			:title="title"
			class="px-2"
			@forward="forwardFiles"
			@delete="deleteFiles"
		/>

		<!-- Поиск -->
		<JobtronSearch
			v-if="searchFilesFilter != 'images'"
			v-model="searchMessagesQuery"
		/>

		<!-- Результаты -->
		<div class="ConversationSearchMobile-results messenger__messages-search-results px-2">
			<template v-if="searchFilesFilter === 'users'">
				<template v-for="(msg, index) in chatSearchMessagesResults">
					<div
						v-if="msg.body !== 'Event'"
						:key="index"
						class="messenger__message-wrapper mb-4"
						@click="goto(msg, $event)"
					>
						<ConversationMessage :message="msg" />
					</div>
				</template>
			</template>
			<template v-else>
				<div
					v-for="(file, index) in chatSearchFilesResults"
					:key="index"
					class="messenger__message-wrapper mb-4"
					@click="goto(file, $event)"
				>
					<ConversationMessage :message="file" />
				</div>
			</template>
		</div>
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import ConversationHeaderMobile from '../ConversationHeader/ConversationHeaderMobile.vue'
import ConversationSearchFilter from './ConversationSearchFilter.vue'
import ConversationSearchTitle from './ConversationSearchTitle.vue'
import ConversationMessage from '../ConversationFeed/ConversationMessage/ConversationMessage.vue'
import JobtronSearch from '@ui/Search'

const titles = {
	users: 'Пользователи',
	images: 'Фотографии',
	videos: 'Видео',
	docs: 'Файлы',
	music: 'Голосовые сообщения',
	links: 'Ссылки',
}

export default {
	name: 'ConversationSearchMobile',
	components: {
		ConversationHeaderMobile,
		ConversationSearchFilter,
		ConversationSearchTitle,
		ConversationMessage,
		JobtronSearch,
	},
	data(){
		return {
			searchFilesFilter: 'users',
			searchMessagesQuery: '',
			searchMessagesDate: '',
			isLoading: false,
		}
	},
	computed: {
		...mapGetters([
			'chat',
			'chatSearchMessagesResults',
			'chatSearchFilesResults',
		]),
		title(){
			return titles[this.searchFilesFilter]
		},
	},
	watch: {
		searchFilesFilter(){
			if(this.searchFilesFilter === 'images') this.searchMessagesQuery = ''
			this.search()
		},
		searchMessagesQuery(){
			this.search()
		}
	},
	mounted(){
		this.search()
	},
	methods: {
		...mapActions([
			'findFilesInChat',
			'findMessagesInChat',
			'loadMessages',
			'toggleChatSearchMode',
		]),
		forwardFiles(){},
		deleteFiles(){},
		async search(){
			if(this.isLoading) return
			this.isLoading = true
			if(this.searchFilesFilter === 'users'){
				await this.findMessagesInChat({
					text: this.searchMessagesQuery,
					chat_id: this.chat.id,
					date: this.searchMessagesDate,
				})
			}
			else{
				await this.findFilesInChat({
					text: this.searchMessagesQuery,
					chat_id: this.chat.id,
				})
			}
			this.isLoading = false
		},
		goto(message, event) {
			event.stopPropagation();
			this.loadMessages({
				reset: false,
				goto: message.id
			});
			this.toggleChatSearchMode();
		},
	}
}
</script>

<style lang="scss">
.ConversationSearchMobile{
	display: flex;
	flex-flow: column nowrap;
	align-items: stretch;

	position: fixed;
	z-index: 100;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;

	background-color: #fff;

	&-icon{
		display: inline-flex;
		align-items: center;
		justify-content: center;

		padding: 0.5rem;
	}
	&-filters{
		margin-bottom: 1rem;
	}
	&-results{
		overflow-y: auto;
	}
}
</style>
