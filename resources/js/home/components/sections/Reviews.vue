<template>
  <section id="jReviews">
    <a id="reviews" class="ancor" name="reviews"/>
    <div class="section-content">
      <h2 class="jReviews-header jHeader">{{ $lang(lang, 'review-header') }}</h2>
      <div class="jReviews-wrapper">
        <div class="jReviews-types">
          <button
              class="jReviews-video jButton"
              @click="setMode('videos')"
          >{{ $lang(lang, 'review-video') }}
          </button>
          <button
              class="jReviews-photo jButton"
              @click="setMode('photos')"
          >{{ $lang(lang, 'review-photo') }}
          </button>
        </div>
        <div v-if="isDesktop" class="jReviews-items-wrapper">
          <div class="jReviews-items">
            <div class="jReviews-item-watch">
              <div
                  v-if="mode === 'videos'"
                  class="jReviews-item-player"
              >
                <iframe
                    :src="prefix + videos[activeVideo].video"
                    allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    class="jReviews-item-iflame"
                    frameborder="0"
                    title="YouTube video player"
                />
              </div>
              <div
                  v-if="mode === 'photos'"
                  class="jReviews-item-full"
              >
                <img
                    :src="photos[activePhoto].full"
                    class="jReviews-item-image"
                >
              </div>
            </div>
            <div
                ref="carouselWrap"
                class="jReviews-item-thumbnails"
            >
              <Hooper
                  ref="carousel"
                  :settings="hooperSettings"
              >
                <Slide
                    v-for="(item, key) in content"
                    :key="'jTmb' + key"
                >
                  <div
                      :style="`background-image: url(${item.thumbnail}); background-position: 0 -1rem;`"
                      class="jReviews-item-thumbnail"
                  />
                </Slide>
                <hooper-navigation slot="hooper-addons"></hooper-navigation>
              </Hooper>
            </div>
          </div>
        </div>
        <div v-if="!isDesktop" class="jReviews-items-wrapper">
          <div class="jReviews-items">
            <div class="jReviews-item-watch">
              <div
                  class="jReviews-item-player"
              >
                <Hooper
                    ref="carousel"
                    :settings="hooperSettings"
                >
                  <Slide
                      v-for="(item, key) in content"
                      :key="'jTmb' + key"
                  >
                    <iframe
                        v-if="mode === 'videos'"
                        :src="prefix + videos[key].videos"
                        allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        class="jReviews-item-iflame"
                        frameborder="0"
                        title="YouTube video player"
                    />
                    <img
                        v-if="mode === 'photos'"
                        :src="item.thumbnail"
                        class="jReviews-item-image"
                        alt="photos">
                  </Slide>
                  <hooper-navigation slot="hooper-addons"></hooper-navigation>
                </Hooper>
              </div>
            </div>
          </div>
        </div>
        <div class="jReviews-footer">
          <p class="jReviews-title">{{ $lang(lang, 'review-title') }}</p>
          <a
              class="jReviews-free jButton"
              href="/register"
          >
            {{ $lang(lang, 'review-free') }}
          </a>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import {Hooper, Navigation as HooperNavigation, Slide} from 'hooper'
import 'hooper/dist/hooper.css'

export default {
  components: {
    Hooper,
    Slide,
    HooperNavigation
  },
  computed: {
    lang() {
      return this.$root.$data.lang
    },
    isDesktop() {
      return this.$viewportSize.width >= 1260
    },
    content() {
      return this.mode === 'photos'
          ? this.photos
          : this.videos
    }
  },
  data() {
    return {
      activeVideo: 0,
      activePhoto: 0,
      mode: 'videos',
      prefix: 'https://www.youtube.com/embed/',
      videos: [
        {
          thumbnail: 'https://i3.ytimg.com/vi/LQtmJnljYyk/maxresdefault.jpg',
          video: 'LQtmJnljYyk'
        },
        {
          thumbnail: 'https://i3.ytimg.com/vi/LQtmJnljYyk/maxresdefault.jpg',
          video: 'LQtmJnljYyk'
        },
        {
          thumbnail: 'https://i3.ytimg.com/vi/LQtmJnljYyk/maxresdefault.jpg',
          video: 'LQtmJnljYyk'
        },
        {
          thumbnail: 'https://i3.ytimg.com/vi/LQtmJnljYyk/maxresdefault.jpg',
          video: 'LQtmJnljYyk'
        },
        {
          thumbnail: 'https://i3.ytimg.com/vi/LQtmJnljYyk/maxresdefault.jpg',
          video: 'LQtmJnljYyk'
        },
      ],
      photos: [
        {
          thumbnail: 'https://chudo-prirody.com/uploads/posts/2021-08/1628879740_153-p-foto-kotyat-prikolnie-161.jpg',
          full: 'https://placekitten.com/1024/576'
        },
        {
          thumbnail: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQeUE8cuMRSgVfaLre3jpUHoORbJxaXyZjsmuGURFp4F1W5eW9JLa-s233pH4UHXBHNso0&usqp=CAU',
          full: 'https://placekitten.com/1024/576'
        },
        {
          thumbnail: 'https://damion.club/uploads/posts/2022-01/1643042029_80-damion-club-p-samie-nyashnie-kotiki-83.jpg',
          full: 'https://placekitten.com/1024/576'
        },
        {
          thumbnail: 'https://img1.goodfon.ru/wallpaper/nbig/9/3e/kotenok-koshka-horoshenkiy-3793.jpg',
          full: 'https://placekitten.com/1024/576'
        },
        {
          thumbnail: 'https://img1.goodfon.ru/wallpaper/big/2/85/koshka-kot-kotenok-ryzhiy-yazyk.jpg',
          full: 'https://placekitten.com/1024/576'
        },
      ],
      resizeObserver: null,
      hooperSettings: {
        itemsToShow: 1,
        centerMode: true,
        trimWhiteSpace: true,
        autoPlay: true,
        playSpeed: 3000,
        breakpoints: {
          1260: {
            centerMode: false,
            itemsToShow: 2.8,
            vertical: true,
            trimWhiteSpace: true,
            autoPlay: true,
            playSpeed: 3000,
          }
        }
      }
    }
  },
  mounted() {
    this.resizeObserver = new ResizeObserver(() => {
      this.$refs.carousel.update()
    })
    this.resizeObserver.observe(this.$refs.carouselWrap)
  },
  methods: {
    setMode(mode) {
      this.mode = mode
      this.$refs.carousel.update()
    }
  },
}
</script>

