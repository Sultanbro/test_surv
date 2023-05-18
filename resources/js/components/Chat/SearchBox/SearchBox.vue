<template>
	<div
		v-if="isOpen"
		class="messenger__box-search"
	>
		<label class="messenger__box-search__input">
			<div class="messenger__icon">
				<ChatIconSearch />
			</div>
			<input
				id="messenger_search_input"
				class="messenger_search_input"
				type="text"
				placeholder="Поиск"
				v-model="searchString"
				@keyup.enter="search"
			>
			<div
				v-if="connectionError"
				class="messenger__connection-indicator"
			>
				Нет подключения
			</div>
		</label>
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import { ChatIconSearch } from '@icons'

export default {
	name: 'SearchBox',
	components: {
		ChatIconSearch,
	},
	data() {
		return {
			searchString: '',
			connectionError: false,
		}
	},
	computed: {
		...mapGetters([
			'isOpen',
			'isInitialized',
			'unreadCount',
			'searchType',
			'isSearchFocus',
			'isSocketConnected'
		]),
	},
	watch: {
		searchString() {
			this.search()
		},
		isSearchFocus() {
			if (this.isSearchFocus) {
				this.focus();
			}
		},
		isSocketConnected() {
			if (!this.isSocketConnected) {
				this.socketError();
			} else {
				this.connectionError = false;
			}
		}
	},
	mounted() {
		if (this.isSearchFocus) {
			this.focus()
		}
		if (!this.isSocketConnected) {
			this.socketError();
		}
	},
	methods: {
		...mapActions([
			'findContacts',
			'findMessages',
			'setSearchFocus'
		]),
		focus() {
			this.$nextTick(() => {
				document.getElementById('messenger_search_input').focus();
				this.setSearchFocus(false);
			});
		},
		search() {
			this.findMessages(this.searchString);
			this.findContacts(this.searchString);
		},
		socketError() {
			// wait 5 seconds before showing connection error
			setTimeout(() => {
				if (!this.isSocketConnected) {
					this.connectionError = true;
				}
			}, 5000);
		}
	}
}
</script>

<style scoped>

.messenger__box-search {
  display: flex;
  height: 45px;

  align-items: center;

  position: sticky;

  font-size: 16px;
}

.messenger__box-search__input {
  display: flex;
  width: 100%;
  max-width: 400px;
  height: 45px;
  padding-left: 10px;

  justify-content: center;
  align-items: center;
	background: rgba(235, 242, 250, 0.5);
	border-radius: 5px;
}

.messenger_search_input {
  width: 100%;
  height: 30px;
	padding: 0 0.5rem;
  border: none;
	background: none;

	font-weight: 400;
	font-size: 13px;
	line-height: 15px;

	letter-spacing: -0.02em;

	color: #8BABD8;
}

.messenger__card-window textarea,
.messenger__card-window input[type=text],
.messenger__card-window input[type=search] {
  -webkit-appearance: none;
}

.messenger__box-search__input input:focus {
  outline: none;
  -webkit-box-shadow: none;
  box-shadow: none;
}

.messenger__card-window .messenger__chat-container input {
  min-width: 10px;
}

.messenger__icon {
  display: flex;
  left: 30px;
}

.messenger__connection-indicator {
  margin-right: 10px;
  color: #ff0000;
  font-size: 1.2rem;
}

</style>
