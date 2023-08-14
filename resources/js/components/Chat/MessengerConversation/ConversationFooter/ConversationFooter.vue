<template>
	<div class="messenger__chat-footer ConversationFooter">
		<vue-draggable-resizable
			:w="'auto'"
			:h="localState.height"
			:max-height="250"
			:min-height="48"
			:handles="['tm']"
			:draggable="false"
			:prevent-deactivation="true"
			@resizestop="onResizeStop"
		>
			<div class="messenger__box-footer messenger__box-footer-border">
				<SpectrumAnalyser
					v-if="isRecordingAudio"
					class="messenger__chat-footer_spectrum"
					:fill-style="'#5ebee9'"
				/>
				<template v-else>
					<div class="messenger__message-input messenger__message-text-input">
						<div
							v-if="citedMessage"
							class="messenger__message-input_cite"
						>
							<div class="messenger__message-input_cite-body">
								{{ citedMessage.sender.name }}: {{ citedMessage.body }}
							</div>
							<span @click="closeCitation">x</span>
						</div>
						<textarea
							id="messengerMessageInput"
							v-model="body"
							class="messenger__textarea"
							placeholder="Ввести сообщение"
							@keydown.enter="performMessage"
							@paste="pasteMessage"
						/>
					</div>
					<label class="messenger__attachment">
						<input
							type="file"
							style="display:none"
							multiple="multiple"
							@change="prepareFiles"
						>
						<ChatIconUpload class="mt-2" />
					</label>
				</template>
				<EmojiPopup @append="appendEmoji" />
				<div
					id="messengerInput"
					class="messenger__message-input"
				>
					<div
						v-if="(body || (files && files.length)) && !isRecordingAudio"
						class="messenger__message-icon"
						@click="performMessage"
					>
						<ChatIconSend />
					</div>
					<AudioDictaphone
						v-else
						class="messenger__record"
						@stop="handleRecording"
						@error="handleError"
						@start="setRecordingAudio(true)"
						@delete="setRecordingAudio(false)"
					>
						<template #default="{ isRecording, startRecording, stopRecording, deleteRecording }">
							<template v-if="!isRecording">
								<div
									class="messenger__record-item"
									@click="startRecording"
								>
									<svg
										xmlns="http://www.w3.org/2000/svg"
										viewBox="0 0 384 512"
									>
										<path
											fill="#5ebee9"
											d="M192 0C139 0 96 43 96 96V256c0 53 43 96 96 96s96-43 96-96V96c0-53-43-96-96-96zM64 216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 89.1 66.2 162.7 152 174.4V464H120c-13.3 0-24 10.7-24 24s10.7 24 24 24h72 72c13.3 0 24-10.7 24-24s-10.7-24-24-24H216V430.4c85.8-11.7 152-85.3 152-174.4V216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 70.7-57.3 128-128 128s-128-57.3-128-128V216z"
										/>
									</svg>
								</div>
							</template>
							<template v-else>
								<div
									class="messenger__record-item"
									@click="stopRecording($event)"
								>
									<svg
										xmlns="http://www.w3.org/2000/svg"
										viewBox="0 0 384 512"
									>
										<path
											fill="#5ebee9"
											d="M0 128C0 92.7 28.7 64 64 64H320c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128z"
										/>
									</svg>
								</div>
								<div
									class="messenger__record-item"
									@click="deleteRecording($event)"
								>
									<svg
										xmlns="http://www.w3.org/2000/svg"
										viewBox="0 0 320 512"
									>
										<path
											fill="#5ebee9"
											d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z"
										/>
									</svg>
								</div>
							</template>
						</template>
					</AudioDictaphone>
					<span
						v-if="isRecordingAudio"
						class="messenger__record_recording"
					>
						{{ recordingTime | countdownFormat }}
					</span>
				</div>
			</div>
		</vue-draggable-resizable>
		<template v-if="files && files.length">
			<div
				v-for="({file, preview}, index) in files"
				:key="index"
				class="messenger__input_file-attachment"
			>
				<div
					class="messenger__input_file-attachment-preview"
					:style="preview ? `background-image: url(${preview})` : ''"
				>
					<ChatIconHistoryDoc v-if="!preview" />
				</div>
				<div>
					<div>{{ file.name }}</div>
					<div>{{ file.size | fileSizeFormat }}</div>
				</div>
				<div
					class="messenger__input_file-attachment_remove"
					@click="removeFile(file, $event)"
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						viewBox="0 0 320 512"
					>
						<path
							d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z"
						/>
					</svg>
				</div>
			</div>
		</template>
	</div>
</template>

