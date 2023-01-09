<script>
export default {
	name: 'Section1Courses',
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
            <h3 class="jSec1-profile-title">{{ $lang(lang, 's1-for-worker-courses') }}</h3>
            <ul class="jSec1-profile-list">
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-worker-courses-1') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-worker-courses-2') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-worker-courses-3') }}</li>
            </ul>
            <h3 class="jSec1-profile-title">{{ $lang(lang, 's1-for-super-courses') }}</h3>
            <ul class="jSec1-profile-list">
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-super-courses-1') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-super-courses-2') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-super-courses-3') }}</li>
            </ul>
            <a
                href="/register"
                class="jSec1-profile-button jButton"
            >{{ $lang(lang, 's1-for-free') }}</a>
        </div>
        <div class="jSec1-profile-banner">
            <img
                :src="require('../../assets/img/s1-profile-banner.png').default"
                alt=""
                class="jSec1-profile-banner-img"
            >
        </div>
    </div>
</template>
<style lang="scss"></style>
