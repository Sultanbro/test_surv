<template>
  <div class="messenger__box-search">
    <div class="messenger__box-search__indicators">
      <div class="messenger__icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="-300 -250 800 800">
          <g>
            <path
              d="M438.818 96.071H15c-8.284 0-15 6.716-15 15v231.676c0 8.284 6.716 15 15 15h423.818c8.284 0 15-6.716 15-15V111.071C453.818 102.787 447.103 96.071 438.818 96.071z M30 133.089l140.736 93.819L30 320.733V133.089z M55.533 327.747l133.231-88.821l32.598 21.731c1.68 1.12 3.613 1.679 5.547 1.679s3.867-0.56 5.547-1.679l32.601-21.733l133.233 88.822H55.533z M226.909 240.319L55.53 126.071h342.759L226.909 240.319z M283.085 226.907l140.734-93.818v187.64L283.085 226.907z"/>
          </g>
        </svg>
        <span class="messenger__counter" v-text="unreadCount"></span>
      </div>
      <div v-if="isSearchMessagesMode" @click="toggleSearch()" class="messenger__icon messenger__icon-clickable">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="-150 -150 800 800">
          <g>
            <path
              d="M239.7,260.2c0.5,0,1,0,1.6,0c0.2,0,0.4,0,0.6,0c0.3,0,0.7,0,1,0c29.3-0.5,53-10.8,70.5-30.5    c38.5-43.4,32.1-117.8,31.4-124.9c-2.5-53.3-27.7-78.8-48.5-90.7C280.8,5.2,262.7,0.4,242.5,0h-0.7c-0.1,0-0.3,0-0.4,0h-0.6    c-11.1,0-32.9,1.8-53.8,13.7c-21,11.9-46.6,37.4-49.1,91.1c-0.7,7.1-7.1,81.5,31.4,124.9C186.7,249.4,210.4,259.7,239.7,260.2z     M164.6,107.3c0-0.3,0.1-0.6,0.1-0.8c3.3-71.7,54.2-79.4,76-79.4h0.4c0.2,0,0.5,0,0.8,0c27,0.6,72.9,11.6,76,79.4    c0,0.3,0,0.6,0.1,0.8c0.1,0.7,7.1,68.7-24.7,104.5c-12.6,14.2-29.4,21.2-51.5,21.4c-0.2,0-0.3,0-0.5,0l0,0c-0.2,0-0.3,0-0.5,0    c-22-0.2-38.9-7.2-51.4-21.4C157.7,176.2,164.5,107.9,164.6,107.3z"/>
            <path
              d="M446.8,383.6c0-0.1,0-0.2,0-0.3c0-0.8-0.1-1.6-0.1-2.5c-0.6-19.8-1.9-66.1-45.3-80.9c-0.3-0.1-0.7-0.2-1-0.3    c-45.1-11.5-82.6-37.5-83-37.8c-6.1-4.3-14.5-2.8-18.8,3.3c-4.3,6.1-2.8,14.5,3.3,18.8c1.7,1.2,41.5,28.9,91.3,41.7    c23.3,8.3,25.9,33.2,26.6,56c0,0.9,0,1.7,0.1,2.5c0.1,9-0.5,22.9-2.1,30.9c-16.2,9.2-79.7,41-176.3,41    c-96.2,0-160.1-31.9-176.4-41.1c-1.6-8-2.3-21.9-2.1-30.9c0-0.8,0.1-1.6,0.1-2.5c0.7-22.8,3.3-47.7,26.6-56    c49.8-12.8,89.6-40.6,91.3-41.7c6.1-4.3,7.6-12.7,3.3-18.8c-4.3-6.1-12.7-7.6-18.8-3.3c-0.4,0.3-37.7,26.3-83,37.8    c-0.4,0.1-0.7,0.2-1,0.3c-43.4,14.9-44.7,61.2-45.3,80.9c0,0.9,0,1.7-0.1,2.5c0,0.1,0,0.2,0,0.3c-0.1,5.2-0.2,31.9,5.1,45.3    c1,2.6,2.8,4.8,5.2,6.3c3,2,74.9,47.8,195.2,47.8s192.2-45.9,195.2-47.8c2.3-1.5,4.2-3.7,5.2-6.3    C447,415.5,446.9,388.8,446.8,383.6z"/>
          </g>
        </svg>
      </div>
      <div v-if="isSearchContactsMode" @click="toggleSearch()" class="messenger__icon messenger__icon-clickable">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="-300 -250 800 800">
          <g>
            <path
              d="M438.818 96.071H15c-8.284 0-15 6.716-15 15v231.676c0 8.284 6.716 15 15 15h423.818c8.284 0 15-6.716 15-15V111.071C453.818 102.787 447.103 96.071 438.818 96.071z M30 133.089l140.736 93.819L30 320.733V133.089z M55.533 327.747l133.231-88.821l32.598 21.731c1.68 1.12 3.613 1.679 5.547 1.679s3.867-0.56 5.547-1.679l32.601-21.733l133.233 88.822H55.533z M226.909 240.319L55.53 126.071h342.759L226.909 240.319z M283.085 226.907l140.734-93.818v187.64L283.085 226.907z"/>
          </g>
        </svg>
      </div>
    </div>
    <div class="messenger__box-search__input" v-if="isOpen">
      <div class="messenger__icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
          <path
            d="m1253.89 164.337-3.16-3.159a5.585 5.585 0 1 0-.55.547l3.16 3.16a.375.375 0 0 0 .27.115.4.4 0 0 0 .28-.115.39.39 0 0 0 0-.548Zm-12.11-6.794a4.765 4.765 0 1 1 4.76 4.768 4.763 4.763 0 0 1-4.76-4.768Z"
            id="messenger__icon-search" transform="translate(-1241 -147)"/>
        </svg>
      </div>
      <input
        type="text"
        :placeholder="isSearchContactsMode ? 'Поиск сотрудников' : 'Поиск'"
        v-model="searchString"
        @keyup.enter="searchUser"
      />
    </div>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";