<script>
import VueDraggableResizable from 'vue-draggable-resizable'
import {mapActions, mapGetters} from 'vuex';
import EmojiPopup from './EmojiPopup/EmojiPopup.vue';
import AudioDictaphone from './AudioDictaphone/AudioDictaphone.vue';
import SpectrumAnalyser from './SpectrumAnalyser/SpectrumAnalyser.vue';
import {
	ChatIconUpload,
	ChatIconSend,
	ChatIconHistoryDoc,
} from '@icons'

export default {
	name: 'ConversationFooter',
	components: {
		EmojiPopup,
		AudioDictaphone,
		SpectrumAnalyser,
		ChatIconUpload,
		ChatIconSend,
		ChatIconHistoryDoc,
		VueDraggableResizable,
	},
	filters: {
		countdownFormat(value) {
			let seconds = Math.floor(value / 100);
			let minutes = Math.floor(seconds / 60);
			let santiseconds = value % 100;
			seconds = seconds % 60;
			return `${minutes}:${seconds < 10 ? '0' + seconds : seconds},${santiseconds < 10 ? '0' + santiseconds : santiseconds}`;
		},
		fileSizeFormat(value) {
			if (value < 1024) {
				return `${value} B`;
			} else if (value < 1024 * 1024) {
				return `${Math.floor(value / 1024)} KB`;
			} else {
				return `${Math.floor(value / 1024 / 1024)} MB`;
			}
		}
	},
	inject: [
		'ChatApp'
	],
	data() {
		return {
			body: '',
			files: [],
			isRecordingAudio: false,
			recordingTime: 0,
			localState: this.defaultLocalState()
		};
	},
	computed: {
		...mapGetters(['chat', 'editMessage', 'citedMessage', 'messageSending'])
	},
	watch: {
		editMessage(message) {
			if (message) {
				this.body = message.body;
			} else {
				this.body = '';
			}
		},
		recordingTime(duration) {
			if (duration > 0) {
				setTimeout(() => {
					this.recordingTime = duration + 1;
				}, 10);
			}
		},
		citedMessage(){
			this.focusInput()
		}
	},
	created(){
		this.loadLocalState()
		this.ChatApp.$on('addQuote', this.onQuote)
	},
	methods: {
		...mapActions(['sendMessage', 'editMessageAction', 'uploadFiles', 'citeMessage']),
		performMessage(e) {
			if(e.ctrlKey) {
				if(e.target.selectionStart || e.target.selectionStart === 0){
					const start = e.target.selectionStart
					const end = e.target.selectionEnd
					const text = this.body
					this.body = text.substring(0, start) + '\n' + text.substring(end, text.length)
					e.target.selectionStart = start + 1
				}
				else{
					this.body += '\n'
				}
				return true
			}
			if(this.messageSending) return true
			let text = this.body.trim();
			if ((text || this.files.length > 0) && this.chat) {
				if (this.editMessage) {
					this.editMessageAction(text);
				} else {
					e.preventDefault();
					if (this.files.length > 0) {
						this.uploadFiles({'files': this.files.map(f => f.file), 'caption': text});
						this.files = [];
					} else {
						this.sendMessage(text)
					}
					this.body = '';
					this.$nextTick(() => {
						this.focusInput()
					});
				}
			}
		},
		focusInput(){
			const input = document.getElementById('messengerMessageInput')
			if(input) input.focus()
		},
		appendEmoji(emoji) {
			this.body += emoji;
		},
		pasteMessage(e) {
			const items = (e.clipboardData || e.originalEvent.clipboardData).items;
			for (let index in items) {
				let item = items[index];
				if (item.kind === 'file') {
					this.files.push(this.getPreview({
						file: item.getAsFile(),
						preview: null
					}));
				}
			}
		},
		removeFile(file, event) {
			event.stopPropagation();
			this.files = this.files.filter(f => f.file !== file);
		},
		prepareFiles(event) {
			const files = event.target.files;
			for (let file of files) {
				this.files.push(this.getPreview({
					file,
					preview: null
				}));
			}
		},
		handleRecording({blob}) {
			this.uploadFiles({'files': [blob], 'caption': ''});
			this.isRecordingAudio = false;
		},
		setRecordingAudio(value) {
			this.isRecordingAudio = value;
			this.recordingTime += 1;
		},
		closeCitation(event) {
			event.stopPropagation();
			this.body = ''
			this.citeMessage(null);
		},
		onResizeStop(left, top, width, height){
			this.ChatApp.$emit('FooterResized')
			this.localState.height = height
			this.saveLocalState()
		},
		handleError(error) {
			console.error('ConversationFooter', error)
		},
		getPreview(fileObj){
			if(fileObj.file.type && fileObj.file.type.substring(0, 5) === 'image'){
				const reader = new FileReader()
				reader.addEventListener('load', () => {
					fileObj.preview = reader.result
				}, false);
				reader.readAsDataURL(fileObj.file)
			}
			return fileObj
		},
		defaultLocalState(){
			return {
				height: 48
			}
		},
		loadLocalState(){
			const local = localStorage.getItem('ConversationFooter')
			if(local){
				this.localState = JSON.parse(local)
				return
			}
			this.localState = this.defaultLocalState()
		},
		saveLocalState(){
			localStorage.setItem('ConversationFooter', JSON.stringify(this.localState))
		},
		onQuote(text){
			if(this.body.trim()) this.body += '\n> ' + text.split('\n').join('\n> ') + '\n'
			else this.body = '> ' + text.split('\n').join('\n> ') + '\n'
		}
	},
}
</script>