<style lang="scss">
@import '../../assets/scss/app.variables.scss';

#jReviews {
  width: 100%;
  background: url("../../assets/img/reviews-bg.svg") no-repeat;
  background-position-x: 85%;
}

.jReviews-header {
  position: relative;
  margin-bottom: 3rem;

  &:after {
    content: '';
    display: block;
    width: 8.5625rem;
    position: absolute;
    bottom: -0.75rem;
    left: 50%;
    transform: translateX(-50%);
    border-bottom: 0.1875rem solid #42b1f4;
  }
}

.jReviews-types {
  .jButton {
    display: inline-block;
    margin-bottom: 1.25rem;
  }
}

.jButton {
  &.jReviews-video {
    background: #ffd86b;
    color: #303b4d;

    &:hover {
      background: #ffdc7a;
    }
  }
}

.jReviews-item-player {
  position: relative;
  padding-top: 56.25%;
  background: #000;
}

@media (max-width: $medium) {
  .jReviews-item-player {
    position: relative;
    padding-top: 0;
    background: #000;
  }
}

.jReviews-item-iflame {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
}

.jReviews-item-full {
}

.jReviews-item-image {
  width: 100%;
}

.jReviews-item-thumbnails {
  display: flex;
  gap: 0.625rem;
  margin-top: 1.125rem;

  .hooper {
    margin: 0 -5px;
    flex: 100% 1 1;
    height: auto;
  }
}

.jReviews-item-thumbnail {
  margin: 0 5px;
  background-size: cover;
  cursor: pointer;

  &:after {
    content: '';
    display: block;
    padding-bottom: 56.25%;
  }
}

.jReviews-title {
  margin-top: 1.75rem;
}

.jReviews-free {
  margin-top: 1.75rem;
}


@media screen and (min-width: $small) {
  .jReviews-types {
    display: flex;
    gap: 2rem;

    .jButton {
      display: block;
    }
  }
}


@media screen and (min-width: $medium) {
  #jReviews {
    padding-bottom: 15rem;
  }
  .jReviews-types {
    display: block;
  }
  .jReviews-wrapper {
    display: grid;
    grid-template-columns: 1fr 2fr;
  }
  .jReviews-items-wrapper {
    grid-column: 2;
    grid-row: 1/3;
  }
  .jReviews-items {
    display: flex;
    gap: 0.75rem;
    flex-flow: row nowrap;
  }
  .jReviews-item-watch {
    flex: 0 1 100%;
  }
  .jReviews-item-thumbnails {
    flex: 0 0 12rem;
    flex-flow: column nowrap;
    justify-content: space-between;
    margin: 0;
    padding: 1.5rem 0;

    .hooper {
      margin: -5px 0;
    }
  }
  .jReviews-item-thumbnail {
    margin: 5px 0;
  }
  .jReviews-footer {
    padding-right: 2rem;
  }

  .hooper-navigation.is-vertical .hooper-prev {
    top: -42px;
    bottom: auto;
    right: 72px;
    left: auto;
    transform: initial;
  }

  .hooper-navigation.is-vertical .hooper-next {
    right: 64px;
    top: auto;
    bottom: -49px;
    transform: initial;
  }
}

@media screen and (min-width: $large) {
  .hooper-navigation.is-vertical .hooper-prev {
    top: -84px;
    bottom: auto;
    right: 154px;
    left: auto;
    transform: scale(2);
  }

  .hooper-navigation.is-vertical .hooper-next {
    right: 142px;
    top: auto;
    bottom: -86px;
    transform: scale(2);
  }
}
</style>
