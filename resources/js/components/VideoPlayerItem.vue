<template>
	<div class="video-player">
		<videoPlayer
			ref="videoPlayer"
			class="vjs-custom-skin"
			:options="playerOptions"
			:playsinline="true"
			@play="onPlayerPlay"
			@pause="onPlayerPause"
			@ready="playerReadied"
			@statechanged="playerStateChanged"
		/>
	</div>
</template>

<script>
import { videoPlayer } from 'vue-video-player'

export default {
	components: {
		videoPlayer,
	},
	props: {
		src: {
			type: String,
			default: ''
		},
		autoplay: {
			type: Boolean,
			default: true
		}
	},

	data(){
		return {
			playerOptions: {
				// videojs options
				muted: false,
				autoplay: false,
				height:  360,
				width: 100,
				language: 'ru',
				playbackRates: [0.5, 0.7, 0.8, 0.9, 1.0, 1.1, 1.2, 1.3, 1.4, 1.5, 1.75, 2.0, 3.0],
				sources: [{
					src: this.src,
					type: 'video/mp4'
				}],
				//sources: [this.src],
				poster: '/images/author.jpg',
				userActions: {
					hotkeys: true
				}
			},
		}
	},

	methods: {
		// listen event
		onPlayerPlay() {
			// console.log('player play!', player)
		},
		onPlayerPause() {
			// console.log('player pause!', player)
		},
		// ...player event

		// or listen state event
		playerStateChanged() {
			// console.log('player current update state', playerCurrentState)
		},

		playerReadied() {},
	}
}
</script>

<style lang="scss">
.video-player {
	&.vjs-custom-skin{
		.video-js{
			min-height: auto;
		}
	}
}
</style>
