<template>
	<section id="jSec1">
		<div class="section-content">
			<h1 class="jSec1-header jHeader">
				{{ $lang(lang, 's1-header') }}
			</h1>
			<div
				v-if="isMedium"
				:class="{'jSec1-tabs-popup-active': isPopupVisible}"
				class="jSec1-tabs"
			>
				<ul class="jSec1-tabs-buttons">
					<Section1Tab
						:active-tab="activeTab"
						tab-id="profile"
						@content="setTab('profile')"
						@popup="setPopup('profile')"
					/>
					<Section1Tab
						:active-tab="activeTab"
						tab-id="kpi"
						@content="setTab('kpi')"
						@popup="setPopup('kpi')"
					/>
					<Section1Tab
						:active-tab="activeTab"
						tab-id="db"
						@content="setTab('db')"
						@popup="setPopup('db')"
					/>
					<Section1Tab
						:active-tab="activeTab"
						tab-id="courses"
						@content="setTab('courses')"
						@popup="setPopup('courses')"
					/>
					<Section1Tab
						:active-tab="activeTab"
						tab-id="struct"
						@content="setTab('struct')"
						@popup="setPopup('struct')"
					/>
					<Section1Tab
						:active-tab="activeTab"
						tab-id="news"
						@content="setTab('news')"
						@popup="setPopup('news')"
					/>
				</ul>
				<Section1Popup
					v-click-outside="hidePopup"
					:lang="lang"
					:type="activePopup"
				/>
				<div class="jSec1-tabs-content">
					<Section1Profile v-show="activeTab === 'profile'" />
					<Section1KPI v-show="activeTab === 'kpi'" />
					<Section1DB v-show="activeTab === 'db'" />
					<Section1Courses v-show="activeTab === 'courses'" />
					<Section1Struct v-show="activeTab === 'struct'" />
					<Section1News v-show="activeTab === 'news'" />
				</div>
			</div>
			<Hooper
				v-if="!isMedium"
				ref="carousel"
				:auto-play="true"
				:infinite-scroll="true"
				:play-speed="3000"
			>
				<Slide>
					<Section1Profile />
				</Slide>
				<Slide>
					<Section1KPI />
				</Slide>
				<Slide>
					<Section1DB />
				</Slide>
				<Slide>
					<Section1Courses />
				</Slide>
				<Slide>
					<Section1Struct />
				</Slide>
				<Slide>
					<Section1News />
				</Slide>
			</Hooper>
		</div>
	</section>
</template>

<script>
import {Hooper, Slide} from 'hooper'
import 'hooper/dist/hooper.css'
import Section1Popup from '../section1/Section1Popup'
import Section1Tab from '../section1/Section1Tab'
import Section1Profile from '../section1/Section1Profile'
import Section1DB from '../section1/Section1DB'
import Section1KPI from '../section1/Section1KPI'
import Section1Courses from '../section1/Section1Courses'
import Section1Struct from '../section1/Section1Struct'
import Section1News from '../section1/Section1News'

export default {
	name: 'SectionSection1',
	components: {
		Section1Popup,
		Section1Tab,
		Section1Profile,
		Section1DB,
		Section1KPI,
		Section1Courses,
		Section1Struct,
		Section1News,
		Hooper,
		Slide,
	},
	data() {
		return {
			activeTab: 'profile',
			activePopup: 'profile',
			isPopupVisible: false
		}
	},
	computed: {
		isMedium() {
			return this.$viewportSize.width >= 1260
		},
		lang() {
			return this.$root.$data.lang
		}
	},
	methods: {
		setTab(key) {
			this.activeTab = key
		},
		setPopup(key) {
			setTimeout(() => {
				this.activePopup = key
				this.isPopupVisible = true
			}, 1);
		},
		hidePopup() {
			if (this.isPopupVisible) this.isPopupVisible = false
		}
	}
}
</script>

<style lang="scss">
@import '../../assets/scss/app.variables.scss';

#jSec1 {
  width: 100%;
  padding-top: 1rem;
  padding-bottom: 4rem;
  background-image: url("../../assets/img/s1-bg.svg");
  background-repeat: no-repeat;

  .hooper {
    height: auto;
  }
}