<style lang="scss">
.ConversationFooter{
	overflow: hidden;

	// fix for vue-draggable-resizable
	.handle-tm{
		display: flex !important;
		align-items: center;
		justify-content: center;

		height: 3px;
		position: relative;
		cursor: move;
		&:before{
			content: '';
			width: 1rem;
			height: 1px;
			background-color: #000;
		}
	}
	.resizable{
		display: flex;
		flex-flow: column nowrap;
		justify-content: stretch;
		flex: 0 1 content;
		width: 100% !important;
		min-height: 45px;
		transform: none !important;
	}
}
.messenger__chat-footer {
  width: 100%;
  border-bottom-right-radius: 4px;
  /* border-top: 2px solid #e2e2e2; */
  // max-height: 30vh;
  // overflow-y: auto;
  // overflow-x: hidden;
}

.messenger__box-footer {
  display: flex;
	align-items: stretch;
	flex: 1;
  padding: 5px 8px;
  position: relative;
  background-color: #fff;
}

.messenger__attachment{
	display: flex;
	align-items: flex-start;
	padding: 0 7px;
	margin: 0;
}

.messenger__textarea {
  /* min-height: 20px; */
  // max-height: 80px;
  overflow-x: hidden !important;
  overflow-y: auto !important;
  width: 100%;
  height: 100%;
  line-height: 20px;
  outline: 0;
  resize: none;
  border: none;
  box-sizing: content-box;
  font-size: 14px;
  background: #fff;
  color: #0a0a0a;
  caret-color: #1976d2;
  padding: 5px 10px 0 5px;
}

.messenger__textarea:focus {
  border: none;
  outline: none;
  -webkit-box-shadow: none;
  box-shadow: none;
}

.messenger__chat-footer_spectrum {
  min-height: 20px;
  max-height: 80px;
  width: 100%;
  resize: none;
  border: none;
  box-sizing: content-box;
  background: #fff;
  padding: 5px 10px 0 5px;
}

.messenger__message-input {
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: center;
  margin-right: 10px;
  min-width: 0;
}

.messenger__message-text-input {
  flex-grow: 1;
  align-items: flex-start;
}

.messenger__input_file-attachment {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: flex-start;
	gap: 10px;

  padding: 0 10px;
  line-height: 1.5;
  width: 100%;
}
.messenger__input_file-attachment-preview{
	display: flex;
	align-items: center;
	justify-content: center;

	width: 30px;
	height: 30px;
	background-size: cover;
}
.messenger__input_file-attachment_remove {
  width: 30px;
  height: 36px;
  cursor: pointer;
  padding: 10px;
  min-width: 10px;
	margin-left: auto;
}
.messenger__input_file-attachment_remove:hover{
	fill: red;
}

.messenger__record {
  display: flex;
  align-items: center;
}

.messenger__record-item {
  width: 35px;
  height: 35px;
  padding: 7px;
  cursor: pointer;
}

.messenger__record-item:hover {
  background: #dff2fb;
  border-radius: 50%;
}

.messenger__record-item svg {
  width: 100%;
  height: 100%;
}

.messenger__message-send {
  height: 35px;
  border: none;
  cursor: pointer;
  background: #5ebee9;
  color: #fff;
  border-radius: 30px;
  padding: 0 20px;
  margin: 5px 0 10px 0;
}

.messenger__record_recording {
  align-items: center;
  justify-content: center;
  padding: 7px;
}

.messenger__message-input_cite {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
  justify-content: space-between;
  width: 100%;
  max-height: 40px;
  padding: 7px 10px 0 5px;
  background: #fff;
  border-radius: 4px;
  margin-bottom: 5px;
}

.messenger__message-input_cite-body {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.messenger__message-input_cite span {
  font-size: 14px;
  color: #0a0a0a;
  cursor: pointer;
}

.messenger__message-icon{
	display: flex;
	align-items: center;
  justify-content: center;
	width: 35px;
	height: 35px;
	padding: 7px;
	cursor: pointer;
}
.messenger__message-icon_hover:hover {
  background: #dff2fb;
  border-radius: 50%;
}

</style>
