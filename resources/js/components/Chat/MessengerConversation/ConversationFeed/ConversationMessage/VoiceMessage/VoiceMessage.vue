<template>
	<div class="voice-message">
		<div
			class="voice-message__button"
			@click="togglePlay"
		>
			<div class="voice-message__button__icon">
				<ChatIconPauseVoice v-if="isPlaying" />
				<ChatIconPlayVoice v-else />
			</div>
		</div>
		<div
			class="voice-message__bars"
			@click="resume($event.offsetX / $event.target.offsetWidth)"
		>
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
import {
	ChatIconPauseVoice,
	ChatIconPlayVoice,
} from '@icons'

export default {
	name: 'VoiceMessage',
	components: {
		ChatIconPauseVoice,
		ChatIconPlayVoice,
	},
	filters: {
		durationFormat(value) {
			const minutes = Math.floor(value / 60);
			const seconds = Math.floor(value - minutes * 60);

			return `${minutes}:${seconds < 10 ? `0${seconds}` : seconds}`;
		},
	},
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
  background-color: #C4CAE1;
  margin: 0 2px;
  border-radius: 2px;
}

.voice-message__bars__bar--active {
  background-color: #3361FF;
}

.voice-message__duration {
  margin-left: 10px;
  color: #A6B7D4;
	font-size: 11px;
	line-height: 14px;
}

.voice-message__duration::before {
  content: "·";
  margin: 0 5px;
}

@media only screen and (max-width: 670px) {
	.voice-message__bars{
		width: 165px;
	}
	.voice-message__bars__bar {
		display: inline-block;
		width: 2px;
		height: 10px;
		background-color: #C4CAE1;
		margin: 0 1px;
		border-radius: 2px;
	}
}

</style>