.jSec1-header.jHeader {
  //  font-weight: 700;
  font-size: 1.25rem;
  line-height: 1;
}

.jSec1-tabs {
  display: flex;
  flex-flow: column nowrap;
  align-items: center;
  position: relative;

  .Section1Popup {
    top: 3.5rem;
    visibility: hidden;
    opacity: 0;
    transition: all 0.3s;
  }
}

.jSec1-tabs-popup-active {
  .Section1Popup {
    visibility: visible;
    opacity: 1;
  }
}

.jSec1-tabs-buttons {
  margin: 0;
  padding: 0;
  text-align: center;
}

.jSec1-tabs-button {
  display: inline-block;
  margin: 0.25rem;
  list-style: none;
  text-align: center;
}

.jSec1-tabs-link {
  display: inline-block;
  padding: 0.75rem 0;
  margin-bottom: -0.0625rem;
  text-decoration: none;
  color: #252525;
}

.jSec1-tabs-link--active {
  border-bottom: 0.1875rem solid #42b1f4;
}

.jSec1-tabs-qm {
  display: inline-block;
  width: 1rem;
  height: 1rem;
  background-image: url("../../assets/img/s1-qm.svg");
  background-size: contain;
  vertical-align: super;
}

.jSec1-tabs-content {
  width: 100%;
}

.jSec1-tabs-item {
  display: flex;
  flex-flow: row wrap;
  padding-top: 1.875rem;
}

.jSec1-profile-text {
  order: 2;
}

.jSec1-profile-banner {
  order: 1;
}

@media screen and (min-width: $small) {
  .jSec1-profile-title {
    margin-top: 0;
  }
}

.jSec1-profile-title {
  //margin-top: 0;
  margin-bottom: 0.5rem;
  color: #42b1f4;
  font-size: 1.25rem;
}

.jSec1-profile-list {
  margin: 0 0 2.375rem;
  line-height: 1.56;
  color: #000;
  padding: 0;
}

.jSec1-profile-list-item {
  list-style: none;

  &:before {
    content: '';
    display: inline-block;
    width: 0.5rem;
    height: 0.75rem;
    margin-right: 0.5625rem;
    transform: matrix(0.83, 0.56, 0.56, -0.83, 0, 0);
    background-image: url("../../assets/img/s1-dot.svg");
    background-size: cover;
  }
}

.jSec1-profile-button {
  margin: 0 0 2.375rem;
}

.jSec1-profile-banner-img {
  max-width: 100%;
  border-radius: 0.25rem;
}

@media (max-width: $small) {
  .jSec1-profile-list {
    margin: 0;
  }

  .jSec1-profile-button {
    margin-top: 1.5rem;
  }
}

@media screen and (min-width: $small) {
  #jSec1 {
    background-position: 100%;
  }
}

@media screen and (min-width: $medium) {
  #jSec1 {
    min-height: 764px;
    padding-bottom: 8rem;
    background-image: url("../../assets/img/s1-bg.svg"), url("../../assets/img/s1-bg-2.svg");
    background-repeat: no-repeat, no-repeat;
    background-position: 100% 50%, 0% 50%;
  }
  .jSec1-header.jHeader {
    font-size: 2.5rem;
  }
  .jSec1-tabs-buttons {
    display: flex;
    justify-content: center;
    gap: 1.125rem;
    width: fit-content;
    border-bottom: 0.0625rem solid #d9d9d9;
  }
  .jSec1-tabs-button {
    display: block;
    margin: 0;
  }
  .jSec1-tabs-item {
    display: flex;
  }
  .jSec1-profile-text {
    flex: 0 0 45%;
    order: 1;
  }
  .jSec1-profile-banner {
    flex: 0 0 55%;
    order: 2;
  }
}

@media screen and (min-width: $large) {
  #jSec1 {
    min-height: 1530px;
  }

  .jSec1-profile-banner-img {
    position: absolute;
    width: 1349px;
  }
}

@media (max-width: $large) and (min-width: 1920px) {
  .jSec1-profile-banner-img {
    position: absolute;
    max-width: 920px;
  }
}

@media (max-width: $small) {
  #jSec1 {
    padding-bottom: 0;
  }
}
</style>
