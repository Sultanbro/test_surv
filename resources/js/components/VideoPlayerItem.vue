<template>
    <div class="video-player">
        <video-player 
            class="vjs-custom-skin"
            ref="videoPlayer"
            :options="playerOptions"
            :playsinline="true"
            @play="onPlayerPlay($event)"
            @pause="onPlayerPause($event)"
            @statechanged="playerStateChanged($event)" />
    </div>
</template>

<script>
export default {
    props: [
        'src',
    ],
    mounted() {
        let self = this; 
        window.addEventListener('keyup', function(ev) {
            self.pauseVideo(ev);
        });
    },
    data(){
        return {
            playerOptions: {
                // videojs options
                muted: false,
                state: true,
                width: 100,
                language: 'ru',
                playbackRates: [0.5, 0.75, 1.0, 1.25, 1.5, 1.75, 2.0, 3.0],
                sources: [{
                    type: "video/mp4",
                    src: this.src,
                }],
                poster: "/static/images/author.jpg",
                userActions: {
                    hotkeys: true
                }
            },
        }
    },
    methods: {
        // listen event
        onPlayerPlay(player) {
            // console.log('player play!', player)
        },
        onPlayerPause(player) {
            // console.log('player pause!', player)
        },
        // ...player event

        // or listen state event
        playerStateChanged(playerCurrentState) {
            // console.log('player current update state', playerCurrentState)
        },

        playerReadied(player) {
            console.log('the player is readied', player)
        },

        pauseVideo(event){
            if(event.code == 'Space'){
                if(this.state){
                    this.$refs.videoPlayer.player.play()
                    this.state = false;       
                }else{
                    this.$refs.videoPlayer.player.pause()
                    this.state = true;  
                }
            }
        }
    }
}
</script>

<style lang="scss">
.video-player {

}
</style>