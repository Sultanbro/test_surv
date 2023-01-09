<script>
export default {
	name: 'Section1Profile',
	computed: {
		lang () {
			return this.$root.$data.lang
		}
	},
}
</script>
<template>
    <div class="jSec1-tabs-item jSec1-profile">
        <div class="jSec1-profile-text">
            <h3 class="jSec1-profile-title">{{ $lang(lang, 's1-for-worker-profile') }}</h3>
            <ul class="jSec1-profile-list">
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-worker-profile-1') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-worker-profile-2') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-worker-profile-3') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-worker-profile-4') }}</li>
            </ul>
            <h3 class="jSec1-profile-title">{{ $lang(lang, 's1-for-super-profile') }}</h3>
            <ul class="jSec1-profile-list">
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-super-profile-1') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-super-profile-2') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-super-profile-3') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-super-profile-4') }}</li>
            </ul>
            <a
                href="/register"
                class="jSec1-profile-button jButton"
            >{{ $lang(lang, 's1-for-free') }}</a>
        </div>
        <div class="jSec1-profile-banner">
            <video class="jSec1-profile-banner-img" autoplay loop>
                <source :src="require('-!file-loader!../../assets/img/profile2.mp4').default">
            </video>
        </div>
    </div>
</template>
<style lang="scss"></style>
