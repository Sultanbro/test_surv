<template>
	<emoji-picker
		:search="search"
		@emoji="append"
	>
		<template #emoji-invoker="{ events: { click: clickEvent } }">
			<div
				class="messenger__message-icon messenger__message-icon_hover"
				@click.stop="clickEvent"
			>
				<svg
					xmlns="http://www.w3.org/2000/svg"
					width="16"
					height="16"
					viewBox="0 0 16 16"
				>
					<path
						fill="#5ebee9"
						d="M8 1c3.9 0 7 3.1 7 7s-3.1 7-7 7-7-3.1-7-7 3.1-7 7-7zM8 0c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8v0z"
					/>
					<path
						fill="#5ebee9"
						d="M8 13.2c-2 0-3.8-1.2-4.6-3.1l0.9-0.4c0.6 1.5 2.1 2.4 3.7 2.4s3.1-1 3.7-2.4l0.9 0.4c-0.8 2-2.6 3.1-4.6 3.1z"
					/>
					<path
						fill="#5ebee9"
						d="M7 6c0 0.552-0.448 1-1 1s-1-0.448-1-1c0-0.552 0.448-1 1-1s1 0.448 1 1z"
					/>
					<path
						fill="#5ebee9"
						d="M11 6c0 0.552-0.448 1-1 1s-1-0.448-1-1c0-0.552 0.448-1 1-1s1 0.448 1 1z"
					/>
				</svg>
			</div>
		</template>
		<template #emoji-picker="{ emojis, insert }">
			<div>
				<div
					class="emoji-picker"
					:style="{ bottom: 5 + '%', right: 10 + '%' }"
				>
					<div>
						<div
							v-for="(emojiGroup, category) in emojis"
							:key="category"
						>
							<h5>{{ category }}</h5>
							<div class="emojis">
								<span
									v-for="(emoji, emojiName) in emojiGroup"
									:key="emojiName"
									:title="emojiName"
									@click="insert(emoji)"
								>{{ emoji }}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</template>
	</emoji-picker>
</template>

<script>
import EmojiPicker from './EmojiPicker/EmojiPicker.vue';

export default {
	name: 'EmojiPopup',
	components: {EmojiPicker},
	data() {
		return {
			search: '',
		};
	},
	methods: {
		append(emoji) {
			this.$emit('append', emoji);
		}
	}
};
</script>

<style scoped>

.wrapper {
  position: relative;
  display: inline-block;
}

.regular-input {
  padding: 0.5rem 1rem;
  border-radius: 3px;
  border: 1px solid #ccc;
  width: 20rem;
  height: 12rem;
  outline: none;
}

.regular-input:focus {
  box-shadow: 0 0 0 3px rgba(66, 153, 225, .5);
}

.emoji-invoker {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  width: 1.5rem;
  height: 1.5rem;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.2s;
  padding: 0;
  background: transparent;
  border: 0;
}

.emoji-invoker:hover {
  transform: scale(1.1);
}

.emoji-invoker > svg {
  fill: #b1c6d0;
}

.emoji-picker {
  position: fixed;
  z-index: 1000;
  font-family: Montserrat,serif;
  border: 1px solid #ccc;
  width: 300px;
  height: 20rem;
  overflow: scroll;
  padding: 1rem;
  box-sizing: border-box;
  border-radius: 0.5rem;
  background: #fff;
  box-shadow: 1px 1px 8px #c7dbe6;
}

.emoji-picker__search {
  display: flex;
}

.emoji-picker__search > input {
  flex: 1;
  border-radius: 10rem;
  border: 1px solid #ccc;
  padding: 0.5rem 1rem;
  outline: none;
}

.emoji-picker h5 {
  margin-bottom: 0;
  color: #b1b1b1;
  text-transform: uppercase;
  font-size: 0.8rem;
  cursor: default;
}

.emoji-picker .emojis {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
}

.emoji-picker .emojis:after {
  content: "";
  flex: auto;
}

.emoji-picker .emojis span {
  font-size: 22px;
  cursor: pointer;
  border-radius: 5px;
}

.emoji-picker .emojis span:hover {
  background: #ececec;
  cursor: pointer;
}

</style>
