<script>
export default {
    name: 'Section1KPI',
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
            <h3 class="jSec1-profile-title">{{ $lang(lang, 's1-for-worker-kpi') }}</h3>
            <ul class="jSec1-profile-list">
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-worker-kpi-1') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-worker-kpi-2') }}</li>
            </ul>
            <h3 class="jSec1-profile-title">{{ $lang(lang, 's1-for-super-kpi') }}</h3>
            <ul class="jSec1-profile-list">
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-super-kpi-1') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-super-kpi-2') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-super-kpi-3') }}</li>
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
