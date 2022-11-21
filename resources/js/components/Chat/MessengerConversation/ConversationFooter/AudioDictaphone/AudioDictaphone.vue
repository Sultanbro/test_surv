<template>
  <div>
    <slot v-if="mediaRecorder" :isRecording="isRecording"
          :startRecording="startRecording"
          :stopRecording="stopRecording"
          :deleteRecording="deleteRecording">
    </slot>
  </div>
</template>

<script>
export default {
  name: 'dictaphone',
  data() {
    return {
      audioBlob: null,
      mediaRecorder: null,
      isRecording: false,
      mimeType: 'audio/webm',
      abort: false
    };
  },
  methods: {
    startRecording(e) {
      e.stopPropagation();
      this.isRecording = true;
      this.abort = false;
      this.$emit('start');
      this.mediaRecorder.start();
      return true;
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
      // stream.getTracks().forEach(function(track) {
      //   if (track.readyState === 'live' && track.kind === 'audio') {
      //     track.stop();
      //   }
      // });
    }
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
  // eslint-disable-next-line consistent-return
  async mounted() {
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
};
</script>
