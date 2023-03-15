<script>
import Section1InlinePopup from './Section1InlinePopup'
export default {
	name: 'Section1Struct',
	components: {
		Section1InlinePopup
	},
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
			<h3 class="jSec1-profile-title">
				{{ $lang(lang, 's1-for-super-struct') }}
			</h3>
			<ul class="jSec1-profile-list">
				<li class="jSec1-profile-list-item">
					{{ $lang(lang, 's1-for-super-struct-1') }}
					<Section1InlinePopup>{{ $lang(lang, 's1-for-super-struct-1p') }}</Section1InlinePopup>
				</li>
				<li class="jSec1-profile-list-item">
					{{ $lang(lang, 's1-for-super-struct-2') }}
					<Section1InlinePopup>{{ $lang(lang, 's1-for-super-struct-2p') }}</Section1InlinePopup>
				</li>
				<li class="jSec1-profile-list-item">
					{{ $lang(lang, 's1-for-super-struct-3') }}
					<Section1InlinePopup>{{ $lang(lang, 's1-for-super-struct-3p') }}</Section1InlinePopup>
				</li>
			</ul>
			<h3 class="jSec1-profile-title">
				{{ $lang(lang, 's1-for-worker-struct') }}
			</h3>
			<ul class="jSec1-profile-list">
				<li class="jSec1-profile-list-item">
					{{ $lang(lang, 's1-for-worker-struct-1') }}
				</li>
				<li class="jSec1-profile-list-item">
					{{ $lang(lang, 's1-for-worker-struct-2') }}
				</li>
			</ul>
			<a
				href="/register"
				class="jSec1-profile-button jButton"
			>{{ $lang(lang, 'increase-profit') }}</a>
		</div>
		<div class="jSec1-profile-banner">
			<img
				:src="require('../../assets/img/struct.png').default"
				alt=""
				class="jSec1-profile-banner-img"
				width="760"
			>
		</div>
	</div>
</template>
<style lang="scss"></style>
