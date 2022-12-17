<template>
  <section id="jReviews">
    <a id="reviews" class="ancor" name="reviews"/>
    <div class="section-content">
      <h2 class="jReviews-header jHeader">{{ $lang(lang, 'review-header') }}</h2>
      <div class="jReviews-wrapper">
        <div class="jReviews-types">
          <button class="jReviews-video jButton">{{ $lang(lang, 'review-video') }}</button>
          <button class="jReviews-text jButton" disabled>{{ $lang(lang, 'review-text') }}</button>
          <button class="jReviews-photo jButton" disabled>{{ $lang(lang, 'review-photo') }}</button>
        </div>
        <div class="jReviews-items-wrapper">
          <div class="jReviews-items">
            <div class="jReviews-item-watch">
              <div class="jReviews-item-player">
                <iframe
                    :src="prefix + items[activeItem].video"
                    allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    class="jReviews-item-iflame"
                    frameborder="0"
                    title="YouTube video player"
                />
              </div>
            </div>
            <div class="jReviews-item-thumbnails">
              <div
                  v-for="(item, key) in items"
                  :key="'jTmb' + key"
                  :style="`background-image: url(${item.thumbnail});`"
                  class="jReviews-item-thumbnail"
                  @click="activeItem = key"
              ></div>
            </div>
          </div>
        </div>
        <div class="jReviews-footer">
          <p class="jReviews-title">{{ $lang(lang, 'review-title') }}</p>
          <a class="jReviews-free jButton" href="javascript:void(0)">
            {{ $lang(lang, 'review-free') }}
          </a>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  computed: {
    lang() {
      return this.$root.$data.lang
    }
  },
  data() {
    return {
      activeItem: 0,
      prefix: 'https://www.youtube.com/embed/',
      items: [
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
      ]
    }
  }
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

.jReviews-item-iflame {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
}

.jReviews-item-thumbnails {
  display: flex;
  gap: 0.625rem;
  margin-top: 1.125rem;
}

.jReviews-item-thumbnail {
  flex: 0 1 33%;
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
  }
}


@media screen and (min-width: $medium) {
  #jReviews {
    padding-bottom: 3rem;
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
  }
  .jReviews-footer {
    padding-right: 2rem;
  }
}
</style>