export default {
  name: "SearchBox",
  computed: {
    ...mapGetters(['isOpen', 'unreadCount', 'searchType', 'isSearchMessagesMode', 'isSearchContactsMode']),
  },
  data() {
    return {
      searchString: '',
    }
  },
  watch: {
    searchString() {
      if (this.isSearchMessagesMode) {
        this.findMessages(this.searchString);
      } else if (this.isSearchContactsMode) {
        this.findContacts(this.searchString);
      }
    }
  },
  methods: {
    ...mapActions(['findContacts', 'findMessages']),
    toggleSearch() {
      if (this.searchType === 'contacts') {
        this.$store.commit('setSearchType', 'messages');
      } else {
        this.$store.commit('setSearchType', 'contacts');
      }
    },
  }
}
</script>

<style scoped>

.messenger__box-search {
  position: sticky;
  display: flex;
  align-items: center;
  height: 64px;
  padding: 0 5px;
  border-bottom: 1px solid #c6c6c6;
  font-size: 16px;
}

.messenger__box-search__input {
  width: 100%;
  height: 50px;
  display: flex;
  justify-content: center;
  align-items: center;
  border-left: 2px solid #d5d5d5;
  padding-left: 10px;
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

.messenger__box-search__indicators {
  display: flex;
  justify-content: space-around;
  max-width: 100%;
}

.messenger__icon {
  display: flex;
  left: 30px;
  margin: 0 5px;
}

.messenger__icon svg {
  width: 28px;
  height: 28px;
}

.messenger__icon svg:hover {
  fill: #000;
}

.messenger__icon-clickable {
  cursor: pointer;
}

.messenger__counter {
  font-size: 11px;
}

</style>
