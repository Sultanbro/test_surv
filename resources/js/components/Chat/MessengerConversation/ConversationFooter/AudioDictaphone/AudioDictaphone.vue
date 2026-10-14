<template>
	<div>
		<slot
			:is-recording="isRecording"
			:start-recording="startRecording"
			:stop-recording="stopRecording"
			:delete-recording="deleteRecording"
		/>
	</div>
</template>

<script>

export default {
	name: 'AudioDictaphone',
	data() {
		return {
			audioBlob: null,
			mediaRecorder: null,
			isRecording: false,
			mimeType: 'audio/webm',
			abort: false
		};
	},
	watch: {
		audioBlob() {
			if (!this.abort) {
				this.$emit('stop', {
					blob: this.audioBlob,
					src: URL.createObjectURL(this.audioBlob),
				});
			}
		},
	},
	methods: {
		async initialize() {
			let stream;

			// for each browser choose the right media type
			const mediaType = {
				chrome: 'audio/webm',
				firefox: 'audio/ogg',
				safari: 'audio/mp3',
				unknown: 'audio/webm',
			};

			// get the browser name
			const browser = navigator.userAgent.toLowerCase();
			const browserName = browser.indexOf('chrome') > -1 ? 'chrome' : browser.indexOf('firefox') > -1 ? 'firefox' : browser.indexOf('safari') > -1 ? 'safari' : 'unknown';

			// set the media type
			this.mimeType = mediaType[browserName];

			try {
				stream = await navigator.mediaDevices.getUserMedia({audio: true});
			} catch (error) {
				this.$emit('error', '`navigator.mediaDevices.getUserMedia()` failed.');
				return Promise.resolve();
			}

			const recorder = new MediaRecorder(stream);
			let chunks = [];

			recorder.addEventListener('stop', () => {
				this.audioBlob = new Blob(chunks, {type: this.mimeType});
				chunks = [];
			});

			recorder.addEventListener('dataavailable', (e) => {
				chunks.push(e.data);
			});

			this.mediaRecorder = recorder;
		},
		async startRecording(e) {
			e.stopPropagation();
			try {
				await this.initialize();
				this.mediaRecorder.start();
				this.isRecording = true;
				this.abort = false;
				this.$emit('start');
				return true;
			} catch (e) {
				console.error(e);
				alert('Запись недоступна. Разрешите доступ к микрофону или смените браузер.');
				return false;
			}
		},
		stopRecording(e) {
			e.stopPropagation();
			this.stopRecord();
			return true;
		},
		deleteRecording(e) {
			e.stopPropagation();
			this.$emit('delete');
			this.audioBlob = null;
			this.abort = true;
			this.stopRecord();
			return true;
		},
		stopRecord() {
			this.isRecording = false;
			this.mediaRecorder.stop();
			this.mediaRecorder.stream.getTracks().forEach(track => track.stop());
			this.mediaRecorder.stream.getAudioTracks()[0].stop();
			this.mediaRecorder = null;
		},
	},
};
</script>
