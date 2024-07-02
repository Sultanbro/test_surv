<template>
	<div class="readers-badge">
		✔ Cообщение прочитано: {{ message.readers[0].name }}
		<a
			v-if="message.readers.length > 1"
			href="javascript:"
			class="readers-link"
			@mouseover="showReadersList"
			@mouseleave="hideReadersList"
		>
			и еще {{ message.readers.length - 1 }} человек
		</a>

		<div
			v-if="showReaders"
			id="messenger_readers_list"
			class="readers-list"
			:style="{top:listY + 'px',left:listX+'px'}"
		>
			<div class="readers-list-body">
				<div
					v-for="(reader, index) in message.readers"
					:key="index"
					class="readers-list-body-text"
				>
					<!--          avatar -->
					<div class="readers-list-body-text-avatar">
						<img
							:src="reader.img_url"
							alt="avatar"
						>
					</div>
					<span>{{ reader.name }} {{ reader.last_name }}</span>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		message: {
			type: Object,
			required: true,
		},
		user: {
			type: Object,
			required: true,
		},
	},
	data: function () {
		return {
			showReaders: false,
			listX: 0,
			listY: 0,
		};
	},
	methods: {
		showReadersList: function (event) {
			this.showReaders = true;
			this.listX = event.clientX - 200;
			this.listY = event.clientY - this.message.readers.length * 40;
		},
		hideReadersList: function () {
			this.showReaders = false;
		},
	},
}
</script>

<style scoped>
.readers-badge {
  display: block;
  font-size: 12px;
  color: #a0a0a4;
  margin-top: 5px;
}

.readers-link {
  color: #3434b7;
  text-decoration: none;
  cursor: pointer;
}

.readers-list {
  width: 200px;
  position: fixed;
  background-color: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 5px;
  padding: 5px;
  z-index: 100;
}

.readers-list-body {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: flex-start;
}

.readers-list-body-text {
  display: flex;
  align-items: center;
  font-size: 14px;
  color: #000;
}

.readers-list-body-text-avatar {
  display: inline-block;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
  margin-right: 10px;
}

.readers-list-body-text-avatar img {
  width: 100%;
  height: 100%;
}
</style>
