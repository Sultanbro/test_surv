<template>
  <div class="messenger__box-search">
    <div class="messenger__box-search__input" v-if="isOpen">
      <div class="messenger__icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
          <path
            d="m1253.89 164.337-3.16-3.159a5.585 5.585 0 1 0-.55.547l3.16 3.16a.375.375 0 0 0 .27.115.4.4 0 0 0 .28-.115.39.39 0 0 0 0-.548Zm-12.11-6.794a4.765 4.765 0 1 1 4.76 4.768 4.763 4.763 0 0 1-4.76-4.768Z"
            id="messenger__icon-search" transform="translate(-1241 -147)"/>
        </svg>
      </div>
      <input
        id="messenger_search_input"
        type="text"
        placeholder="Поиск"
        v-model="searchString"
        @keyup.enter="search"
      />
      <div v-if="connectionError" class="messenger__connection-indicator">
        Нет подключения
      </div>
    </div>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";

export default {
  name: "SearchBox",
  computed: {
    ...mapGetters(['isOpen', 'isInitialized', 'unreadCount', 'searchType', 'isSearchFocus', 'isSocketConnected']),
  },
  mounted() {
    if (this.isSearchFocus) {
      this.focus()
    }
    if (!this.isSocketConnected) {
      this.socketError();
    }
  },
  data() {
    return {
      searchString: '',
      connectionError: false,
    }
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
  methods: {
    ...mapActions(['findContacts', 'findMessages', 'setSearchFocus']),
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
  position: sticky;
  display: flex;
  align-items: center;
  height: 50px;
  padding: 0 5px;
  border-bottom: 1px solid #c6c6c6;
  font-size: 16px;
}

.messenger__box-search__input {
  width: 100%;
  max-width: 400px;
  height: 35px;
  display: flex;
  justify-content: center;
  align-items: center;
  padding-left: 10px;
  border: 1px solid #c6c6c6;
  border-radius: 10px;
}

.messenger__box-search__input input {
  width: 100%;
  height: 30px;
  border: none;
}

.messenger__card-window textarea, .messenger__card-window input[type=text], .messenger__card-window input[type=search] {
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

.messenger__icon svg {
  width: 28px;
  height: 28px;
}

.messenger__icon svg:hover {
  fill: #000;
}

.messenger__connection-indicator {
  margin-right: 10px;
  color: #ff0000;
}

</style>
