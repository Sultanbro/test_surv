<template>
  <div class="voice-message">
    <div class="voice-message__button" @click="togglePlay">
      <div class="voice-message__button__icon">
        <svg
          v-if="!isPlaying"
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M8 5V19L19 12L8 5Z"
            fill="#5ebee9"
          />
        </svg>
        <svg
          v-else
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M6 19H8V5H6V19ZM16 19H18V5H16V19Z"
            fill="#5ebee9"
          />
        </svg>
      </div>
    </div>
    <div class="voice-message__bars" @click="resume($event.offsetX / $event.target.offsetWidth)">
      <div
        v-for="(bar, index) in bars"
        :key="index"
        :class="{
          'voice-message__bars__bar': true,
          'voice-message__bars__bar--active': index < activeBar
        }"
      />
    </div>
    <div class="voice-message__duration">
      {{ duration | durationFormat }}
    </div>
    <audio
      ref="audio"
      :src="audioSource"
      @timeupdate="updateProgress"
      @ended="setStopped"
      @pause="setStopped"
      @play="setPlaying"
      @loadedmetadata="updateDuration"
    />
  </div>
</template>

<script>
export default {
  name: 'VoiceMessage',
  props: {
    audioSource: {
      type: String,
      required: true,
    },
    isActive: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      progress: 0,
      duration: '',
      isPaused: true,
    };
  },
  computed: {
    isPlaying() {
      return this.isActive && !this.isPaused;
    },
    activeBar() {
      return Math.floor(this.bars.length * this.progress / 100);
    },
    bars() {
      return Array.from({ length: 40 });
    },
  },
  watch: {
    isActive(val) {
      console.log('isActive', val);
      if (!val) {
        this.stop();
      }
    },
  },
  methods: {
    setPlaying() {
      this.isPaused = false;
      this.$emit('play');
    },
    setStopped() {
      this.$refs.audio.currentTime = 0;
      this.progress = 0;
      this.isPaused = true;
    },
    play() {
      this.$refs.audio.play();
    },
    pause() {
      this.$refs.audio.pause();
      this.isPaused = true;
    },
    stop() {
      this.$refs.audio.pause();
    },
    resume(position) {
      if (position > 0.05 && position < 0.95) {
        this.$refs.audio.currentTime = this.$refs.audio.duration * position;
        this.$refs.audio.play();
      }
    },
    togglePlay() {
      if (this.isPlaying) {
        this.pause();
      } else {
        this.play();
      }
    },
    updateProgress() {
      this.progress = (this.$refs.audio.currentTime / this.$refs.audio.duration) * 100;
    },
    updateDuration() {
      if (this.$refs.audio.duration === Infinity) {
        this.$refs.audio.currentTime = Number.MAX_SAFE_INTEGER;
        this.$refs.audio.ontimeupdate = () => {
          this.$refs.audio.ontimeupdate = () => {};
          this.$refs.audio.currentTime = 0;
          this.duration = this.$refs.audio.duration;
        };
      }
    },
  },
  filters: {
    durationFormat(value) {
      const minutes = Math.floor(value / 60);
      const seconds = Math.floor(value - minutes * 60);

      return `${minutes}:${seconds < 10 ? `0${seconds}` : seconds}`;
    },
  },
};
</script>

<style scoped>
.voice-message {
  display: flex;
  align-items: center;
  padding: 0 0 0 10px;
  cursor: pointer;
  user-select: none;
  transition: all 0.2s ease-in-out;
}

.voice-message:hover {
  background-color: #F2F2F2;
}

.voice-message__button {
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: all 0.2s ease-in-out;
  margin-right: 5px;
}

.voice-message__button:hover {
  background-color: #5ebee9;
}

.voice-message__button__icon {
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.voice-message__progress {
  width: 250px;
  height: 2px;
  background-color: #E5E5E5;
  margin: 0 10px;
}

.voice-message__progress__bar {
  height: 2px;
  background-color: #5ebee9;
}

.voice-message__bars {
  width: 240px;
  height: 30px;
  margin: 0 10px;
  display: flex;
  align-items: center;
}

.voice-message__bars__bar {
  display: inline-block;
  width: 2px;
  height: 10px;
  background-color: #E5E5E5;
  margin: 0 2px;
  border-radius: 2px;
}

.voice-message__bars__bar--active {
  background-color: #5ebee9;
}

.voice-message__duration {
  margin-left: 10px;
  font-size: 12px;
  color: #1A1A1A;
}

.voice-message__duration::before {
  content: "·";
  margin: 0 5px;
}


</style>
