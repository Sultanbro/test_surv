<template>
  <div>
    <span class="textarea-app-placeholder" v-if="mode === 'edit'">Редактирование: {{ selectedMessage.body | truncate(50, '...') }}</span>
    <div class="composer">
<!--      <label title="Отправить файл" class="textarea-app-button textarea-app-file">-->
<!--        <input type="file" multiple="multiple">-->
<!--      </label>-->

      <textarea
        v-model="message"
        placeholder="Ввести сообщение..."
        ref="messageInput"
        @keydown.enter="send"
      />

      <button @click="send"
              title="Отправить сообщение"
              class="textarea-send-button textarea-send-button-bright-arrow send-message-button"></button>

    </div>
  </div>
</template>

<script>
export default {
  props: {
    mode: {
      type: String,
      default: 'send',
    },
    selectedMessage: {
      type: Object,
      default: '',
    },
  },
  data() {
    return {
      message: '',
    };
  },
  watch: {
    selectedMessage: function(val) {
      this.focus(val);
    },
    mode: function(val) {
      if (val === 'edit') {
        this.focus(this.selectedMessage);
      }
    },
  },
  methods: {
    focus(val) {
      if (val) {
        this.message = val.body;
        this.$nextTick(() => this.$refs.messageInput.focus())
      } else {
        this.message = '';
      }
    },
    send: function (e) {
      e.preventDefault();

      if (this.mode === 'edit') {
        if (this.message === '') {
          this.mode = 'send';
          return;
        }
        this.$emit(this.mode, this.message);

      } else if (this.mode === 'send') {
        if (this.message === '') {
          return;
        }
        this.$emit(this.mode, this.message);
      }

      this.message = '';
    },
  },
  filters: {
    truncate: function (text, length, suffix) {
      if (text && text.length > length) {
        return text.substring(0, length) + suffix;
      } else {
        return text;
      }
    },
  }
};
</script>

<style>
.composer {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
  border-top: 1px solid #e6e6e6;
  border-bottom: 1px solid #e6e6e6;
}

.composer textarea {
  width: 96%;
  margin: 10px;
  resize: none;
  border-radius: 3px;
  border: 1px solid lightgray;
  padding: 6px;
}

.textarea-app-placeholder {
  color: #6ab3f3;
  font-size: 12px;
  margin-left: 10px;
}

.textarea-app-button {
  display: inline-block;
  width: 25px;
  height: 25px;
  border: 0;
  background-color: transparent;
  background-position: center;
  background-repeat: no-repeat;
  -webkit-transition: opacity .3s;
  -o-transition: opacity .3s;
  transition: opacity .3s;
  -webkit-appearance: none;
  outline: 0;
  cursor: pointer;
  vertical-align: top;
}

.textarea-app-file > input {
  display: none !important;
}

.textarea-app-file {
  background-image: url(data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTYiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTQuMDE5IDBjMS45NSAwIDMuMTI5IDEuMjcxIDMuMTI4IDIuODUzdjYuODQ0Yy4wMDIgMS4yMjMtLjc3MyAyLjE1LTIuMTE1IDIuMTM5LTEuMzU4LS4wMTItMi4xMDItMS4wMS0yLjA5Ni0yLjMyMlY1Ljc0OWEuNjUuNjUgMCAxMTEuMy0uMDAyVjkuNTJjLS4wMDMuNjYzLjI1NyAxLjAxMi44MDcgMS4wMTcuNTYuMDA1LjgwNS0uMjg5LjgwNC0uODM3VjIuODUzYzAtLjg4NS0uNjItMS41NTMtMS44MjgtMS41NTMtMS4yMzYgMC0xLjkyOS42OTktMS45MzIgMS41NTF2OC41MDNjLjAwOCAxLjU3NiAxLjA2MyAyLjY2MyAzLjAyMiAyLjY5MyAyLjIzNC4wMzMgMi45NDYtMS4wMTkgMi45OTMtMy40ODdsLjAwMy0uMjM2Yy4wMDMtMS40My4wMDMtMi43NyAwLTQuMDJhLjY1LjY1IDAgMDExLjMtLjAwM2wuMDAyIDEuOTQ2YzAgLjY3IDAgMS4zNjQtLjAwMiAyLjA4LS4wMDggMy4yNjctMS4xMSA1LjA2OC00LjMxNSA1LjAyLTIuNjczLS4wNC00LjI5MS0xLjcwOC00LjMwMy0zLjk4N1YyLjg0N0MuNzkzIDEuMjggMi4wNjMgMCA0LjAxOSAweiIgZmlsbD0iI0I1QkFCRSIvPjwvc3ZnPg==);
}

.textarea-app-smile {
  background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg width= '16' height= '15' xmlns= 'http://www.w3.org/2000/svg' %3E%3Cpath d= 'M8.14 13.864a6.364 6.364 0 100-12.728 6.364 6.364 0 000 12.728zm0 1.136a7.5 7.5 0 110-15 7.5 7.5 0 010 15zm3.504-5.8c.194.197.191.576-.006.77a4.985 4.985 0 01-3.505 1.434 4.984 4.984 0 01-3.49-1.42c-.198-.192-.202-.571-.01-.769.193-.198.51-.364.708-.171a3.985 3.985 0 002.792 1.135c1.064 0 2.06-.416 2.804-1.147.197-.194.513-.029.707.168zM5.89 4a1 1 0 011 1v1a1 1 0 01-2 0V5a1 1 0 011-1zm4.5 0a1 1 0 011 1v1a1 1 0 01-2 0V5a1 1 0 011-1z' fill= '%23B5BABE' /%3E%3C/svg%3E");
}

.button-app-send-message {
  background-color: rgb(142, 200, 47);
  cursor: pointer;
}

.button-bright-arrow {
  border-color: transparent;
  background-image: url(data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%2212%22%20height%3D%2213%22%3E%3Cpath%20fill%3D%22%23FFF%22%20fill-rule%3D%22evenodd%22%20d%3D%22M4.059%209.152L4.04%2012.21.165%208.333l3.92-3.92-.018%203.245h2.862a3%203%200%200%200%203-3v-.156a3%203%200%200%200-3-3H5.545L6.06.286h1.38a4%204%200%200%201%204%204v.866a4%204%200%200%201-4%204H4.06z%22/%3E%3C/svg%3E);
}

.textarea-send-button {
  display: block;
  width: 33px;
  height: 33px;
  background-color: transparent;
  background-image: url(data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%2212%22%20height%3D%2213%22%3E%3Cpath%20fill%3D%22%23646D7B%22%20fill-rule%3D%22evenodd%22%20d%3D%22M4.059%209.152L4.04%2012.21.165%208.333l3.92-3.92-.018%203.245h2.862a3%203%200%200%200%203-3v-.156a3%203%200%200%200-3-3H5.545L6.06.286h1.38a4%204%200%200%201%204%204v.866a4%204%200%200%201-4%204H4.06z%22%20opacity%3D%22.8%22/%3E%3C/svg%3E);
  background-position: center center;
  background-repeat: no-repeat;
  opacity: .5;
  -webkit-transition: opacity .3s;
  -o-transition: opacity .3s;
  transition: opacity .3s;
  -webkit-appearance: none;
  outline: 0;
  cursor: pointer;
  border-radius: 50%;
  border: 1px solid #b1b1b1;
  -ms-flex-negative: 0;
  flex-shrink: 0;
}
</style>
