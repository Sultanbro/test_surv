<template>
  <section id="jSec1">
    <a name="features" id="features" class="ancor"/>
    <div class="section-content">
      <h1 class="jSec1-header jHeader">{{ $lang(lang, 's1-header') }}</h1>
      <div class="jSec1-tabs">
        <ul class="jSec1-tabs-buttons">
          <li class="jSec1-tabs-button">
            <a
              href="javascript:void(0)"
              class="jSec1-tabs-link"
              :class="{'jSec1-tabs-link--active': activeTab === 'profile'}"
              @click="setTab('profile')"
            >{{ $lang(lang, 's1-profile') }}</a>
            <a
              href="javascript:void(0)"
              class="jSec1-tabs-qm"
            />
          </li>
          <li class="jSec1-tabs-button">
            <a
              href="javascript:void(0)"
              class="jSec1-tabs-link"
              :class="{'jSec1-tabs-link--active': activeTab === 'db'}"
              @click="setTab('db')"
            >{{ $lang(lang, 's1-db') }}</a>
            <a
              href="javascript:void(0)"
              class="jSec1-tabs-qm"
            />
          </li>
          <li class="jSec1-tabs-button">
            <a
              href="javascript:void(0)"
              class="jSec1-tabs-link"
              :class="{'jSec1-tabs-link--active': activeTab === 'kpi'}"
              @click="setTab('kpi')"
            >{{ $lang(lang, 's1-kpi') }}</a>
            <a
              href="javascript:void(0)"
              class="jSec1-tabs-qm"
            />
          </li>
          <li class="jSec1-tabs-button">
            <a
              href="javascript:void(0)"
              class="jSec1-tabs-link"
              :class="{'jSec1-tabs-link--active': activeTab === 'courses'}"
              @click="setTab('courses')"
            >{{ $lang(lang, 's1-courses') }}</a>
            <a
              href="javascript:void(0)"
              class="jSec1-tabs-qm"
            />
          </li>
          <li class="jSec1-tabs-button">
            <a
              href="javascript:void(0)"
              class="jSec1-tabs-link"
              :class="{'jSec1-tabs-link--active': activeTab === 'struct'}"
              @click="setTab('struct')"
            >{{ $lang(lang, 's1-struct') }}</a>
            <a
              href="javascript:void(0)"
              class="jSec1-tabs-qm"
            />
          </li>
          <li class="jSec1-tabs-button">
            <a
              href="javascript:void(0)"
              class="jSec1-tabs-link"
              :class="{'jSec1-tabs-link--active': activeTab === 'news'}"
              @click="setTab('news')"
            >{{ $lang(lang, 's1-news') }}</a>
            <a
              href="javascript:void(0)"
              class="jSec1-tabs-qm"
            />
          </li>
        </ul>
        <div class="jSec1-tabs-content">
          <div class="jSec1-tabs-item jSec1-profile" v-show="activeTab === 'profile'">
            <div class="jSec1-profile-text">
              <h3 class="jSec1-profile-title">{{ $lang(lang, 's1-for-worker') }}</h3>
              <ul class="jSec1-profile-list">
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-worker-1') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-worker-2') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-worker-3') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-worker-4') }}</li>
              </ul>
              <h3 class="jSec1-profile-title">{{ $lang(lang, 's1-for-super') }}</h3>
              <ul class="jSec1-profile-list">
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-super-1') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-super-2') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-super-3') }}</li>
                <li class="jSec1-profile-list-item">{{ $lang(lang, 's1-for-super-4') }}</li>
              </ul>
              <a href="javascript:void(0)" class="jSec1-profile-button jButton">{{ $lang(lang, 's1-for-free') }}</a>
            </div>
            <div class="jSec1-profile-banner">
              <img :src="require('../../assets/img/s1-profile-banner.png').default" alt="" class="jSec1-profile-banner-img">
            </div>
          </div>
          <div class="jSec1-tabs-item" v-show="activeTab === 'news'"></div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  computed: {
    lang () {
      return this.$root.$data.lang
    }
  },
  data(){
    return {
      activeTab: 'profile'
    }
  },
  methods: {
    setTab(key){
      this.activeTab = key
    }
  }
}
</script>

<style lang="scss">
@import '../../assets/scss/app.variables.scss';
#jSec1 {
  width: 100%;
  padding-top: 1rem;
  background-image: url("../../assets/img/s1-bg.svg");
  background-repeat: no-repeat;
}
//.jSec1-header {
//  font-weight: 700;
//}

.jSec1-tabs {
  display: flex;
  flex-flow: column nowrap;
  align-items: center;
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
  width: 1.25rem;
  height: 1.25rem;
  margin-left: 0.0625rem;
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
  padding-top: 3.875rem;
}
.jSec1-profile-text {
  order: 2;
}
.jSec1-profile-banner {
  order: 1;
}
.jSec1-profile-title {
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
}







@media screen and (min-width: $small) {
  #jSec1 {
    background-position: 100%;
  }
}

@media screen and (min-width: $medium) {
  #jSec1 {
    background-image: url("../../assets/img/s1-bg.svg"), url("../../assets/img/s1-bg-2.svg");
    background-repeat: no-repeat, no-repeat;
    background-position: 100% 50%, 0% 50%;
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

@media screen and (min-width: 1920px) {
  .jSec1-profile-banner-img {
    position: absolute;
    max-width: 920px;
  }
}
</style>
